<?php

namespace App\Enums;

enum OfficialTypeEnum: string
{
    case Judge = 'judge';
    case Referee = 'referee';
    case Umpire = 'umpire';
}
