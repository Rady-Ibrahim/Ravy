<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Database\Seeders\RolePermissionSeeder;
use Modules\Auth\Mail\OtpCodeMail;
use Modules\Auth\Models\User;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_customer_cannot_login(): void
    {
        $password = 'password123';
        User::factory()->create([
            'email' => 'customer@example.com',
            'password' => $password,
            'type' => 'customer',
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'customer@example.com',
            'password' => $password,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_sends_verification_otp_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Customer One',
            'email' => 'newcustomer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('user.email', 'newcustomer@example.com');

        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail): bool {
            return $mail->hasTo('newcustomer@example.com') && $mail->purpose === 'verify';
        });
    }

    public function test_resend_verification_code_sends_new_email_for_unverified_customer(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'pending@example.com',
            'type' => 'customer',
            'email_verified_at' => null,
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/auth/resend-verification-code', [
            'email' => 'pending@example.com',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Verification code resent successfully.');

        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail): bool {
            return $mail->hasTo('pending@example.com') && $mail->purpose === 'verify';
        });

        $cacheKey = 'auth:email_otp:verify:'.hash('sha256', 'pending@example.com');
        $this->assertNotNull(Cache::get($cacheKey));
    }

    public function test_role_permission_seeder_includes_catalog_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertDatabaseHas('permissions', ['name' => 'admin.categories.view', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.categories.create', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.categories.edit', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.categories.delete', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.products.view', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.products.create', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.products.edit', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'admin.products.delete', 'guard_name' => 'web']);
    }
}
