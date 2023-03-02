<?php

namespace Modules\SocialMediaAuthentication\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_id',
        'provider_name'
    ];

    protected static function newFactory()
    {
        return \Modules\SocialMediaAuthentication\Database\factories\ProviderFactory::new();
    }
}
