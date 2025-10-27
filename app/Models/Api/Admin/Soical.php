<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soical extends Model
{
    use HasFactory;
    protected $fillable = [
        
        'facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'tiktok', 'pinterest', 'snapchat', 'email', 'phone',
        'facebook_cta', 'twitter_cta', 'instagram_cta', 'youtube_cta', 'linkedin_cta', 'tiktok_cta', 'pinterest_cta' , 'snapchat_cta' , 'email_cta','phone_cta',
        'facebook_layout', 'twitter_layout', 'instagram_layout', 'youtube_layout', 'linkedin_layout', 'tiktok_layout', 'pinterest_layout' , 'snapchat_layout' , 'email_layout','phone_layout',
        'whatsapp' , 'whatsapp_cta','whatsapp_layout'
    ];
    
}