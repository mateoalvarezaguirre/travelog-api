<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    public function delete(User $user, Place $place): bool
    {
        return $user->id === $place->user_id;
    }
}
