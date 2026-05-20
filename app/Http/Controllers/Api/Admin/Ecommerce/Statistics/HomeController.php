<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Statistics\BestSellingStatisticsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Statistics\OrderStatusPercentagesRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Statistics\OverviewStatisticsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Statistics\RecentOrdersStatisticsRequest;
use App\Services\Admin\Ecommerce\Statistics\HomeStatisticsService;
use App\Traits\ResponseTrait;

class HomeController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected HomeStatisticsService $homeStatisticsService
    ) {}

    public function overview(OverviewStatisticsRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->homeStatisticsService->overview($data['type']);

            return $this->success($result, 'Overview ' . $data['type'] . ' statistics');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function recentOrders(RecentOrdersStatisticsRequest $request)
    {
        try {
            $data = $request->validated();
            $limit = (int) ($data['limit'] ?? 10);

            $result = $this->homeStatisticsService->recentOrders($limit);

            return $this->success($result, __('main.list_successfully'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function orderStatusPercentages(OrderStatusPercentagesRequest $request)
    {
        try {
            $data = $request->validated();
            $month = $data['month'] ?? null;

            $result = $this->homeStatisticsService->orderStatusPercentages($month);

            return $this->success(
                $result,
                $month ? ('Order status percentages statistics for ' . $month) : 'Order status percentages statistics'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function bestSelling(BestSellingStatisticsRequest $request)
    {
        try {
            $data = $request->validated();
            $month = $data['month'] ?? null;
            $limit = (int) ($data['limit'] ?? 5);
            $page = (int) ($data['page'] ?? 1);

            $result = $this->homeStatisticsService->bestSelling($limit, $month , $page);
            return $this->successPaginated(
                paginator:          $result,
                resourceCollection: $result->items(),
                message:            'Best selling items'
            );


        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
