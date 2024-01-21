<?php

namespace App\Providers;


use App\Repositories\AdminStatisticsRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ArticleRepository::class ,  ArticleRepository::class);
        $this->app->bind(AuthorRepository::class ,  AuthorRepository::class);
        $this->app->bind(AdminStatisticsRepository::class ,  AdminStatisticsRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
