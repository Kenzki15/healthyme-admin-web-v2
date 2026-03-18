<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService;

class UserController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 10); // Default 10 users per page
            $page = $request->input('page', 1);
            // Get users from Back4App with account_type filter set to "user"
            $users = $this->back4AppService->getUsers('user');
            // Fetch all BMI records
            $bmiRecords = $this->back4AppService->fetchUserBMIData();
            // Index BMI records by user objectId (latest by createdAt)
            $bmiByUser = collect($bmiRecords)
                ->sortByDesc('createdAt')
                ->groupBy(fn($bmi) => $bmi['user']['objectId'] ?? null)
                ->map(fn($group) => $group->first());
            // Merge BMI data into users
            $users = collect($users)->map(function ($user) use ($bmiByUser) {
                $bmi = $bmiByUser[$user['objectId']] ?? null;
                // Ensure profileImage is always present
                $user['profileImage'] = $user['profileImage'] ?? null;
                if ($bmi) {
                    $user['gender'] = $bmi['gender'] ?? null;
                    $user['height'] = $bmi['height'] ?? null;
                    $user['weight'] = $bmi['weight'] ?? null;
                    $user['bmiValue'] = $bmi['bmiValue'] ?? null;
                    $user['bmiCategory'] = $bmi['bmiCategory'] ?? null;
                    $user['age'] = $bmi['age'] ?? null;
                }
                return $user;
            });
            // Paginate users manually since users is a collection
            $total = $users->count();
            $paginated = $users->forPage($page, $perPage)->values();
            $pagination = [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'from' => ($total > 0) ? (($page - 1) * $perPage + 1) : 0,
                'to' => min($page * $perPage, $total),
            ];
            return Inertia::render('User', [
                'users' => $paginated,
                'pagination' => $pagination,
                'success' => session('success'),
                'error' => session('error')
            ]);
        } catch (\Exception $e) {
            return Inertia::render('User', [
                'users' => [],
                'pagination' => null,
                'error' => 'Failed to load users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        try {
            $user = $this->back4AppService->getUser($id);
            
            return Inertia::render('UserShow', [
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // Add other validation rules as needed
        ]);

        try {
            $updatedUser = $this->back4AppService->updateUser($id, $request->all());
            
            return redirect()->route('users.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        try {
            $this->back4AppService->deleteUser($id);
            
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user with BMI data.
     */
    public function view($id)
    {
        try {
            $user = $this->back4AppService->getUser($id);
            // Fetch all BMI records and filter for this user
            $bmiRecords = $this->back4AppService->fetchUserBMIData();
            $userBmi = collect($bmiRecords)->first(function ($bmi) use ($id) {
                return isset($bmi['user']['objectId']) && $bmi['user']['objectId'] === $id;
            });
            return Inertia::render('UserView', [
                'user' => $user,
                'bmi' => $userBmi
            ]);
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    /**
     * Send a push notification to a specific user.
     */
    public function sendNotification(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $user = $this->back4AppService->getUser($id);
            $deviceToken = $user['deviceToken'] ?? null;

            if (!$deviceToken) {
                return redirect()->back()->with('error', 'User device token not found');
            }

            $fields = [
                'app_id' => env('ONESIGNAL_APP_ID'),
                'include_player_ids' => [$deviceToken],
                'headings' => ['en' => $request->title],
                'contents' => ['en' => $request->message],
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if (isset($result['id'])) {
                return redirect()->back()->with('success', 'Notification sent successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to send notification');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }
}
