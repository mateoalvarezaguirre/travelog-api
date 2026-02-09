<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CamelCaseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->replace(
            $this->transformKeys($request->all(), fn (string $key) => Str::snake($key))
        );

        $response = $next($request);

        if ($this->isJsonResponse($response)) {
            $data = json_decode($response->getContent(), true);

            if (is_array($data)) {
                $response->setContent(
                    json_encode($this->transformKeys($data, fn (string $key) => Str::camel($key)))
                );
            }
        }

        return $response;
    }

    private function transformKeys(array $data, Closure $transformer): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $newKey = is_string($key) ? $transformer($key) : $key;
            $result[$newKey] = is_array($value) ? $this->transformKeys($value, $transformer) : $value;
        }

        return $result;
    }

    private function isJsonResponse(Response $response): bool
    {
        return $response instanceof JsonResponse
            || str_contains($response->headers->get('Content-Type', ''), 'application/json');
    }
}
