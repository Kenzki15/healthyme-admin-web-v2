<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService;

class DashboardController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    public function index(Request $request)
    {
        $user = $request->session()->get('parse_user', []);

        try {
            // Get total user count for account_type 'user'
            $totalUsers = $this->back4AppService->countUsersByAccountType('user');

            // Fetch UserBMI data and group gender counts by age ranges
            $userBMIs = $this->back4AppService->fetchUserBMIData();
            $ageRanges = [
                '18-24' => ['min' => 18, 'max' => 24],
                '25-34' => ['min' => 25, 'max' => 34],
                '35-44' => ['min' => 35, 'max' => 44],
                '45-54' => ['min' => 45, 'max' => 54],
                '55+'   => ['min' => 55, 'max' => 200],
            ];
            $genderAgeCounts = [];
            foreach ($ageRanges as $range => $bounds) {
                $genderAgeCounts[$range] = [
                    'male' => 0,
                    'female' => 0,
                    'other' => 0
                ];
            }
            // Aggregate BMI categories
            $bmiCategoryCounts = [];
            foreach ($userBMIs as $bmi) {
                $gender = strtolower($bmi['gender'] ?? ($bmi['user']['gender'] ?? 'other'));
                $age = $bmi['age'] ?? ($bmi['user']['age'] ?? null);
                $bmiCategory = $bmi['bmiCategory'] ?? 'Unknown';
                
                // Count BMI categories
                if (!isset($bmiCategoryCounts[$bmiCategory])) {
                    $bmiCategoryCounts[$bmiCategory] = 0;
                }
                $bmiCategoryCounts[$bmiCategory]++;
                
                // Age range grouping
                if ($age !== null && is_numeric($age)) {
                    foreach ($ageRanges as $range => $bounds) {
                        if ($age >= $bounds['min'] && $age <= $bounds['max']) {
                            if (isset($genderAgeCounts[$range][$gender])) {
                                $genderAgeCounts[$range][$gender]++;
                            } else {
                                $genderAgeCounts[$range]['other']++;
                            }
                            break;
                        }
                    }
                }
            }

            // Fetch and aggregate FoodDetection data
            $foodDetections = $this->back4AppService->fetchFoodDetection();
            $foodCategoryCounts = [];
            foreach ($foodDetections as $fd) {
                $foodName = $fd['foodName'] ?? 'Unknown';
                if (!isset($foodCategoryCounts[$foodName])) {
                    $foodCategoryCounts[$foodName] = 0;
                }
                $foodCategoryCounts[$foodName]++;
            }

            // Sort by creation date (newest first) and get the latest 8 entries for recent activity
            usort($foodDetections, function($a, $b) {
                return strtotime($b['createdAt']) - strtotime($a['createdAt']);
            });
            $latestFoodLogs = array_slice($foodDetections, 0, 8);
            
            // Format the data for the frontend
            $latestFoodLogs = array_map(function($log) {
                return [
                    'id' => $log['objectId'] ?? uniqid(),
                    'foodName' => $log['foodName'] ?? 'Unknown',
                    'confidence' => round(($log['confidence'] ?? 0) * 100, 1), // Convert to percentage
                    'userName' => isset($log['user']) 
                        ? ($log['user']['username'] ?? $log['user']['name'] ?? 'Unknown User')
                        : 'Unknown User',
                    'user' => $log['user'] ?? null, // Include full user object with profileImage
                    'createdAt' => $log['createdAt'] ?? date('c')
                ];
            }, $latestFoodLogs);

            // Fetch food nutrition data and get total count for Nutrition Database
            $foodNutritionData = $this->back4AppService->fetchFoodNutrition(1, 1);
            $foodNutritionListCount = $foodNutritionData['count'] ?? 0;

            $reportData = [
                'totalUsers' => $totalUsers,
                'activeUsers' => $foodNutritionListCount, // Use nutrition database count instead
                'newUsersThisMonth' => 0, // Placeholder - implement as needed
                'averageSessionDuration' => '0m 0s', // Placeholder - implement as needed
                'totalMealsLogged' => count($foodDetections),
                'foodNutritionListCount' => $foodNutritionListCount,
                'genderCounts' => [
                    'male' => array_sum(array_column($genderAgeCounts, 'male')),
                    'female' => array_sum(array_column($genderAgeCounts, 'female')),
                    'other' => array_sum(array_column($genderAgeCounts, 'other')),
                ],
                'genderAgeCounts' => $genderAgeCounts,
                'bmiCategories' => [
                    'labels' => array_keys($bmiCategoryCounts),
                    'data' => array_values($bmiCategoryCounts)
                ],
                'popularFoodCategories' => [
                    'labels' => array_keys($foodCategoryCounts),
                    'data' => array_values($foodCategoryCounts)
                ],
                'latestFoodLogs' => $latestFoodLogs
            ];

            return Inertia::render('Dashboard', [
                'user' => $user,
                'reportData' => $reportData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching dashboard data', [
                'error' => $e->getMessage()
            ]);
            // Return default data if there's an error
            return Inertia::render('Dashboard', [
                'user' => $user,
                'reportData' => [
                    'totalUsers' => 0,
                    'activeUsers' => 0,
                    'newUsersThisMonth' => 0,
                    'averageSessionDuration' => '0m 0s',
                    'totalMealsLogged' => 0,
                    'foodNutritionListCount' => 0,
                    'genderCounts' => [
                        'male' => 0,
                        'female' => 0,
                        'other' => 0
                    ],
                    'latestFoodLogs' => []
                ]
            ]);
        }
    }
}