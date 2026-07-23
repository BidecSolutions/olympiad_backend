<?php

namespace App\Enums;

enum RolesEnum: string
{
    case SuperAdmin = 'super_admin';
    case SubAdmin = 'sub_admin';
    case SchoolAdmin = 'school_admin';
    case Judge = 'judge';
    case Referee = 'referee';
    case Umpire = 'umpire';

    public static function fromOfficialType(OfficialTypeEnum $type): self
    {
        return self::from($type->value);
    }

    /**
     * @return list<string>
     */
    public static function officialRoleValues(): array
    {
        return array_map(
            fn (self $role): string => $role->value,
            [self::Judge, self::Referee, self::Umpire],
        );
    }
}
