<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Person extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;


    protected $fillable = [
        "name",
        "role",
        "address",
        "age",
        "country_id"
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
