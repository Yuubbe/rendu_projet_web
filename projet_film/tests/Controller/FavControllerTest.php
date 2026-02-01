<?php

namespace App\Tests\Controller;

use App\Tests\Support\TestUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FavControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $user = new TestUser('fav@test.com');
        $client->loginUser($user);
        $client->request('GET', '/fav');

        self::assertResponseIsSuccessful();
    }

}
