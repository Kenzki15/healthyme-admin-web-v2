<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService;

class StepTrackerController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10); // Default 10 records per page
        $page = $request->input('page', 1);
        
        // Fetch all step tracker data and deduplicate by username+email, keeping the latest entry
        $results = collect($this->back4AppService->fetchStepTrackerData());
        
        // Sort by createdAt or updatedAt descending if available, so latest comes first
        $sorted = $results->sortByDesc(function ($item) {
            return isset($item['updatedAt']) ? strtotime($item['updatedAt']) : (isset($item['createdAt']) ? strtotime($item['createdAt']) : 0);
        })->values();
        
        $unique = $sorted->unique(function ($item) {
            $username = isset($item['user']['username']) ? $item['user']['username'] : ($item['username'] ?? ($item['name'] ?? 'Unknown'));
            $email = isset($item['user']['email']) ? $item['user']['email'] : ($item['email'] ?? '');
            return $username . '|' . $email;
        })->values();
        
        // Fetch BMI data and merge with step tracker data
        $bmiRecords = $this->back4AppService->fetchUserBMIData();
        $bmiByUser = collect($bmiRecords)
            ->sortByDesc('createdAt')
            ->groupBy(fn($bmi) => $bmi['user']['objectId'] ?? null)
            ->map(fn($group) => $group->first());
        
        // Fetch historical step data for each user (last 7 days)
        $unique = $unique->map(function ($item) use ($bmiByUser) {
            $userId = $item['user']['objectId'] ?? null;
            
            // Merge BMI data
            if ($userId && isset($bmiByUser[$userId])) {
                $bmi = $bmiByUser[$userId];
                $item['bmiValue'] = $bmi['bmiValue'] ?? null;
                $item['bmiCategory'] = $bmi['bmiCategory'] ?? null;
                $item['height'] = $bmi['height'] ?? null;
                $item['weight'] = $bmi['weight'] ?? null;
                $item['age'] = $bmi['age'] ?? null;
                $item['gender'] = $bmi['gender'] ?? null;
            }
            
            // Fetch historical step data for the user
            $weeklyHistory = [];
            $weeklyGoalsCompleted = 0;
            $totalWeeklySteps = 0;
            
            if ($userId) {
                $historyData = $this->back4AppService->fetchUserStepHistory($userId);
                
                // Process last 7 days
                $weeklyHistory = $this->processWeeklyHistory($historyData);
                
                // Calculate weekly statistics
                foreach ($weeklyHistory as $dayData) {
                    if ($dayData['steps'] > 0) {
                        $totalWeeklySteps += $dayData['steps'];
                        if ($dayData['goalCompleted']) {
                            $weeklyGoalsCompleted++;
                        }
                    }
                }
            }
            
            $item['weeklyHistory'] = $weeklyHistory;
            $item['weeklyGoalsCompleted'] = $weeklyGoalsCompleted;
            $item['totalWeeklySteps'] = $totalWeeklySteps;
            $item['weeklyAverage'] = count(array_filter($weeklyHistory, fn($day) => $day['steps'] > 0)) > 0 
                ? round($totalWeeklySteps / 7) 
                : ($item['weeklyAvg'] ?? 0);
            
            return $item;
        });
        
        $total = $unique->count();
        $paginated = $unique->forPage($page, $perPage)->values();
        
        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
            'from' => ($total > 0) ? (($page - 1) * $perPage + 1) : 0,
            'to' => min($page * $perPage, $total),
        ];
        
        return Inertia::render('StepTracker', [
            'stepTrackerData' => $paginated,
            'pagination' => $pagination
        ]);
    }

    /**
     * Process weekly history data for the last 7 days
     */
    private function processWeeklyHistory($historyData)
    {
        $weeklyHistory = [];
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Get the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayOfWeek = date('l', strtotime($date));
            $dayShort = substr($dayOfWeek, 0, 3);
            
            // Find data for this specific date
            $dayData = collect($historyData)->firstWhere('date', $date);
            
            $steps = 0;
            $goal = 10000; // default goal
            $goalCompleted = false;
            
            if ($dayData) {
                $steps = $dayData['stepsToday'] ?? $dayData['steps'] ?? $dayData['stepCount'] ?? 0;
                $goal = $dayData['dailyGoal'] ?? $dayData['goal'] ?? 10000;
                $goalCompleted = $steps >= $goal;
            }
            
            $weeklyHistory[] = [
                'date' => $date,
                'dayName' => $dayOfWeek,
                'dayShort' => $dayShort,
                'steps' => $steps,
                'goal' => $goal,
                'goalCompleted' => $goalCompleted,
                'progress' => $goal > 0 ? min(100, round(($steps / $goal) * 100)) : 0,
                'isToday' => $date === date('Y-m-d'),
                'hasData' => $dayData !== null
            ];
        }
        
        return $weeklyHistory;
    }
}