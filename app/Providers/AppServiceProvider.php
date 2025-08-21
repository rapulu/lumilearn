<?php

namespace App\Providers;

use App\Models\Todo;
use App\Policies\TodoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Todo::class => TodoPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-todolist', [TodoPolicy::class, 'manageTodoList']);
    }

}
