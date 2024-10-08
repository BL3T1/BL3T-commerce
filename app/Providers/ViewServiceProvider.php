<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.auth'], function ($view) {
            $home_categories = Category::orderBy('created_at', 'desc')
                -> paginate(5)
                -> take(5);

            $view->with('home_categories', $home_categories);
        });
    }
}
