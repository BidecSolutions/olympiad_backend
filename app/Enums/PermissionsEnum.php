<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case ViewDashboard = 'view_dashboard';
    case ManageUsers = 'manage_users';
    case ManageOlympiads = 'manage_olympiads';
    case SubmitSolution = 'submit_solution';
    case ViewResults = 'view_results';
}
