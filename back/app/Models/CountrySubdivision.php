<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountrySubdivision extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'country_id',
    ];
}
