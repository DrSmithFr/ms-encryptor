<?php

declare(strict_types = 1);

namespace App\Service;

use Exception;
use App\Entity\User;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @throws Exception
     *
     * @param string $password
     * @param string $email
     *
     * @return User
     */
    public function createUser(string $password, string $uuid = null): User
    {
        $user = (new User())
            ->setUuid($uuid ? Uuid::fromString($uuid) : Uuid::uuid4())
            ->setPlainPassword($password);

        $this->updatePassword($user);

        return $user;
    }

    /**
     * @param User   $user
     * @return User
     */
    public function updatePassword(User $user): User
    {
        $encoded = $this->encodePassword($user->getPlainPassword());

        $user->setPassword($encoded);
        $user->setPlainPassword(null);

        return $user;
    }

    /**
     * @param string $pass
     *
     * @return string
     */
    private function encodePassword(string $pass): string
    {
        return password_hash($pass, PASSWORD_ARGON2I);
    }
}
