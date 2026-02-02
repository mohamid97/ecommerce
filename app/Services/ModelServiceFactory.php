<?php

namespace App\Services;


class ModelServiceFactory
{
    protected static array $map = [
        'user' => \App\Services\Admin\User\UserService::class,
        'lang' => \App\Services\Admin\Lang\LangService::class,
        'slider'=>\App\Services\Admin\Slider\SliderService::class,
        'category'=>\App\Services\Admin\Category\CategoryService::class,
        'about'=>\App\Services\Admin\About\AboutService::class,
        'contact'=>\App\Services\Admin\Contact\ContactService::class,
        'location'=>\App\Services\Admin\Location\LocationService::class,
        'maincontact'=>\App\Services\Admin\Maincontact\MaincontactService::class,
        'social'=>\App\Services\Admin\Social\SocialService::class,
        'permission'=>\App\Services\Admin\Permission\PermissionService::class,
        'role'=>\App\Services\Admin\Role\RoleService::class,
        'event'=>\App\Services\Admin\Event\EventService::class,
        'blog'=>\App\Services\Admin\Blog\BlogService::class,
        'client'=>\App\Services\Admin\Client\ClientService::class,
        'ourwork'=>\App\Services\Admin\Ourwork\OurworkService::class,
        'feedback'=> \App\Services\Admin\Feedback\FeedbackService::class,
        'achivement'=>\App\Services\Admin\Achivement\AchivementService::class,
        'service'=>\App\Services\Admin\Service\ServiceService::class,
        'product'=>\App\Services\Admin\Ecommerce\Product\ProductService::class,
        'branch'=>\App\Services\Admin\Branch\BranchService::class,
        'setting'=>\App\Services\Admin\Setting\SettingService::class,
        'metasetting'=>\App\Services\Admin\Metasetting\MetasettingService::class,
        'message'=>\App\Services\Admin\Message\MessageService::class,
        'ourteam'=>\App\Services\Admin\Ourteam\OurteamService::class,
        'mediaimage'=>\App\Services\Admin\Mediaimage\MediaimageService::class,
        'mediavideo'=>\App\Services\Admin\Mediavideo\MediavideoService::class,
        'brand'     => \App\Services\Admin\Brand\BrandService::class,
        'applicant'=> \App\Service\Admin\Applicant\ApplicantService::class,
        'faq'      =>  \App\Services\Admin\Faq\FaqService::class,
        'certificate'=> \App\Services\Admin\Certificate\CertificateService::class,
        'page'=> \App\Services\Admin\Page\PageService::class,
        'des'=> \App\Services\Admin\Des\DesService::class,
        'option'=> \App\Services\Admin\Ecommerce\Option\OptionService::class,
      
    ];

    public static function make(string $modelName)
    {
        $modelName = strtolower($modelName);
        $serviceClass = self::$map[$modelName] ?? null;

        if (!$serviceClass || !class_exists($serviceClass)) {
            throw new \Exception("Service not found for model: $modelName");
        }
        return app($serviceClass);
        
    }

    


    
}