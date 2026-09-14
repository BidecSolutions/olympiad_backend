<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubAdminStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\SubAdmin;
use App\Services\SubAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SubAdminController extends Controller
{
    public function __construct(private SubAdminService $subAdminService) {}

    public function index(): JsonResponse
    {
        $subAdmins = $this->subAdminService->list();

        return response()->json([
            'status' => true,
            'message' => 'Sub admins retrieved successfully.',
            'data' => $subAdmins,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->subAdminPayloadRules());

        $subAdmin = $this->subAdminService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Sub admin created successfully.',
            'data' => $subAdmin,
        ], 201);
    }

    public function show(SubAdmin $subAdmin): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Sub admin retrieved successfully.',
            'data' => $this->subAdminService->find($subAdmin),
        ]);
    }

    public function update(Request $request, SubAdmin $subAdmin): JsonResponse
    {
        $payloadData = $request->validate($this->subAdminPayloadRules($subAdmin));

        $subAdmin = $this->subAdminService->update($subAdmin, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Sub admin updated successfully.',
            'data' => $subAdmin,
        ]);
    }

    public function destroy(SubAdmin $subAdmin): JsonResponse
    {
        $this->subAdminService->delete($subAdmin);

        return response()->json([
            'status' => true,
            'message' => 'Sub admin deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function subAdminPayloadRules(?SubAdmin $subAdmin = null): array
    {
        $subAdminId = $subAdmin?->id;
        $userId = $subAdmin?->user_id;

        return [
            'name' => $subAdminId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'email' => $subAdminId
                ? [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($userId),
                ]
                : [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
            'password' => $subAdminId
                ? ['sometimes', 'nullable', 'string', 'confirmed', Password::defaults()]
                : ['required', 'string', 'confirmed', Password::defaults()],
            'phone' => $subAdminId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'region' => $subAdminId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(SubAdminStatusEnum::class)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
