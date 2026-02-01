<?php

namespace App\Tests\Security;

use App\Tests\Support\TestUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TestUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new TestUser($identifier);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return is_a($class, TestUser::class, true);
    }

    public function upgradePassword(
        \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword
    ): void
    {
        if ($user instanceof TestUser) {
            $user->setPassword($newHashedPassword);
        }
    }
}
