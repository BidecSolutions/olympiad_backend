<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\SchoolStatusEnum;
use App\Enums\SubAdminStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\School;
use App\Models\SubAdmin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $login = trim($request->string('login')->toString());
        $user = $this->resolveUser($login);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => [__('auth.failed')],
            ]);
        }

        $this->assertAccountIsActive($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->toAuthArray(),
        ], 200);
    }

    private function resolveUser(string $login): ?User
    {
        if (str_contains($login, '@')) {
            return User::query()->where('email', $login)->first();
        }

        if (! ctype_digit($login)) {
            return null;
        }

        $id = (int) $login;

        $school = School::query()->find($id);
        if ($school) {
            return $school->user;
        }

        $subAdmin = SubAdmin::query()->find($id);

        return $subAdmin?->user;
    }

    /**
     * @throws ValidationException
     */
    private function assertAccountIsActive(User $user): void
    {
        $school = $user->school;
        if ($school && $school->status !== SchoolStatusEnum::Approved) {
            throw ValidationException::withMessages([
                'login' => ['This school account is not approved yet.'],
            ]);
        }

        $subAdmin = $user->subAdmin;
        if ($subAdmin && $subAdmin->status !== SubAdminStatusEnum::Active) {
            throw ValidationException::withMessages([
                'login' => ['This sub admin account is inactive.'],
            ]);
        }
    }
}
