<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvatarRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Models\Avatar;

class AvatarController extends Controller
{
    public function __invoke(): array
    {
        return Avatar::where('default', 1)->get()->toArray();
    }
}
