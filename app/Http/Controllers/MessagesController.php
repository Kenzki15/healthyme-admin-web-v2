<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService;

class MessagesController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10); // Default 10 messages per page
        $page = $request->input('page', 1);
        // Fetch all messages with user data
        $results = collect($this->back4AppService->fetchUserFeedbackWithUserData());
        $total = $results->count();
        $paginated = $results->forPage($page, $perPage)->values();
        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
            'from' => ($total > 0) ? (($page - 1) * $perPage + 1) : 0,
            'to' => min($page * $perPage, $total),
        ];
        return Inertia::render('Messages', [
            'messages' => $paginated,
            'pagination' => $pagination
        ]);
    }

    public function markAsRead($id)
    {
        $result = $this->back4AppService->updateUserFeedback($id, ['status' => 'Read']);
        return response()->json($result);
    }

    public function destroy($id)
    {
        try {
            $result = $this->back4AppService->deleteUserFeedback($id);
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message: ' . $e->getMessage()
            ], 500);
        }
    }

}
