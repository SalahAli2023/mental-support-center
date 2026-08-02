<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force JSON response for API routes
        // This ensures Laravel returns JSON responses for validation errors
        $request->headers->set('Accept', 'application/json');
        
        // Handle JSON request body parsing
        // Laravel should parse JSON automatically, but we ensure it works
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            // Check if request has JSON content type
            $contentType = $request->header('Content-Type', '');
            $hasJsonContentType = $contentType && (
                str_contains(strtolower($contentType), 'application/json') ||
                str_contains(strtolower($contentType), 'text/json')
            );
            
            if ($hasJsonContentType) {
                // Try to get JSON data from request
                // First, try Laravel's built-in method
                $jsonData = null;
                
                try {
                    // Check if Laravel already parsed the JSON
                    $allData = $request->all();
                    if (!empty($allData)) {
                        $jsonData = $allData;
                    }
                } catch (\Exception $e) {
                    // Continue to manual parsing
                }
                
                // If Laravel didn't parse it, do it manually
                if (empty($jsonData)) {
                    // Read raw content
                    $rawContent = file_get_contents('php://input');
                    
                    if (!empty($rawContent)) {
                        $decoded = json_decode($rawContent, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $jsonData = $decoded;
                            
                            // Merge JSON data into request
                            // This ensures the data is available for validation
                            $request->merge($jsonData);
                            
                            // Also set it in the request parameter bag
                            $request->request->add($jsonData);
                        }
                    }
                }
            }
        }
        
        return $next($request);
    }
}

