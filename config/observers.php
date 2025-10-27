<?php

return [

    'admin' => [
        \App\Models\Api\Admin\Slider::class => \App\Observers\Admin\Slider\SliderObserver::class,
        \App\Models\Api\Admin\Category::class => \App\Observers\Admin\Category\CategoryObserver::class,
        \App\Models\Api\Admin\Event::class => \App\Observers\Admin\Event\EventObserver::class,
        \App\Models\Api\Admin\Ourwork::class => \App\Observers\Admin\Ourwork\OurworkObserver::class,
        \App\Models\Api\Admin\Feedback::class => \App\Observers\Admin\Feedback\FeedbackObserver::class,
        \App\Models\Api\Admin\Achivement::class => \App\Observers\Admin\Achivement\AcvhivementObserver::class,
        \App\Models\Api\Admin\Service::class => \App\Observers\Admin\Service\ServiceObserver::class,
        \App\Models\Api\Admin\Product::class => \App\Observers\Admin\Product\ProductObserver::class,
        \App\Models\Api\Admin\Blog::class => \App\Observers\Admin\Blog\BlogObserver::class,
        \App\Models\Api\Admin\Ourteam::class => \App\Observers\Admin\Ourteam\OurteamObserver::class,
        \App\Models\Api\Admin\Mediaimage::class => \App\Observers\Admin\Mediaimage\MediaimageObserver::class,
        \App\Models\Api\Admin\Brand::class      => \App\Observers\Admin\Brand\BrandObserver::class,
        \App\Models\Api\Admin\Page::class      => \App\Observers\Admin\Page\PageObserver::class,
        \App\Models\Api\Admin\Des::class      => \App\Observers\Admin\Des\DesObserver::class,
    ],

    
    'user' => [
        // still
       
    ],


];