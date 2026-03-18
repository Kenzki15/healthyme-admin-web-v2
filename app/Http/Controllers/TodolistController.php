<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService;

class TodolistController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    public function index()
    {
        try {
            // Fetch all notes from Back4App
            $notes = $this->back4AppService->fetchNotes();
            
            return Inertia::render('Todolist', [
                'user' => session('parse_user', []),
                'notes' => $notes
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching notes', [
                'error' => $e->getMessage()
            ]);
            
            return Inertia::render('Todolist', [
                'user' => session('parse_user', []),
                'notes' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|string',
                'priority' => 'required|string',
                'category' => 'required|string',
                'user' => 'required|string',
                'color' => 'required|string'
            ]);

            $noteData = [
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'priority' => $request->priority,
                'category' => $request->category,
                'user' => $request->user,
                'color' => $request->color
            ];

            // Create note in Back4App
            $result = $this->back4AppService->createNote($noteData);

            if (isset($result['objectId'])) {
                return response()->json([
                    'success' => true,
                    'note' => array_merge($noteData, ['objectId' => $result['objectId']]),
                    'message' => 'Note created successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create note'
                ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating note', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|string',
                'priority' => 'required|string',
                'category' => 'required|string',
                'user' => 'required|string',
                'color' => 'required|string'
            ]);

            $noteData = [
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'priority' => $request->priority,
                'category' => $request->category,
                'user' => $request->user,
                'color' => $request->color
            ];

            // Update note in Back4App
            $result = $this->back4AppService->updateNote($id, $noteData);

            return response()->json([
                'success' => true,
                'note' => array_merge($noteData, ['objectId' => $id]),
                'message' => 'Note updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating note', [
                'error' => $e->getMessage(),
                'note_id' => $id,
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Delete note from Back4App
            $result = $this->back4AppService->deleteNote($id);

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting note', [
                'error' => $e->getMessage(),
                'note_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting note: ' . $e->getMessage()
            ], 500);
        }
    }
}