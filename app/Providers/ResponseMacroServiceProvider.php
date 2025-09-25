<?php
namespace App\Providers;

use Closure;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Support\Str;

class ResponseMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /**
         * success(string $message = 'success', mixed $data = [], int $status = 200, array $headers = [], ?string $code = null)
         */
        Response::macro('success', function (
            string $message = 'success',
            mixed $data = [],
            int $status = 200,
            array $headers = [],
            ?string $code = null
        ) {
            $base = [
                'message' => $message,
                'status'  => $status,
            ];

            if (!is_null($code)) {
                $base['code'] = $code;
            }

            // Always send JSON content type for success
            $headers = array_merge([
                'Content-Type' => 'application/json; charset=utf-8',
            ], $headers);

            // 1) If it's a JsonResource, use the resource pipeline and attach the top-level keys
            if ($data instanceof JsonResource) {
                return $data
                    ->additional($base)
                    ->response()
                    ->setStatusCode($status)
                    ->withHeaders($headers);
            }

            // 2) Paginators -> standardized shape (data, links, meta)
            if ($data instanceof LengthAwarePaginator) {
                $payload = array_merge($base, self::formatLengthAwarePaginator($data));
                return response()->json($payload, $status, $headers);
            }

            if ($data instanceof Paginator) {
                $payload = array_merge($base, self::formatSimplePaginator($data));
                return response()->json($payload, $status, $headers);
            }

            if ($data instanceof CursorPaginator) {
                $payload = array_merge($base, self::formatCursorPaginator($data));
                return response()->json($payload, $status, $headers);
            }

            // 3) Array-like / Collection / JsonSerializable
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            } elseif ($data instanceof Collection) {
                $data = $data->all();
            } elseif ($data instanceof \JsonSerializable) {
                $data = $data->jsonSerialize();
            }

            // 4) Scalar or null -> wrap as data; associative arrays pass through as-is
            $payload = array_merge($base, [
                'data' => $data ?? [],
            ]);

            return response()->json($payload, $status, $headers);
        });

        /**
         * fail(
         *   string $detail,
         *   array|MessageBag $errors = [],
         *   int $status = 500,
         *   string $type = 'about:blank',
         *   ?string $title = null,
         *   array $headers = [],
         *   ?string $code = null
         * )
         *
         * RFC 7807 compliant problem+json with optional application-level "code".
         */
        Response::macro('error', function (
            string $detail,
            array|MessageBag $errors = [],
            int $status = 500,
            string $type = 'about:blank',
            ?string $title = null,
            array $headers = [],
            ?string $code = null
        ) {
            // Ensure failure statuses are 4xx/5xx; never allow 2xx here.
            if ($status < 400) {
                $status = 500;
            }

            if ($errors instanceof MessageBag) {
                $errors = $errors->toArray();
            }

            $problemTitle = $title
                ?? (HttpResponse::$statusTexts[$status] ?? 'Error');

            // Optional trace id from inbound header; generate if missing
            $traceId = request()->header('X-Request-Id') ?: (string) Str::uuid();

            $problem = [
                'type'      => $type,                          // e.g., 'about:blank' or a documentation URL
                'title'     => $problemTitle,                  // short, human-readable summary
                'status'    => $status,                        // HTTP status
                'detail'    => $detail,                        // human-readable explanation
                'instance'  => request()->fullUrl(),           // request URI
                'timestamp' => now()->toIso8601String(),
                'trace_id'  => $traceId,
                'errors'    => !empty($errors) ? $errors : [], // field-level errors
            ];

            if (!is_null($code)) {
                $problem['code'] = $code; // application-level code
            }

            // Include debug info only in debug mode
            $isDebug = config('app.debug', false);
            if ($isDebug && ($e = app('log')->getLogger()?->error ?? null)) {
                // No-op: we don't have exception context here; this is just a placeholder explanation.
                // If you forward exceptions here, you could attach: $problem['debug'] = ['exception' => ..., 'trace' => ...];
            }

            $headers = array_merge([
                'Content-Type' => 'application/problem+json; charset=utf-8',
                'X-Request-Id' => $traceId,
            ], $headers);

            return response()->json($problem, $status, $headers);
        });
    }

    /** @return array{data: array, links: array, meta: array} */
    private static function formatLengthAwarePaginator(LengthAwarePaginator $paginator): array
    {
        // Preserve query string in URLs
        $paginator->appends(request()->query());

        return [
            'data'  => $paginator->items(),
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
            'meta'  => [
                'current_page' => $paginator->currentPage(),
                'from'         => $paginator->firstItem(),
                'last_page'    => $paginator->lastPage(),
                'path'         => $paginator->path(),
                'per_page'     => $paginator->perPage(),
                'to'           => $paginator->lastItem(),
                'total'        => $paginator->total(),
                'count'        => $paginator->count(),
            ],
        ];
    }

    /** @return array{data: array, links: array, meta: array} */
    private static function formatSimplePaginator(Paginator $paginator): array
    {
        $paginator->appends(request()->query());

        return [
            'data'  => $paginator->items(),
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta'  => [
                'current_page' => $paginator->currentPage(),
                'path'         => $paginator->path(),
                'per_page'     => $paginator->perPage(),
                'has_more'     => $paginator->hasMorePages(),
            ],
        ];
    }

    /** @return array{data: array, links: array, meta: array} */
    private static function formatCursorPaginator(CursorPaginator $paginator): array
    {
        $paginator->appends(request()->query());

        return [
            'data'  => $paginator->items(),
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta'  => [
                'per_page'    => $paginator->perPage(),
                'next_cursor' => optional($paginator->nextCursor())->encode(),
                'prev_cursor' => optional($paginator->previousCursor())->encode(),
            ],
        ];
    }
}
