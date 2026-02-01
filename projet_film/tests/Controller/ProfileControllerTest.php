<?php

namespace App\Tests\Controller;

use App\Tests\Support\TestUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    public function testPasswordChangeShowsToast(): void
    {
        $client = static::createClient();
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new TestUser('tester@example.com');
        $user->setPassword($hasher->hashPassword($user, 'oldpass'));

        $client->loginUser($user);

        $client->request('POST', '/profile', [
            'current_password' => 'oldpass',
            'new_password' => 'newpass123',
            'confirm_password' => 'newpass123',
        ]);

        $this->assertResponseRedirects('/profile');
        $client->followRedirect();
        $this->assertSelectorExists('.toast-notice');
    }
}
