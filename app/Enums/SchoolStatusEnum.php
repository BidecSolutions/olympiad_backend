<?php

namespace App\Enums;

enum SchoolStatusEnum: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Blacklisted = 'blacklisted';
}
