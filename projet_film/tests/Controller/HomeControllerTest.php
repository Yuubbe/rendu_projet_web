<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\FilmRepository;
use App\Service\RecommendationService;

final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createMock(FilmRepository::class);
        $mockRepo->method('findBy')->willReturn([]);
        static::getContainer()->set(FilmRepository::class, $mockRepo);

        $mockReco = $this->createMock(RecommendationService::class);
        $mockReco->method('recommend')->willReturn([]);
        static::getContainer()->set(RecommendationService::class, $mockReco);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
