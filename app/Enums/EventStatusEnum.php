<?php

namespace App\Enums;

enum EventStatusEnum: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
