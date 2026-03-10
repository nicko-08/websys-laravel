<?php

namespace App\Actions\Users;

use App\Events\UserModified;
use App\Models\User;

final class UpdateUser
{
    public function execute(User $user, array $data): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        // Fire audit event
        event(new UserModified(
            model: $user,
            user: request()->user(),
            action: 'updated'
        ));

        return $user->refresh();
    }
}
