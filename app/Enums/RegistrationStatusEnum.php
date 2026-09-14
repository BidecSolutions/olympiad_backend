<?php

namespace App\Enums;

enum RegistrationStatusEnum: string
{
    case Pending = 'pending';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
