<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ParseMultipartPutPATCH
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $method = strtoupper($request->method());
        $contentType = $request->headers->get('Content-Type', '');

        if (in_array($method, ['PUT', 'PATCH']) && str_contains($contentType, 'multipart/form-data')) {
            $rawContent = $request->getContent();
            if ($rawContent) {
                // Extract boundary
                preg_match('/boundary=(.*)$/', $contentType, $matches);
                if (isset($matches[1])) {
                    $boundary = trim($matches[1], '"');
                    $parts = preg_split('/-+' . preg_quote($boundary, '/') . '/', $rawContent);
                    array_pop($parts); // Remove final boundary indicator '--'

                    $inputs = [];
                    $files = [];

                    foreach ($parts as $part) {
                        if (empty($part) || trim($part) === '--') {
                            continue;
                        }

                        // Separate headers and body content
                        $subParts = explode("\r\n\r\n", $part, 2);
                        if (count($subParts) < 2) {
                            continue;
                        }

                        $headers = $subParts[0];
                        $body = substr($subParts[1], 0, -2); // strip trailing \r\n

                        // Parse Content-Disposition
                        if (preg_match('/name="([^"]+)"/', $headers, $matchName)) {
                            $name = $matchName[1];

                            // Check if it's a file
                            if (preg_match('/filename="([^"]*)"/', $headers, $matchFile)) {
                                $filename = $matchFile[1];
                                if ($filename !== '') {
                                    // Parse Content-Type
                                    $fileContentType = 'application/octet-stream';
                                    if (preg_match('/Content-Type:\s*([^\s;]+)/i', $headers, $matchType)) {
                                        $fileContentType = $matchType[1];
                                    }

                                    // Create temporary file
                                    $tempPath = tempnam(sys_get_temp_dir(), 'put_upload_');
                                    file_put_contents($tempPath, $body);

                                    $uploadedFile = new UploadedFile(
                                        $tempPath,
                                        $filename,
                                        $fileContentType,
                                        null,
                                        true // test mode to bypass normal uploaded check
                                    );

                                    if (str_ends_with($name, '[]')) {
                                        $arrayName = substr($name, 0, -2);
                                        $files[$arrayName][] = $uploadedFile;
                                    } else {
                                        $files[$name] = $uploadedFile;
                                    }
                                }
                            } else {
                                // Regular input
                                if (str_ends_with($name, '[]')) {
                                    $arrayName = substr($name, 0, -2);
                                    $inputs[$arrayName][] = $body;
                                } else {
                                    $inputs[$name] = $body;
                                }
                            }
                        }
                    }

                    $request->merge($inputs);
                    $request->files->add($files);
                }
            }
        }

        return $next($request);
    }
}
