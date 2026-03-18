<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService;

class FoodNutritionController extends Controller
{
    protected $back4AppService;

    public function __construct(Back4AppService $back4AppService)
    {
        $this->back4AppService = $back4AppService;
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        
        $foodData = $this->back4AppService->fetchFoodNutrition($page, $perPage);
        
        $pagination = [
            'current_page' => (int) $page,
            'per_page' => (int) $perPage,
            'total' => $foodData['count'],
            'last_page' => ceil($foodData['count'] / $perPage),
            'from' => (($page - 1) * $perPage) + 1,
            'to' => min($page * $perPage, $foodData['count']),
        ];
        
        return Inertia::render('FoodNutrition', [
            'foods' => $foodData['results'],
            'pagination' => $pagination,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'calories' => 'required|string',
            'protein' => 'required|string',
            'carbs' => 'required|string',
            'fat' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        // Remove description if not set or null (make it 'undefined')
        if (!isset($validated['description']) || $validated['description'] === null) {
            unset($validated['description']);
        }

        $result = $this->back4AppService->AddFoodNutrition($validated);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->back4AppService->deleteFoodNutrition($id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 400);
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'calories' => 'required|string',
            'protein' => 'required|string',
            'carbs' => 'required|string',
            'fat' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        // Remove description if not set or null (make it 'undefined')
        if (!isset($validated['description']) || $validated['description'] === null) {
            unset($validated['description']);
        }

        $result = $this->back4AppService->updateFoodNutrition($id, $validated);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 400);
        }

        return response()->json(['success' => true, 'food' => $result]);
    }
}