<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\Cart\CartStoreRequest;
use App\Services\Ecommerce\Cart\CartService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResponseTrait;

}