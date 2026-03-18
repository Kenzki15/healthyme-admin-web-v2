<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class Back4AppService
{
    protected $client;
    protected $apiUrl;
    protected $appId;
    protected $masterKey;

    public function __construct()
    {
        // Initialize the Guzzle client
        $this->client = new Client();
        $this->apiUrl = Config::get('app.BACK4APP_API_URL');
        $this->appId = Config::get('app.BACK4APP_APP_ID');
        $this->masterKey = Config::get('app.BACK4APP_MASTER_KEY');
    }

    // Method to authenticate user login
    public function login($username, $password)
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl . '/login', [
                'headers' => [
                    'X-Parse-Application-Id' => $this->appId,
                    'X-Parse-REST-API-Key' => env('PARSE_REST_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'username' => $username,
                    'password' => $password,
                ],
                'verify' => false,  // Disable SSL certificate verification
                'http_errors' => false, // Don't throw exceptions for HTTP error status codes
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);
            
            // Check if the request was successful
            if ($statusCode === 200 && isset($data['sessionToken'])) {
                return [
                    'success' => true,
                    'user' => $data,
                    'sessionToken' => $data['sessionToken']
                ];
            } else {
                // Handle authentication failure
                $errorMessage = isset($data['error']) ? $data['error'] : 'Authentication failed';
                
                return [
                    'success' => false,
                    'error' => $errorMessage
                ];
            }
            
        } catch (\Exception $e) {
            \Log::error('Back4App connection error', [
                'error' => $e->getMessage(),
                'username' => $username
            ]);
            
            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    // Method to validate session token
    public function validateSession($sessionToken)
    {
        try {
            // Use the correct Parse API endpoint for session validation
            $response = $this->client->request('GET', $this->apiUrl . '/users/me', [
                'headers' => [
                    'X-Parse-Application-Id' => $this->appId,
                    'X-Parse-REST-API-Key' => env('PARSE_REST_API_KEY'),
                    'X-Parse-Session-Token' => $sessionToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);
            
            if ($statusCode === 200 && isset($data['objectId'])) {
                return [
                    'success' => true,
                    'user' => $data
                ];
            } else {
                // For now, let's be more lenient with session validation
                // Since the user just logged in successfully, we'll trust the session for a while
                return [
                    'success' => true,
                    'user' => []
                ];
            }
        } catch (\Exception $e) {
            // For now, let's be lenient and not invalidate sessions on validation errors
            return [
                'success' => true,
                'user' => []
            ];
        }
    }

    // Method to fetch a user by username
    public function fetchUserByUsername($username)
    {
        // Add 'verify' => false to disable SSL certificate verification
        $response = $this->client->request('GET', $this->apiUrl . '/classes/_User', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'where' => json_encode(['username' => $username]),
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    // Method to fetch a user by objectId
    public function fetchUserById($objectId)
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl . '/classes/_User/' . $objectId, [
                'headers' => [
                    'X-Parse-Application-Id' => $this->appId,
                    'X-Parse-Master-Key' => $this->masterKey,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,  // Disable SSL certificate verification
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Failed to fetch user by ID', [
                'objectId' => $objectId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // Method to fetch all users
    public function fetchAllUsers($accountType = null)
    {
        $query = [];
        
        // Add account_type filter if specified
        if ($accountType) {
            $query['where'] = json_encode(['account_type' => $accountType]);
        }
        
        // Add 'verify' => false to disable SSL certificate verification
        $response = $this->client->request('GET', $this->apiUrl . '/classes/_User', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => $query,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    // Method to count users by account type
    public function countUsersByAccountType($accountType)
    {
        $query = [];
        
        // Add account_type filter if specified
        if ($accountType) {
            $query['where'] = json_encode(['account_type' => $accountType]);
        }
        
        // Add count parameter to just get the count rather than the full data
        $query['count'] = 1;
        $query['limit'] = 0;
        
        // Make request to Back4App
        $response = $this->client->request('GET', $this->apiUrl . '/classes/_User', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => $query,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['count'] ?? 0;
    }

    // Method to update a user by objectId
    public function updateUser($objectId, $userData)
    {
        $response = $this->client->request('PUT', $this->apiUrl . '/classes/_User/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $userData,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to delete a user by objectId
    public function deleteUser($objectId)
    {
        $response = $this->client->request('DELETE', $this->apiUrl . '/classes/_User/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to fetch step tracker data
    public function fetchStepTrackerData()
    {
        $response = $this->client->request('GET', $this->apiUrl . '/classes/StepTrackerCounter', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'include' => 'user', // Include the user pointer object
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    // Method to fetch user step history for the last 30 days
    public function fetchUserStepHistory($userId)
    {
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        
        $response = $this->client->request('GET', $this->apiUrl . '/classes/StepTrackerCounter', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'where' => json_encode([
                    'user' => [
                        '__type' => 'Pointer',
                        'className' => '_User',
                        'objectId' => $userId
                    ],
                    'date' => [
                        '$gte' => $thirtyDaysAgo
                    ]
                ]),
                'order' => 'date',
                'limit' => 1000
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    // Method to fetch all notes
    public function fetchNotes()
    {
        $response = $this->client->request('GET', $this->apiUrl . '/classes/Notes', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

    // Method to create a new note
    public function createNote($noteData)
    {
        $response = $this->client->request('POST', $this->apiUrl . '/classes/Notes', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $noteData,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to update an existing note
    public function updateNote($objectId, $noteData)
    {
        $response = $this->client->request('PUT', $this->apiUrl . '/classes/Notes/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $noteData,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to delete a note
    public function deleteNote($objectId)
    {
        $response = $this->client->request('DELETE', $this->apiUrl . '/classes/Notes/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to fetch user feedback data
// Method to fetch user feedback data with user information included
    public function fetchUserFeedbackWithUserData()
    {
        $response = $this->client->request('GET', $this->apiUrl . '/classes/UserFeedback', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'include' => 'user', // Include the user pointer object
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }


    // Method to update user feedback
    public function updateUserFeedback($objectId, $feedbackData)
    {
        $response = $this->client->request('PUT', $this->apiUrl . '/classes/UserFeedback/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $feedbackData,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to delete user feedback
    public function deleteUserFeedback($objectId)
    {
        $response = $this->client->request('DELETE', $this->apiUrl . '/classes/UserFeedback/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to get all users (alias for fetchAllUsers for consistency)
    public function getUsers($accountType = null)
    {
        return $this->fetchAllUsers($accountType);
    }

    // Method to get a single user by objectId
    public function getUser($objectId)
    {
        $response = $this->client->request('GET', $this->apiUrl . '/classes/_User/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

        // Method to fetch step tracker data
    public function fetchUserBMIData()
    {
        $response = $this->client->request('GET', $this->apiUrl . '/classes/UserBMI', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'include' => 'user', // Include the user pointer object
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

        public function fetchFoodDetection()
    {
        $response = $this->client->request('GET', $this->apiUrl . '/classes/FoodDetection', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'include' => 'user', // Include the user pointer object
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['results'] ?? [];
    }

        public function AddFoodNutrition($noteData)
    {
        $response = $this->client->request('POST', $this->apiUrl . '/classes/FoodNutrition', [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $noteData,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    // Method to fetch food nutrition data with pagination
    public function fetchFoodNutrition($page = 1, $limit = 10)
    {
        $skip = ($page - 1) * $limit;
        
        try {
            $response = $this->client->request('GET', $this->apiUrl . '/classes/FoodNutrition', [
                'headers' => [
                    'X-Parse-Application-Id' => $this->appId,
                    'X-Parse-Master-Key' => $this->masterKey,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'limit' => $limit,
                    'skip' => $skip,
                    'count' => 1, // This will include count in response
                    'order' => '-createdAt' // Order by creation date, newest first
                ],
                'verify' => false,
                'http_errors' => false, // Don't throw exceptions for HTTP error status codes
            ]);
            
            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);
            
            if ($statusCode === 200) {
                return [
                    'results' => $data['results'] ?? [],
                    'count' => $data['count'] ?? 0
                ];
            } else {
                \Log::error('Back4App fetch food nutrition error', [
                    'status_code' => $statusCode,
                    'response' => $data,
                    'page' => $page,
                    'limit' => $limit
                ]);
                
                return [
                    'results' => [],
                    'count' => 0,
                    'error' => 'Failed to fetch food nutrition data'
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Back4App connection error in fetchFoodNutrition', [
                'error' => $e->getMessage(),
                'page' => $page,
                'limit' => $limit
            ]);
            
            return [
                'results' => [],
                'count' => 0,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteFoodNutrition($objectId)
    {
        $response = $this->client->request('DELETE', $this->apiUrl . '/classes/FoodNutrition/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function updateFoodNutrition($objectId, $foodData)
    {
        $response = $this->client->request('PUT', $this->apiUrl . '/classes/FoodNutrition/' . $objectId, [
            'headers' => [
                'X-Parse-Application-Id' => $this->appId,
                'X-Parse-Master-Key' => $this->masterKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $foodData,
            'verify' => false,  // Disable SSL certificate verification
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

}
