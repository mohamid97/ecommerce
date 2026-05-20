<?php

namespace App\Services\Admin\Ecommerce\Statistics;

use App\Models\Api\Admin\Product;
use App\Models\Api\Admin\Expense;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Ecommerce\OrderItem;
use App\Models\Api\Ecommerce\ProductVariant;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeStatisticsService
{
    private const CACHE_TTL_MINUTES = 5;

    private const ORDER_STATUSES = [
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
        'refunded',
    ];

    public function overview(string $type): array
    {
        $key = 'admin_stats:overview:' . $type;

        return Cache::remember($key, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($type) {
            return match ($type) {
                'daily' => $this->overviewDaily(),
                'weekly' => $this->overviewWeekly(),
                'monthly' => $this->overviewMonthly(),
                'annually' => $this->overviewAnnually(),
            };
        });
    }

    public function recentOrders(int $limit = 10): array
    {
        $key = 'admin_stats:recent_orders:' . $limit;

        return Cache::remember($key, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($limit) {
            return Order::query()
                ->with('user')
                ->withCount('items')
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function (Order $order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer' => [
                            'id' => $order->user_id,
                            'name' => $this->customerName($order),
                            'email' => $order->user?->email ?? $order->guest_email,
                            'phone' => $order->phone ?? $order->user?->phone,
                            'type' => $order->user_id ? 'user' : 'guest',
                        ],
                        'status' => $order->status,
                        'payment_status' => $order->payment_status ?? 'unpaid',
                        'payment_method' => $order->payment_method,
                        'total' => (float) ($order->total_after_discount ?? $order->total),
                        'items_count' => (int) ($order->items_count ?? 0),
                        'created_at' => $order->created_at?->format('Y-m-d H:i:s'),
                    ];
                })
                ->values()
                ->all();
        });
    }

    public function orderStatusPercentages(?string $month = null): array
    {
        $key = 'admin_stats:order_status_percentages:' . ($month ?: 'all');

        return Cache::remember($key, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($month) {
            $baseQuery = Order::query();

            if (!empty($month)) {
                $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $end = $start->copy()->endOfMonth();
                $baseQuery->whereBetween('created_at', [$start, $end]);
            }

            $totalOrders = (int) (clone $baseQuery)->count();

            $counts = (clone $baseQuery)
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $knownStatuses = collect(self::ORDER_STATUSES)->map(function (string $status) use ($counts, $totalOrders) {
                $count = (int) ($counts[$status] ?? 0);

                return [
                    'status' => $status,
                    'count' => $count,
                    'percentage' => $totalOrders > 0 ? round(($count / $totalOrders) * 100, 2) : 0,
                ];
            });

            $extraStatuses = $counts
                ->keys()
                ->diff(self::ORDER_STATUSES)
                ->values()
                ->map(function (string $status) use ($counts, $totalOrders) {
                    $count = (int) ($counts[$status] ?? 0);

                    return [
                        'status' => $status,
                        'count' => $count,
                        'percentage' => $totalOrders > 0 ? round(($count / $totalOrders) * 100, 2) : 0,
                    ];
                });

            return $knownStatuses->concat($extraStatuses)->values()->all();
        });
    }


public function bestSelling(int $perPage = 5, ?string $month = null , int $page = 1): LengthAwarePaginator
{
    // no Cache here because LengthAwarePaginator isn't serializable cleanly
    $baseQuery = OrderItem::query()
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.payment_status', 'paid')
        ->whereNotIn('orders.status', ['cancelled', 'refunded']);

    if (!empty($month)) {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $baseQuery->whereBetween('orders.created_at', [$start, $end]);
    }

    $productRows = (clone $baseQuery)
        ->whereNotNull('order_items.product_id')
        ->whereNull('order_items.variant_id')
        ->whereNull('order_items.bundel_id')
        ->select(
            'order_items.product_id',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total_price_after_discount) as total_sales')
        )
        ->groupBy('order_items.product_id')
        ->orderByDesc('total_quantity')
        ->get();

    $variantRows = (clone $baseQuery)
        ->whereNotNull('order_items.variant_id')
        ->select(
            'order_items.variant_id',
            'order_items.product_id',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total_price_after_discount) as total_sales')
        )
        ->groupBy('order_items.variant_id', 'order_items.product_id')
        ->orderByDesc('total_quantity')
        ->get();

    $bundelRows = (clone $baseQuery)
        ->whereNotNull('order_items.bundel_id')
        ->select(
            'order_items.bundel_id',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total_price_after_discount) as total_sales')
        )
        ->groupBy('order_items.bundel_id')
        ->orderByDesc('total_quantity')
        ->get();

    $products = Product::query()
        ->whereIn('id', $productRows->pluck('product_id')->filter()->unique()->values())
        ->get()->keyBy('id');

    $variants = ProductVariant::query()
        ->with('product')
        ->whereIn('id', $variantRows->pluck('variant_id')->filter()->unique()->values())
        ->get()->keyBy('id');

    $bundels = Bundel::query()
        ->whereIn('id', $bundelRows->pluck('bundel_id')->filter()->unique()->values())
        ->get()->keyBy('id');

    $mappedProducts = $productRows->map(function ($row) use ($products) {
        $product = $products->get($row->product_id);
        return [
            'id'           => (int) $row->product_id,
            'type'         => 'product',
            'title'        => $this->translatedTitle($product, 'Product #' . $row->product_id),
            'image'        => $product?->product_image,
            'sales_number' => (int) $row->total_quantity,
            'revenue'      => round((float) $row->total_sales, 2),
        ];
    });

    $mappedVariants = $variantRows->map(function ($row) use ($variants) {
        $variant = $variants->get($row->variant_id);
        $product = $variant?->product;
        return [
            'id'            => (int) $row->variant_id,
            'type'          => 'variant',
            'title'         => $this->translatedTitle($variant, 'Variant #' . $row->variant_id),
            'product_title' => $this->translatedTitle($product, null),
            'image'         => $product?->product_image,
            'sales_number'  => (int) $row->total_quantity,
            'revenue'       => round((float) $row->total_sales, 2),
        ];
    });

    $mappedBundels = $bundelRows->map(function ($row) use ($bundels) {
        $bundel = $bundels->get($row->bundel_id);
        return [
            'id'           => (int) $row->bundel_id,
            'type'         => 'bundle',
            'title'        => $this->translatedTitle($bundel, 'Bundel #' . $row->bundel_id),
            'image'        => $bundel?->bundle_image,
            'sales_number' => (int) $row->total_quantity,
            'revenue'      => round((float) $row->total_sales, 2),
        ];
    });

    $merged = $mappedProducts
        ->concat($mappedVariants)
        ->concat($mappedBundels)
        ->sortByDesc('sales_number')
        ->values();

    // ── wrap into LengthAwarePaginator so successPaginated works ─────────────
    return new LengthAwarePaginator(
        items: $merged->forPage($page, $perPage)->values(),
        total: $merged->count(),
        perPage: $perPage,
        currentPage: $page,
        options: ['path' => request()->url(), 'query' => request()->query()]
    );
}
    private function overviewDaily(): array
    {
        $now = now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $rows = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as period, COUNT(*) as orders_count, COALESCE(SUM(total), 0) as revenue_sum')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        return $this->buildDailySeries($start, $end, $rows);
    }

    private function overviewWeekly(): array
    {
        $now = now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $rows = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(created_at, '%x-W%v') as period, COUNT(*) as orders_count, COALESCE(SUM(total), 0) as revenue_sum")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        return $this->buildWeeklySeriesForCurrentMonth($start, $end, $rows);
    }

    private function overviewMonthly(): array
    {
        $year = now()->year;

        $ordersRows = Order::query()
            ->whereYear('created_at', $year)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as orders_count, COALESCE(SUM(total), 0) as revenue_sum")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $costRows = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('order_item_batches', 'order_items.id', '=', 'order_item_batches.order_item_id')
            ->whereYear('orders.created_at', $year)
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as period")
            ->selectRaw('COALESCE(SUM(order_item_batches.quantity * COALESCE(order_item_batches.cost_price, 0)), 0) as cost_sum')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $expenseRows = Expense::query()
            ->whereYear('created_at', $year)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COALESCE(SUM(amount), 0) as expense_sum")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        return $this->buildMonthlySeries($year, $ordersRows, $costRows, $expenseRows);
    }

    private function overviewAnnually(): array
    {
        $years = Order::query()
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->values();

        if ($years->isEmpty()) {
            return [];
        }

        $ordersRows = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y') as period, COUNT(*) as orders_count, COALESCE(SUM(total), 0) as revenue_sum")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $costRows = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('order_item_batches', 'order_items.id', '=', 'order_item_batches.order_item_id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y') as period")
            ->selectRaw('COALESCE(SUM(order_item_batches.quantity * COALESCE(order_item_batches.cost_price, 0)), 0) as cost_sum')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $expenseRows = Expense::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y') as period, COALESCE(SUM(amount), 0) as expense_sum")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        return $this->buildAnnualSeries($years, $ordersRows, $costRows, $expenseRows);
    }

    private function buildDailySeries(Carbon $start, Carbon $end, Collection $rows): array
    {
        $series = [];

        foreach (CarbonPeriod::create($start, $end) as $day) {
            $period = $day->format('Y-m-d');
            $row = $rows->get($period);

            $series[] = [
                'period' => $period,
                'orders' => (int) ($row->orders_count ?? 0),
                'revenue' => round((float) ($row->revenue_sum ?? 0), 2),
            ];
        }

        return $series;
    }

    private function buildWeeklySeriesForCurrentMonth(Carbon $start, Carbon $end, Collection $rows): array
    {
        $weekKeys = [];

        foreach (CarbonPeriod::create($start, $end) as $day) {
            $key = $day->format('o') . '-W' . $day->format('W');
            if (!in_array($key, $weekKeys, true)) {
                $weekKeys[] = $key;
            }
        }

        $series = [];

        foreach ($weekKeys as $period) {
            $row = $rows->get($period);
            $series[] = [
                'period' => $period,
                'orders' => (int) ($row->orders_count ?? 0),
                'revenue' => round((float) ($row->revenue_sum ?? 0), 2),
            ];
        }

        return $series;
    }

    private function buildMonthlySeries(int $year, Collection $ordersRows, Collection $costRows, Collection $expenseRows): array
    {
        $series = [];

        for ($month = 1; $month <= 12; $month++) {
            $period = sprintf('%04d-%02d', $year, $month);
            $orderRow = $ordersRows->get($period);
            $revenue = (float) ($orderRow->revenue_sum ?? 0);
            $cost = (float) ($costRows->get($period)->cost_sum ?? 0);
            $expense = (float) ($expenseRows->get($period)->expense_sum ?? 0);

            $series[] = [
                'period' => $period,
                'orders' => (int) ($orderRow->orders_count ?? 0),
                'revenue' => round($revenue, 2),
                'profit' => round($revenue - $cost - $expense, 2),
            ];
        }

        return $series;
    }

    private function buildAnnualSeries(Collection $years, Collection $ordersRows, Collection $costRows, Collection $expenseRows): array
    {
        $series = [];

        foreach ($years as $year) {
            $period = (string) $year;
            $orderRow = $ordersRows->get($period);
            $revenue = (float) ($orderRow->revenue_sum ?? 0);
            $cost = (float) ($costRows->get($period)->cost_sum ?? 0);
            $expense = (float) ($expenseRows->get($period)->expense_sum ?? 0);

            $series[] = [
                'period' => $period,
                'orders' => (int) ($orderRow->orders_count ?? 0),
                'revenue' => round($revenue, 2),
                'profit' => round($revenue - $cost - $expense, 2),
            ];
        }

        return $series;
    }

    private function customerName(Order $order): ?string
    {
        if ($order->user) {
            $fullName = trim(($order->user->first_name ?? '') . ' ' . ($order->user->last_name ?? ''));

            return $order->user->name ?: ($fullName ?: null);
        }

        return $order->guest_name;
    }

    private function translatedTitle($model, ?string $fallback = null): ?string
    {
        if (!$model) {
            return $fallback;
        }

        if (method_exists($model, 'translate')) {
            $title = $model->translate(app()->getLocale())?->title
                ?? $model->translate('en')?->title
                ?? $model->translate('ar')?->title;

            if (!empty($title)) {
                return $title;
            }
        }

        return $model->title ?? $fallback;
    }
}
