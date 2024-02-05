<?php

namespace App\Models\Interfaces;

use App\Models\User;

interface OwnedModel
{
    public function owner(): User;
}
