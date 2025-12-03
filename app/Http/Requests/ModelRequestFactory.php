<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\Admin\About\AboutStoreRequest;
use App\Http\Requests\Api\Admin\About\AboutUpdateRequest;
use App\Http\Requests\Api\Admin\Achivement\AchivementStoreRequest;
use App\Http\Requests\Api\Admin\Achivement\AchivementUpdateRequest;
use App\Http\Requests\Api\Admin\Blog\BlogStoreRequest;
use App\Http\Requests\Api\Admin\Blog\BlogUpdateRequest;
use App\Http\Requests\Api\Admin\Branch\BranchStoreRequest;
use App\Http\Requests\Api\Admin\Branch\BranchUpdateRequest;
use App\Http\Requests\Api\Admin\Brand\BrandStoreRequest;
use App\Http\Requests\Api\Admin\Brand\BrandUpdateRequest;
use App\Http\Requests\Api\Admin\Category\CategoryStoreRequest;
use App\Http\Requests\Api\Admin\Category\CategoryUpdateRequest;
use App\Http\Requests\Api\Admin\Certificate\CertificateStoreRequest;
use App\Http\Requests\Api\Admin\Certificate\CertificateUpdateRequest;
use App\Http\Requests\Api\Admin\Client\ClientStoreRequest;
use App\Http\Requests\Api\Admin\Client\ClientUpdateRequest;
use App\Http\Requests\Api\Admin\Contact\ContactStoreRequest;
use App\Http\Requests\Api\Admin\Contact\ContactUpdateRequest;
use App\Http\Requests\Api\Admin\Coupon\CouponStoreRequest;
use App\Http\Requests\Api\Admin\Coupon\CouponUpdateRequest;
use App\Http\Requests\Api\Admin\Event\EventStoreRequest;
use App\Http\Requests\Api\Admin\Event\EventUpdateRequest;
use App\Http\Requests\Api\Admin\Faq\FaqStoreRequest;
use App\Http\Requests\Api\Admin\Faq\FaqUpdateRequest;
use App\Http\Requests\Api\Admin\Feedback\FeedbackStoreRequest;
use App\Http\Requests\Api\Admin\Feedback\FeedbackUpdateRequest;
use App\Http\Requests\Api\Admin\Location\LocationStoreRequest;
use App\Http\Requests\Api\Admin\Location\LocationUpdateRequest;
use App\Http\Requests\Api\Admin\LangStoreRequest;
use App\Http\Requests\Api\Admin\Ourwork\OurworkStoreRequest;
use App\Http\Requests\Api\Admin\Ourwork\OurworkUpdateRequest;
use App\Http\Requests\Api\Admin\Product\ProductStoreRequest;
use App\Http\Requests\Api\Admin\Product\ProductUpdateRequest;
use App\Http\Requests\Api\Admin\Role\RoleStoreRequest;
use App\Http\Requests\Api\Admin\Role\RoleUpdateRequest;
use App\Http\Requests\Api\Admin\Service\ServiceStoreRequest;
use App\Http\Requests\Api\Admin\Service\ServiceUpdateRequest;
use App\Http\Requests\Api\Admin\Setting\SettingUpdateStore;
use App\Http\Requests\Api\Admin\Slider\SliderStoreRequest;
use App\Http\Requests\Api\Admin\Slider\SliderUpdateRequest;
use App\Http\Requests\Api\Admin\Social\SocialStoreRequest;
use App\Http\Requests\Api\Admin\Users\UserStoreRequest;
use App\Http\Requests\Api\Admin\Users\UserUpdateRequest;
use App\Http\Requests\Api\Admin\Ourteam\OurteamStoreRequest;
use App\Http\Requests\Api\Admin\Ourteam\OurteamUpdateRequest;
use App\Http\Requests\Api\Admin\Mediaimage\MediaimageStoreRequest;
use App\Http\Requests\Api\Admin\Mediaimage\MediaimageUpdateRequest;
use App\Http\Requests\Api\Admin\Mediaimage\MediavideoStoreRequest;
use App\Http\Requests\Api\Admin\Mediaimage\MediavideoUpdateRequest;
use App\Models\Api\Admin\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class ModelRequestFactory
{
    public static function validate(string $model, string $action, Request $request): void
    {
       
        $map = [
            'user' => [
                'store' => UserStoreRequest::class,
                'update' => UserUpdateRequest::class,
            ],
            'lang'=>[
                'store' => LangStoreRequest::class,
                'update' => LangStoreRequest::class,
                
            ],
            'slider'=>[
                'store'=> SliderStoreRequest::class,
                'update'=> SliderUpdateRequest::class
            ],
            'category'=>[
                'store'=> CategoryStoreRequest::class,
                'update'=> CategoryUpdateRequest::class
            ],
            'about'=>[
                'store'=>AboutStoreRequest::class,
                'update'=>AboutUpdateRequest::class
            ],
            'contact'=>[
                'store'=>  ContactStoreRequest::class,
                'update'=> ContactUpdateRequest::class
            ],
            'location'=>[
                'store'=> LocationStoreRequest::class,
                'update'=> LocationUpdateRequest::class
            ],
            'social'=>[
                'store'=>SocialStoreRequest::class
            ],
            'role'=>[
                'store'=> RoleStoreRequest::class,
                'update'=> RoleUpdateRequest::class
            ],
            'event'=>[
                'store' =>EventStoreRequest::class,
                'update' => EventUpdateRequest::class, 
            ],
            'blog'=>[
                'store'=> BlogStoreRequest::class,
                'update'=> BlogUpdateRequest::class
            ],
            'client'=>[
                'store'=>ClientStoreRequest::class,
                'update'=>ClientUpdateRequest::class
            ],
            'ourwork'=>[
                'store'=> OurworkStoreRequest::class,
                'update'=>OurworkUpdateRequest::class
            ],
            'feedback'=>[
                'store'=>FeedbackStoreRequest::class,
                'update'=>FeedbackUpdateRequest::class
            ],
            'achivement'=>[
                'store'=>AchivementStoreRequest::class,
                'update'=>AchivementUpdateRequest::class
            ],
            'service'=>[
                'store'=>ServiceStoreRequest::class,
                'update'=>ServiceUpdateRequest::class
            ],
            'coupon'=>[
                'store'=>CouponStoreRequest::class,
                'update'=>CouponUpdateRequest::class,
            ],
            'product'=>[
                'store'=>ProductStoreRequest::class,
                'update'=>ProductUpdateRequest::class
            ],
            'branch'=>[
                'store'  =>  BranchStoreRequest::class,
                'update' => BranchUpdateRequest::class
            ],
            'setting'=>[
                'store'=> SettingUpdateStore::class
            ],
            'ourteam'=>[
                'store'=> OurteamStoreRequest::class,
                'update'=>OurteamUpdateRequest::class
            ],
            'mediaimage'=>[
                'store'=> MediaImageStoreRequest::class,
                'update'=> MediaImageUpdateRequest::class
            ],
            'mediavideo'=>[
                'store'=> MediaVideoStoreRequest::class,
                'update'=> MediaVideoUpdateRequest::class
            ],
            'brand'=>[
                'store'  => BrandStoreRequest::class,
                'update' => BrandUpdateRequest::class
            ],
            'faq'=>[
                'store'  => FaqStoreRequest::class,
                'update' => FaqUpdateRequest::class 
            ],
            'certificate'=>[
                'store' => CertificateStoreRequest::class,
                'update'=> CertificateUpdateRequest::class
            ],
            'page'=>[
                'store' => \App\Http\Requests\Api\Admin\Page\PageStoreRequest::class,
                'update' => \App\Http\Requests\Api\Admin\Page\PageUpdateRequest::class,
            ],
            'des'=>[
                'store' => \App\Http\Requests\Api\Admin\Des\DesStoreRequest::class,
                'update' => \App\Http\Requests\Api\Admin\Des\DesUpdateRequest::class,
            ],
            'option'=>[
                'store' => \App\Http\Requests\Api\Ecommerce\Option\OptionStoreRequest::class,
                'update' => \App\Http\Requests\Api\Ecommerce\Option\OptionUpdateRequest::class,
            ],


        ];

        

        $model = strtolower($model);
        if (!isset($map[$model][$action])) {
           
            return; 
        }
      

       

       
        $requestClass = app($map[$model][$action]);
    
        $validator = Validator::make($request->all(), $requestClass->rules());
        
        // if ($validator->fails()) {
            
        //     throw new ValidationException($validator);
        // }

        
        
    }
}