<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\Product\MetaFeedService;
use Illuminate\Http\Response;

class MetaFeedController extends Controller
{
    public function __construct(private MetaFeedService $service) {}

    public function __invoke(): Response
    {
        $xml = $this->service->buildFeed();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
