<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        "title",
        "description",
        'type_id',
        "featured",
        "long",
        "lat",
        "era",
        "location",
        "country_id",
        "author_id",
        "history_fact",
        "start_date",
        "source_links",
        "fact_index",
        "end_date",

    ] ;
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('article-image')
            ->singleFile();


    }

    public function getType(){
        return $this->hasOne(ArticleType::class, 'type_id');
    }

    public function author(){
        return $this->hasOne(Author::class, 'id' , "author_id");
    }


    public function country(){
        return $this->hasOne(Country::class, 'id','country_id');
    }

//    public function registerMediaConversions(Media $media = null): void
//    {
//        $this->addMediaConversion('thumb')
//            ->width(600)
//            ->height(800);
//    }

}
