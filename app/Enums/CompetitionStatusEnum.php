<?php

namespace App\Enums;

enum CompetitionStatusEnum: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Inactive = 'inactive';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
