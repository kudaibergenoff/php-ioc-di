<?php

namespace App\Repositories;

use Database\Database as Db;
use App\Models\User;

class UserRepository
{
    public function __construct(
        private Db $db
    )
    {}

    public function findByEmail(string $email): ?User
    {
        $res = $this->db->query(
            'SELECT * FROM users WHERE email=:email',
            [':email' => $email],
            User::class
        );
        return !empty($res) ? $res[0] : null;
    }
}