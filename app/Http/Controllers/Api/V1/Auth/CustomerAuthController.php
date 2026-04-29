<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\CustomerRegistered;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class CustomerAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $customer = Customer::create([
            ...$request->safe()->except(['password', 'password_confirmation']),
            'password' => $request->validated('password'),
            'state' => 'enabled',
        ]);

        $token = $customer->createToken('customer', ['customer'])->plainTextToken;

        CustomerRegistered::dispatch($customer);

        return ApiResponse::success([
            'customer' => new CustomerResource($customer->load('addresses')),
            'token' => $token,
        ], 'Registered');
    }

    public function login(LoginRequest $request)
    {
        $customer = Customer::where('email', $request->validated('email'))->first();

        if (! $customer || ! Hash::check($request->validated('password'), $customer->password ?? '')) {
            return ApiResponse::error('Invalid credentials', 422, [
                'email' => ['Invalid email or password.'],
            ]);
        }

        if (($customer->state ?? 'enabled') !== 'enabled') {
            return ApiResponse::error('Account is disabled', 403);
        }

        $token = $customer->createToken('customer', ['customer'])->plainTextToken;

        return ApiResponse::success([
            'customer' => new CustomerResource($customer->load('addresses')),
            'token' => $token,
        ], 'Logged in');
    }

    public function logout(Request $request)
    {
        $customer = $request->user();
        $customer?->currentAccessToken()?->delete();

        return ApiResponse::success(null, 'Logged out');
    }

    public function me(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $request->user();

        return ApiResponse::success(new CustomerResource($customer->load('addresses')));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $request->user();

        $customer->update($request->validated());

        return ApiResponse::success(new CustomerResource($customer->refresh()->load('addresses')), 'Profile updated');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $request->user();

        if (! Hash::check($request->validated('current_password'), $customer->password ?? '')) {
            return ApiResponse::error('Current password is incorrect', 422, [
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $customer->forceFill([
            'password' => $request->validated('new_password'),
        ])->save();

        return ApiResponse::success(null, 'Password updated');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::broker('customers')->sendResetLink([
            'email' => $request->validated('email'),
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            return ApiResponse::error('Unable to send reset link', 422, [
                'email' => [__($status)],
            ]);
        }

        return ApiResponse::success(null, 'Reset link sent');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::broker('customers')->reset(
            $request->only('email', 'token', 'password', 'password_confirmation'),
            function (Customer $customer, string $password) {
                $customer->forceFill(['password' => $password])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ApiResponse::error('Unable to reset password', 422, [
                'email' => [__($status)],
            ]);
        }

        return ApiResponse::success(null, 'Password reset successful');
    }
}

