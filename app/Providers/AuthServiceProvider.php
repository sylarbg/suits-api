<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('owns', function (User $authUser, User $citizen, Lawyer $lawyer) {
                if ($authUser->isLawyer() && $authUser->is($lawyer)) {
                    return true;
                }

                if (!$authUser->isLawyer() && $authUser->is($citizen)) {
                    return true;
                }

                return false;
        });

        Gate::define('reschedule', function (User $user, Appointment $appointment, Lawyer $lawyer) {
            if (!$appointment->isOwnedBy($lawyer, 'lawyer_id')) {
                return false;
            }

            if ($appointment->isOwnedBy($user) && $appointment->status == Appointment::REJECTED_STATUS) {
                return true;
            }
            return false;
        });

        Gate::define('delete', function (User $user, Appointment $appointment, Lawyer $lawyer) {
            if (!$appointment->isOwnedBy($lawyer, 'lawyer_id')) {
                return false;
            }

            return $lawyer->isOwnedBy($user,'id');
        });

        Gate::define('search-citizen', function (User $user) {
            return $user->isLawyer();
        });

        Gate::define('update', function (User $user, Appointment $appointment, Lawyer $lawyer) {

            if (!$appointment->isOwnedBy($lawyer, 'lawyer_id')) {
                return false;
            }

            return $lawyer->isOwnedBy($user,'id');
        });
    }
}
