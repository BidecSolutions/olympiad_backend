<?php

namespace App\Models;

use Database\Factories\CompetitionTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
])]
class CompetitionType extends Model
{
    /** @use HasFactory<CompetitionTypeFactory> */
    use HasFactory;
}
