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
            'first_name' => 'Customer',
            'last_name' => 'One',
            'email' => 'newcustomer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('user.email', 'newcustomer@example.com')
            ->assertJsonPath('user.first_name', 'Customer')
            ->assertJsonPath('user.last_name', 'One')
            ->assertJsonPath('user.name', 'Customer One');

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

    public function test_profile_returns_user_resource_shape(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'name' => 'John Doe',
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response
            ->assertOk()
            ->assertJsonPath('first_name', 'John')
            ->assertJsonPath('last_name', 'Doe')
            ->assertJsonPath('name', 'John Doe')
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('type', 'customer');
    }

    public function test_customer_can_update_profile_data(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'name' => 'Old Name',
            'phone' => null,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->patchJson('/api/v1/auth/profile', [
            'first_name' => 'New',
            'last_name' => 'Customer',
            'phone' => '01001234567',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Profile updated successfully.')
            ->assertJsonPath('user.first_name', 'New')
            ->assertJsonPath('user.last_name', 'Customer')
            ->assertJsonPath('user.name', 'New Customer')
            ->assertJsonPath('user.phone', '01001234567');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'New',
            'last_name' => 'Customer',
            'name' => 'New Customer',
            'phone' => '01001234567',
        ]);
    }
}
