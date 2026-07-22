<?php

namespace App\Enums;

enum StudentStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Blacklisted = 'blacklisted';
}
