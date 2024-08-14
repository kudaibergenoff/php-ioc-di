<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class UserController {
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * @throws \Exception
     */
    public function handle(): string
    {
        $user = $this->userRepository->findByEmail('test@test.com');
        if (empty($user)) {
            throw new \Exception('Пользователь не найден!');
        }
        return <<<RESPONSE
                Имя пользователя: $user->name
                RESPONSE;
    }
}