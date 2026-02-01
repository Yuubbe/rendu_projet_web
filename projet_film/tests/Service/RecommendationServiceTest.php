<?php

namespace App\Tests\Service;

use App\Entity\Film;
use App\Entity\Genre;
use App\Entity\User;
use App\Service\RecommendationService;
use App\Repository\FilmRepository;
use PHPUnit\Framework\TestCase;

class RecommendationServiceTest extends TestCase
{
    private function setId(object $entity, int $id): void
    {
        // Bypass private id without triggering Reflection deprecation
        $setter = \Closure::bind(function ($obj, $id) {
            $obj->id = $id;
        }, null, $entity);

        $setter($entity, $id);
    }

    public function testRecommendUsesGenreAffinityWhenAvailable(): void
    {
        $genre = new Genre();
        $this->setId($genre, 1);

        $favFilm = new Film();
        $this->setId($favFilm, 10);
        $favFilm->getGenres()->add($genre);

        $user = new User();
        $user->addFavori($favFilm);

        $expected = [new Film()];

        $repo = $this->createMock(FilmRepository::class);
        $repo->expects($this->once())
            ->method('findByGenresPrioritized')
            ->with([1], [10], 6)
            ->willReturn($expected);
        $repo->expects($this->never())->method('findMostPopular');

        $service = new RecommendationService($repo);
        $result = $service->recommend($user, 6, false); // force exploitation

        $this->assertSame($expected, $result);
    }

    public function testRecommendFallsBackToPopular(): void
    {
        $user = new User();

        $popular = [new Film(), new Film()];

        $repo = $this->createMock(FilmRepository::class);
        $repo->expects($this->never())
            ->method('findByGenresPrioritized');
        $repo->expects($this->once())
            ->method('findMostPopular')
            ->with(6, [])
            ->willReturn($popular);

        $service = new RecommendationService($repo);
        $result = $service->recommend($user, 6, false);

        $this->assertSame($popular, $result);
    }
}
