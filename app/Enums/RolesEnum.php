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
}
