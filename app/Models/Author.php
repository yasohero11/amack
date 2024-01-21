<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class Author extends Authenticatable implements HasMedia
{
    use HasFactory , InteractsWithMedia, HasRoles;
    protected $fillable = [
        "name",
        "role",
        "address",
        "age",
        "country_id",
        "email",
        "password"
    ];


    public function country(){
        return $this->hasOne(Country::class, 'id','country_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('person-image')
            ->singleFile();

        $this->addMediaConversion('small')
            ->width(80);

    }
}
