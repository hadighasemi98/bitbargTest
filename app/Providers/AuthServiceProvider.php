<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensCan(scopes: [
            'create-tasks' => 'Create tasks',
            'read-tasks' => 'Read tasks',
            'update-tasks' => 'Update tasks',
            'delete-tasks' => 'Delete tasks',
        ]);

        Gate::define(ability: 'check_role', callback: function ($user, $role) {
            return $user->hasRole($role);
        });

    }
}
