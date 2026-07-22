<?php

namespace App\Enums;

enum SchoolDocumentStatusEnum: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
