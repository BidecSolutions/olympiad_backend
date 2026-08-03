<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    private const GUARD_NAME = 'web';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->seedPermissions();
        $this->seedRoles();
    }

    private function seedPermissions(): void
    {
        $modules = [
            'Auth' => [
                'Register' => PermissionsEnum::AuthRegister,
                'Login' => PermissionsEnum::AuthLogin,
                'Forgot Password' => PermissionsEnum::AuthPasswordForgot,
                'Reset Password' => PermissionsEnum::AuthPasswordReset,
                'Logout' => PermissionsEnum::AuthLogout,
                'Send Verification' => PermissionsEnum::AuthVerificationSend,
                'Verify Email' => PermissionsEnum::AuthVerificationVerify,
            ],
            'Events' => [
                'List' => PermissionsEnum::EventsList,
                'Create' => PermissionsEnum::EventsStore,
                'View' => PermissionsEnum::EventsShow,
                'Edit' => PermissionsEnum::EventsPatch,
                'Delete' => PermissionsEnum::EventsDestroy,
            ],
            'Officials' => [
                'List' => PermissionsEnum::OfficialsList,
                'Create' => PermissionsEnum::OfficialsStore,
                'View' => PermissionsEnum::OfficialsShow,
                'Edit' => PermissionsEnum::OfficialsPatch,
                'Delete' => PermissionsEnum::OfficialsDestroy,
            ],
            'Competitions' => [
                'List' => PermissionsEnum::CompetitionsList,
                'Create' => PermissionsEnum::CompetitionsStore,
                'View' => PermissionsEnum::CompetitionsShow,
                'Edit' => PermissionsEnum::CompetitionsPatch,
                'Delete' => PermissionsEnum::CompetitionsDestroy,
            ],
            'Competition Categories' => [
                'List' => PermissionsEnum::CompetitionCategoriesList,
                'Create' => PermissionsEnum::CompetitionCategoriesStore,
                'View' => PermissionsEnum::CompetitionCategoriesShow,
                'Edit' => PermissionsEnum::CompetitionCategoriesPatch,
                'Delete' => PermissionsEnum::CompetitionCategoriesDestroy,
            ],
            'Category Participations' => [
                'List' => PermissionsEnum::CompetitionCategoryParticipationsList,
                'Create' => PermissionsEnum::CompetitionCategoryParticipationsStore,
                'View' => PermissionsEnum::CompetitionCategoryParticipationsShow,
                'Edit' => PermissionsEnum::CompetitionCategoryParticipationsPatch,
                'Delete' => PermissionsEnum::CompetitionCategoryParticipationsDestroy,
            ],
            'Schools' => [
                'List' => PermissionsEnum::SchoolsList,
                'Create' => PermissionsEnum::SchoolsStore,
                'View' => PermissionsEnum::SchoolsShow,
                'Edit' => PermissionsEnum::SchoolsPatch,
                'Delete' => PermissionsEnum::SchoolsDestroy,
            ],
            'Students' => [
                'List' => PermissionsEnum::SchoolStudentsList,
                'Create' => PermissionsEnum::SchoolStudentsStore,
                'View' => PermissionsEnum::SchoolStudentsShow,
                'Edit' => PermissionsEnum::SchoolStudentsPatch,
                'Delete' => PermissionsEnum::SchoolStudentsDestroy,
            ],
            'Teams' => [
                'List' => PermissionsEnum::SchoolTeamsList,
                'Create' => PermissionsEnum::SchoolTeamsStore,
                'View' => PermissionsEnum::SchoolTeamsShow,
                'Edit' => PermissionsEnum::SchoolTeamsPatch,
                'Delete' => PermissionsEnum::SchoolTeamsDestroy,
            ],
            'Team Members' => [
                'List' => PermissionsEnum::SchoolTeamMembersList,
                'Create' => PermissionsEnum::SchoolTeamMembersStore,
                'View' => PermissionsEnum::SchoolTeamMembersShow,
                'Edit' => PermissionsEnum::SchoolTeamMembersPatch,
                'Delete' => PermissionsEnum::SchoolTeamMembersDestroy,
            ],
            'Registrations' => [
                'List' => PermissionsEnum::SchoolRegistrationsList,
                'Create' => PermissionsEnum::SchoolRegistrationsStore,
                'View' => PermissionsEnum::SchoolRegistrationsShow,
                'Edit' => PermissionsEnum::SchoolRegistrationsPatch,
                'Delete' => PermissionsEnum::SchoolRegistrationsDestroy,
            ],
        ];

        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $label => $permission) {
                Permission::query()->updateOrCreate(
                    [
                        'name' => $permission->value,
                        'guard_name' => self::GUARD_NAME,
                    ],
                    [
                        'module_name' => $module,
                        'label' => $label,
                    ],
                );
            }
        }
    }

    private function seedRoles(): void
    {
        $allPermissions = Permission::query()
            ->where('guard_name', self::GUARD_NAME)
            ->pluck('name');

        foreach (RolesEnum::cases() as $roleEnum) {
            $role = Role::query()->updateOrCreate(
                [
                    'name' => $roleEnum->value,
                    'guard_name' => self::GUARD_NAME,
                ],
            );

            if ($roleEnum === RolesEnum::SuperAdmin) {
                $role->syncPermissions($allPermissions);
            }
        }
    }
}
