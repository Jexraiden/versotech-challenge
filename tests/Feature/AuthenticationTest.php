<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_guest_can_view_login_and_is_redirected_from_protected_home(): void
    {
        $this->get(route('login'))->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
        $this->get(route('home'))->assertRedirect(route('login'));
        $this->get(route('catalog.index'))->assertRedirect(route('login'));
    }

    public function test_valid_credentials_authenticate_and_regenerate_the_session(): void
    {
        $this->withSession(['marker' => 'preserved']);
        $oldSessionId = session()->getId();

        $this->post(route('login.store'), [
            'email' => 'bompreco@example.test',
            'password' => 'password',
        ])->assertRedirect(route('catalog.index'));

        $this->assertAuthenticatedAs(User::where('email', 'bompreco@example.test')->firstOrFail());
        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertSame('preserved', session('marker'));
        $this->get(route('catalog.index'))->assertInertia(fn (Assert $page) => $page->component('Catalog/Index'));
        $this->assertSame(1, auth()->user()->company->id);
    }

    public function test_invalid_credentials_use_a_generic_error(): void
    {
        $genericError = 'Não foi possível fazer login. Verifique suas credenciais e tente novamente.';

        $this->post(route('login.store'), [
            'email' => 'bompreco@example.test',
            'password' => 'wrong',
        ])->assertSessionHasErrors(['email' => $genericError]);

        $this->post(route('login.store'), [
            'email' => 'unknown@example.test',
            'password' => 'wrong',
        ])->assertSessionHasErrors(['email' => $genericError]);
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), [
                'email' => 'bompreco@example.test',
                'password' => 'wrong',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => 'bompreco@example.test',
            'password' => 'password',
        ])->assertTooManyRequests();
    }

    public function test_login_email_limit_is_shared_across_client_ips(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.'.$attempt])
                ->post(route('login.store'), [
                    'email' => 'bompreco@example.test',
                    'password' => 'wrong',
                ])->assertSessionHasErrors('email');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.20'])
            ->post(route('login.store'), [
                'email' => 'bompreco@example.test',
                'password' => 'password',
            ])->assertTooManyRequests();
    }

    public function test_login_ip_limit_is_shared_across_email_addresses(): void
    {
        for ($attempt = 1; $attempt <= 30; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->post(route('login.store'), [
                    'email' => 'unknown'.$attempt.'@example.test',
                    'password' => 'wrong',
                ])->assertSessionHasErrors('email');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post(route('login.store'), [
                'email' => 'another@example.test',
                'password' => 'wrong',
            ])->assertTooManyRequests();
    }

    public function test_logout_invalidates_authentication_and_redirects_to_login(): void
    {
        $user = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($user)->withSession(['marker' => 'remove-me']);
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertNull(session('marker'));
    }

    public function test_authenticated_user_cannot_open_guest_login_route(): void
    {
        $user = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($user)->get(route('login'))->assertRedirect(route('catalog.index'));
    }
}
