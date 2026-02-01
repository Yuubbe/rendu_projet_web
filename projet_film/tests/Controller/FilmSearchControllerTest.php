<?php

namespace App\Tests\Controller;

use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilmSearchControllerTest extends WebTestCase
{
    public function testSearchReturnsJsonResultsAndSuggestions(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createMock(FilmRepository::class);
        $mockRepo->method('searchAdvanced')->with('alp', [], null, null, 200)->willReturn([
            ['id' => 1, 'titre' => 'Alpha', 'annee' => 2020, 'prixLocationDefaut' => 5, 'affiche' => null],
        ]);
        $mockRepo->method('suggestTitles')->willReturn(['Alpha', 'Alphabeta']);

        static::getContainer()->set(FilmRepository::class, $mockRepo);

        $client->request('GET', '/films/search?q=alp');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('Alpha', $data['results'][0]['titre'] ?? null);
        $this->assertContains('Alpha', $data['suggestions']);
    }

    public function testSearchWorksWithTermOnly(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createMock(FilmRepository::class);
        $mockRepo->method('searchAdvanced')->with('gamma', [], null, null, 200)->willReturn([
            ['id' => 5, 'titre' => 'Gamma', 'annee' => 2010, 'prixLocationDefaut' => 7, 'affiche' => null],
        ]);
        $mockRepo->method('suggestTitles')->willReturn(['Gamma']);

        static::getContainer()->set(FilmRepository::class, $mockRepo);

        $client->request('GET', '/films/search?q=gamma');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Gamma', $data['results'][0]['titre'] ?? null);
        $this->assertContains('Gamma', $data['suggestions']);
    }

    public function testSearchWorksWithFiltersWithoutTerm(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createMock(FilmRepository::class);
        $mockRepo->method('searchAdvanced')->with(null, [3], 2000, 2000, 200)->willReturn([
            ['id' => 2, 'titre' => 'Beta', 'annee' => 2000, 'prixLocationDefaut' => 6, 'affiche' => null],
        ]);
        $mockRepo->method('suggestTitles')->willReturn([]);

        static::getContainer()->set(FilmRepository::class, $mockRepo);

        $client->request('GET', '/films/search?genres=3&annee_min=2000&annee_max=2000');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('Beta', $data['results'][0]['titre'] ?? null);
        $this->assertEmpty($data['suggestions']);
    }

    public function testSearchWorksWithGenreOnly(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createMock(FilmRepository::class);
        $mockRepo->method('searchAdvanced')->with(null, [2], null, null, 200)->willReturn([
            ['id' => 3, 'titre' => 'Delta', 'annee' => 1999, 'prixLocationDefaut' => 4, 'affiche' => null],
        ]);
        $mockRepo->method('suggestTitles')->willReturn([]);

        static::getContainer()->set(FilmRepository::class, $mockRepo);

        $client->request('GET', '/films/search?genres=2');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Delta', $data['results'][0]['titre'] ?? null);
        $this->assertEmpty($data['suggestions']);
    }

    public function testSearchWorksWithYearOnly(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createMock(FilmRepository::class);
        $mockRepo->method('searchAdvanced')->with(null, [], 1995, 1995, 200)->willReturn([
            ['id' => 4, 'titre' => 'Epsilon', 'annee' => 1995, 'prixLocationDefaut' => 3, 'affiche' => null],
        ]);
        $mockRepo->method('suggestTitles')->willReturn([]);

        static::getContainer()->set(FilmRepository::class, $mockRepo);

        $client->request('GET', '/films/search?annee_min=1995&annee_max=1995');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Epsilon', $data['results'][0]['titre'] ?? null);
        $this->assertEmpty($data['suggestions']);
    }
}
