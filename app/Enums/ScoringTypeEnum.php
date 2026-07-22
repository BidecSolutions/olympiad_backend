<?php

namespace App\Enums;

enum ScoringTypeEnum: string
{
    case Points = 'points';
    case Time = 'time';
    case Rank = 'rank';
    case Percentage = 'percentage';
}
