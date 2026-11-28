<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipAndPerformanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Aggressive Static Asset & Browser Caching Headers
        if ($request->is('assets/*') || $request->is('css/*') || $request->is('js/*') || $request->is('fonts/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('ETag', md5($response->getContent()));
        } else {
            // HTML & Dynamic views: private, no-cache for fresh finance numbers
            $response->headers->set('Cache-Control', 'no-cache, private, must-revalidate');
        }

        // 2. GZip Compression Activation
        $acceptEncoding = $request->header('Accept-Encoding', '');
        if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
            $content = $response->getContent();
            // Compress only text, json, html, css, js above 1KB
            if (is_string($content) && strlen($content) > 1024) {
                $compressed = gzencode($content, 6);
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', strlen($compressed));
                }
            }
        }

        // 3. Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}

