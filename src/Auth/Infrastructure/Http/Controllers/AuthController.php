<?php

namespace Src\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Src\Auth\Infrastructure\Http\Requests\ForgotPasswordRequest;
use Src\Auth\Infrastructure\Http\Requests\GoogleAuthRequest;
use Src\Auth\Infrastructure\Http\Requests\LoginRequest;
use Src\Auth\Infrastructure\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = $request->user();

        return response()->json($this->issueTokenResponse($user));
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
            'username' => $this->generateUniqueUsername(
                $request->string('name')->toString(),
                $request->string('email')->toString(),
            ),
        ]);

        return response()->json($this->issueTokenResponse($user), 201);
    }

    public function google(GoogleAuthRequest $request): JsonResponse
    {
        $token = $request->string('id_token')->toString();

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $token,
        ]);

        if (! $response->ok()) {
            return response()->json(['message' => 'Invalid Google token.'], 401);
        }

        $payload = $response->json();
        $googleId = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? 'Google User';
        $avatar = $payload['picture'] ?? null;

        if (! $googleId || ! $email) {
            return response()->json(['message' => 'Google token missing required claims.'], 422);
        }

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'username' => $this->generateUniqueUsername($name, $email),
                'google_id' => $googleId,
                'avatar' => $avatar,
                'email_verified_at' => now(),
            ]);
        } else {
            $user->forceFill([
                'google_id' => $user->google_id ?? $googleId,
                'avatar' => $user->avatar ?? $avatar,
            ])->save();
        }

        return response()->json($this->issueTokenResponse($user));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 422);
        }

        return response()->json(['message' => __($status)]);
    }

    public function me(): JsonResponse
    {
        return response()->json(['user' => Auth::user()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function issueTokenResponse(User $user): array
    {
        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }

    private function generateUniqueUsername(string $name, string $email): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = Str::before($email, '@');
        }

        $candidate = $base;
        $suffix = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $candidate = $base . $suffix;
            $suffix += 1;
        }

        return $candidate;
    }
}

