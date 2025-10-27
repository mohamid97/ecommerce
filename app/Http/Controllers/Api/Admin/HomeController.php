<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Api\Admin\{Category,Slider,Blog, Product, Service , Mediaimage , Mediavideo , Ourwork , Client };
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Traits\ResponseTrait;
class HomeController extends Controller
{
    use ResponseTrait;
    
    public function index(){
            $data = Cache::remember('dashboard_counts', 60 * 24, function () {
            return [
                "slider"    => ["value" => Slider::count()],
                "product"   => ["value" => Product::count()],
                "user"      => ["value" => User::count()],
                "category"  => ["value" => Category::count()],
                "blog"      => ["value" => Blog::count()],
                'mediaimage'=> ["value" => Mediaimage::count()],
                "mediavideo"=> ["value" => Mediavideo::count()],
                "ourwork"   => ["value" => Ourwork::count()],
                "service"   => ["value" => Service::count()],
                'client'  => ["value" => Client::count()]
            ];
        });

        return $this->success($data , __('main.dashboard_stat'));
        
    }
}