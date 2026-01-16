<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Quote;
use App\Policies\QuotePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Quote::class => QuotePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ Gates rôle
        Gate::define('admin', fn(User $user) => $user->role === 'admin');
        Gate::define('provider', fn(User $user) => $user->role === 'provider');
    }
}
