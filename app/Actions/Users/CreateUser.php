<?php

namespace App\Actions\Users;

use App\Events\UserModified;
use App\Models\User;
use App\Services\AccountActivationService;

final class CreateUser
{
    public function __construct(
        private readonly AccountActivationService $activationService
    ) {}

    public function execute(array $data): User
    {
        // Create user with pending status and no password
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => User::STATUS_PENDING_ACTIVATION,
            'password' => bcrypt(uniqid()),
        ]);

        // Fire audit event
        event(new UserModified(
            model: $user,
            user: request()->user(),
            action: 'created'
        ));

        // Send activation email
        $this->activationService->sendActivationEmail($user, request()->ip());

        return $user;
    }
}
