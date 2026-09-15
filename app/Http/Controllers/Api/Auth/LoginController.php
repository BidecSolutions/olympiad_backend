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

        $school = $user->school;
        if ($school !== null) {
            return $this->loginSchoolUser($user, $school);
        }

        $this->assertSubAdminIsActive($user);

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
    private function loginSchoolUser(User $user, School $school): JsonResponse
    {
        if (in_array($school->status, [SchoolStatusEnum::Rejected, SchoolStatusEnum::Blacklisted], true)) {
            throw ValidationException::withMessages([
                'login' => ['This school account is not approved yet.'],
            ]);
        }

        if ($school->status === SchoolStatusEnum::Pending) {
            return response()->json([
                'status' => true,
                'message' => 'School registration is under review.',
                'login_state' => 'school_pending',
                'data' => $this->formatSchoolStatus($school),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'login_state' => 'school_approved',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->toAuthArray(),
            'data' => $this->formatSchoolStatus($school),
        ], 200);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatSchoolStatus(School $school): array
    {
        return [
            'id' => $school->id,
            'application_id' => sprintf('APP-%d-%06d', now()->year, $school->id),
            'school_id' => $school->school_code ?? sprintf('SCH-%d-%04d', now()->year, $school->id),
            'name' => $school->name,
            'email' => $school->email,
            'status' => $school->status->value,
            'requested_quota' => $school->requested_quota,
            'approved_quota' => $school->approved_quota ?? $school->requested_quota,
            'submitted_at' => $school->created_at?->toIso8601String(),
        ];
    }

    /**
     * @throws ValidationException
     */
    private function assertSubAdminIsActive(User $user): void
    {
        $subAdmin = $user->subAdmin;
        if ($subAdmin && $subAdmin->status !== SubAdminStatusEnum::Active) {
            throw ValidationException::withMessages([
                'login' => ['This sub admin account is inactive.'],
            ]);
        }
    }
}
