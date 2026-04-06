<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\User;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Seed default admin (session) and customer (API) users. Idempotent by email.
     */
    public function run(): void
    {
        $defaultPassword = env('AUTH_SEED_PASSWORD', 'password');

        $admin = User::query()->updateOrCreate(
            ['email' => env('AUTH_SEED_ADMIN_EMAIL', 'admin@ravy.test')],
            [
                'name' => 'Admin',
                'password' => $defaultPassword,
                'phone' => null,
                'type' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['super-admin']);

        $customer = User::query()->updateOrCreate(
            ['email' => env('AUTH_SEED_CUSTOMER_EMAIL', 'customer@ravy.test')],
            [
                'name' => 'Customer',
                'password' => $defaultPassword,
                'phone' => null,
                'type' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $customer->syncRoles([]);
    }
}
