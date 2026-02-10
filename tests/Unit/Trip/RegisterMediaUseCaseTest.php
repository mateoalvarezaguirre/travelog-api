<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Application\DTOs\In\RegisterMediaDTO;
use Src\Trip\Application\UseCases\RegisterMediaUseCase;
use Src\Trip\Domain\Repositories\TripMediaRepository;
use Src\Trip\Domain\ValueObjects\RegisteredMedia;
use Tests\TestCase;

/**
 * @internal
 */
class RegisterMediaUseCaseTest extends TestCase
{
    public function test_invoke_returns_registered_media_from_repository(): void
    {
        $registered = new RegisteredMedia(1, 'https://example.com/image.jpg');
        $repository = $this->createMock(TripMediaRepository::class);
        $repository->expects($this->once())
            ->method('register')
            ->with(1, 'https://example.com/photo.jpg', null)
            ->willReturn($registered);

        $useCase = new RegisterMediaUseCase($repository);
        $dto     = new RegisterMediaDTO(1, 'https://example.com/photo.jpg', null);

        $result = $useCase($dto);

        $this->assertSame($registered, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('https://example.com/image.jpg', $result->url);
    }
}
