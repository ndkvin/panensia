<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Configuration;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
      'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
      Passport::tokensExpireIn(now()->addMonth(12));
      Passport::refreshTokensExpireIn(now()->addDays(30));
      Passport::personalAccessTokensExpireIn(now()->addDays(1));

      Gate::define('admin', function (User $user) {
        return $user->role == 'admin';
      });

      Gate::define('user', function (User $user) {
        return $user->role == 'user';
      });

      Gate::define('reseller', function (User $user) {
        return $user->role == 'reseller';
      });
    }
}
