<?php

namespace App\Providers;

//use App\Policies\AccountPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
//        dd("Permissoes");
        $this->registerAccountPolicies();
//        $this->registerPolicies();
    }

    public function registerAccountPolicies()
    {
        Gate::define('list_accounts', function ($user) {
            dd("police");
            return $user->hasPermission('list_accounts');
        });
        Gate::define('create_account', function ($user) {
            return $user->hasPermission('create_account');
        });
        Gate::define('show_account_info', function ($user) {
            return $user->hasPermission('show_account_info');
        });
        Gate::define('show_account', function ($user) {
            return $user->hasPermission('show_account');
        });
        Gate::define('update_account', function ($user) {
            return $user->hasPermission('update_account');
        });
        Gate::define('restore_account', function ($user) {
            return $user->hasPermission('restore_account');
        });
        Gate::define('delete_account', function ($user) {
            return $user->hasPermission('delete_account');
        });
        Gate::define('archive_account', function ($user) {
            return $user->hasPermission('archive_account');
        });
        Gate::define('upload_account_image', function ($user) {
            return $user->hasPermission('upload_account_image');
        });
    }
}
