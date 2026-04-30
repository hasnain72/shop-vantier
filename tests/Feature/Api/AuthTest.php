<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // ── Register ──────────────────────────────────────────────────────────────

    public function test_customer_can_register(): void
    {
        Event::fake();

        $res = $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Ahmed',
            'last_name'             => 'Khan',
            'email'                 => 'ahmed@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'customer' => ['id', 'email', 'first_name']]]);

        $this->assertDatabaseHas('customers', ['email' => 'ahmed@example.com']);
    }

    public function test_register_requires_unique_email(): void
    {
        Customer::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Ali',
            'last_name'             => 'Khan',
            'email'                 => 'taken@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422);
    }

    public function test_register_requires_password_confirmation(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'password'   => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function test_customer_can_login(): void
    {
        $customer = Customer::factory()->create(['email' => 'login@example.com']);

        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => 'login@example.com',
            'password' => 'password',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'customer']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        Customer::factory()->create(['email' => 'user@example.com']);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'wrongpassword',
        ])->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_login_fails_for_disabled_account(): void
    {
        Customer::factory()->disabled()->create(['email' => 'disabled@example.com']);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'disabled@example.com',
            'password' => 'password',
        ])->assertStatus(403);
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'password',
        ])->assertStatus(422);
    }

    // ── Me ────────────────────────────────────────────────────────────────────

    public function test_authenticated_customer_can_get_profile(): void
    {
        [$customer, $token] = $this->customerWithToken();

        $this->getJson('/api/v1/auth/me', $this->authHeaders($token))
            ->assertStatus(200)
            ->assertJsonPath('data.customer.email', $customer->email);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function test_customer_can_logout(): void
    {
        [$customer, $token] = $this->customerWithToken();

        $this->postJson('/api/v1/auth/logout', [], $this->authHeaders($token))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Token should be revoked
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    // ── Update Profile ────────────────────────────────────────────────────────

    public function test_customer_can_update_profile(): void
    {
        [$customer, $token] = $this->customerWithToken();

        $this->putJson('/api/v1/auth/profile', [
            'first_name' => 'Updated',
            'last_name'  => 'Name',
        ], $this->authHeaders($token))
            ->assertStatus(200)
            ->assertJsonPath('data.customer.first_name', 'Updated');
    }

    // ── Change Password ───────────────────────────────────────────────────────

    public function test_customer_can_change_password(): void
    {
        [$customer, $token] = $this->customerWithToken();

        $this->putJson('/api/v1/auth/change-password', [
            'current_password'          => 'password',
            'new_password'              => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ], $this->authHeaders($token))
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        [$customer, $token] = $this->customerWithToken();

        $this->putJson('/api/v1/auth/change-password', [
            'current_password'          => 'wrongpassword',
            'new_password'              => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ], $this->authHeaders($token))
            ->assertStatus(422);
    }
}
