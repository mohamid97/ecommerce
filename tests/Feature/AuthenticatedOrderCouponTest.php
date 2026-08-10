<?php

namespace Tests\Feature;

use App\Models\Api\Ecommerce\Order;
use App\Models\User;
use App\Services\Ecommerce\Order\OrderService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class AuthenticatedOrderCouponTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('govs');
        Schema::create('govs', function (Blueprint $table): void {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_authenticated_checkout_passes_coupon_code_to_order_service(): void
    {
        $governmentId = \DB::table('govs')->insertGetId([
            'name_ar' => 'القاهرة',
            'name_en' => 'Cairo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = new User([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'customer@example.test',
        ]);
        $user->id = 42;

        Sanctum::actingAs($user);

        $order = new Order([
            'order_number' => 'ORD-000001',
            'status' => 'pending',
            'government_id' => $governmentId,
            'total_before_discount' => 200,
            'total_after_discount' => 180,
            'shipping_cost' => 70,
            'tax' => 0,
            'total' => 250,
            'coupon_code' => 'SAVE10',
            'discount' => 20,
            'discount_type' => 'coupon',
        ]);
        $order->id = 1;
        $order->setRelation('government', null);
        $order->setRelation('items', collect());

        $service = Mockery::mock(OrderService::class);
        $service->shouldReceive('createOrderFromCart')
            ->once()
            ->withArgs(function (User $actualUser, array $data) use ($user, $governmentId): bool {
                return $actualUser->id === $user->id
                    && $data['government_id'] === $governmentId
                    && $data['coupon_code'] === 'SAVE10'
                    && $data['use_points'] === true
                    && $data['points_to_use'] === 100;
            })
            ->andReturn($order);
        $this->app->instance(OrderService::class, $service);

        $response = $this->postJson('/api/front/v1/orders/store', [
            'government_id' => $governmentId,
            'shipment_address' => '10 Example Street, Cairo',
            'payment_method' => 'cash',
            'coupon_code' => 'SAVE10',
            'use_points' => true,
            'points_to_use' => 100,
        ], ['lang' => 'en']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.coupon_code', 'SAVE10');
    }
}
