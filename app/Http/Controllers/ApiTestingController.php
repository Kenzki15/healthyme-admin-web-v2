<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Back4AppService; // <-- Add this import

class ApiTestingController extends Controller
{
    /**
     * Display the API testing page.
     */
    public function index()
    {
        return Inertia::render('ApiTesting');
    }

    /**
     * Handle API testing requests.
     */
    public function handleRequest(Request $request)
    {
        // Here you can handle the API request logic
        // For example, you might want to call an external API or process the request data

        return response()->json(['message' => 'API request handled successfully']);
    }

    /**
     * Handle image prediction requests.
     */
    public function predict(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
    ]);

    $image = $request->file('image');

    try {
        // Define prediction URL
        $predictionUrl = 'https://healthymeapp-prediction.onrender.com/predict';

        // Try with 'file' field first
        $response = \Illuminate\Support\Facades\Http::timeout(200)->retry(1, 1000)
            ->attach(
                'file',
                fopen($image->getRealPath(), 'r'),
                $image->getClientOriginalName()
            )
            ->post($predictionUrl);

        // If it fails, try with 'image' field
        if (!$response->successful()) {
            $response = \Illuminate\Support\Facades\Http::timeout(200)->retry(1, 1000)
                ->attach(
                    'image',
                    fopen($image->getRealPath(), 'r'),
                    $image->getClientOriginalName()
                )
                ->post($predictionUrl);
        }

        if ($response->successful()) {
            $result = $response->json('result') ?? $response->json();

            // --- Nutrient logic start ---
            $nutrients = [];

            if (is_array($result) && isset($result['results'])) {
                $foodService = new \App\Services\Back4AppService();
                $foodNutrition = collect($foodService->fetchFoodNutrition());

                foreach ($result['results'] as $idx => $item) {
                    $className = $item['class_name'] ?? null;

                    if ($className) {
                        $nutrition = $foodNutrition->first(function ($n) use ($className) {
                            return isset($n['name']) &&
                                strtolower(trim($n['name'])) === strtolower(trim($className));
                        });

                        if ($nutrition) {
                            $nutrients[$idx] = [
                                'calories' => $nutrition['calories'] ?? null,
                                'protein'  => $nutrition['protein'] ?? null,
                                'fat'      => $nutrition['fat'] ?? null,
                                'carbs'    => $nutrition['carbs'] ?? null,
                            ];
                        }
                    }
                }
            }
            // --- Nutrient logic end ---

            return response()->json([
                'result'       => $result,
                'nutrients'    => $nutrients,
                'raw_response' => $response->json(),
            ]);
        } else {
            return response()->json([
                'result'       => null,
                'message'      => $response->json('message') ?? 'Prediction API error.',
                'raw_response' => $response->json(),
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'result'  => null,
            'message' => 'Prediction failed: ' . $e->getMessage(),
        ], 500);
    }
}
}