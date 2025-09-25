<?php

namespace App\Providers;

use App\Http\Resources\PaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Response::macro('success', function ($message, $data = null, $status = 200) {
        //     $paginator = $data instanceof ResourceCollection && $data->resource instanceof LengthAwarePaginator
        //         ? $data->resource
        //         : ($data instanceof LengthAwarePaginator ? $data : null);

        //     $response = [
        //         'message' => $message,
        //         'status'  => $status,
        //         'data'    => $paginator?->items() ?? ($data ?? []),
        //     ];

        //     if ($paginator) {
        //         $response += (new PaginationResource($paginator))->resolve();
        //     }

        //     return response()->json($response, $status);
        // });


        // Response::macro('error', function (
        //     string $title,
        //     array $details = [],
        //     int $status = 500
        // ) {
        //     $defaultMessage = HttpResponse::$statusTexts[$status] ?? 'Unknown Status';

        //     $response = [
        //         'title'   => $defaultMessage,
        //         'status'    => $status,
        //         'detail'    => [$details['message'] ?? $title],
        //         'timestamp' => now()->toISOString(),
        //     ];

        //     if (!empty($details)) {
        //         $response['errors'] = $details;
        //     }

        //     return response()->json(["error" => $response], $status);
        // });
    }
}
