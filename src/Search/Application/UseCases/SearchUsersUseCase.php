<?php

declare(strict_types=1);

namespace Src\Search\Application\UseCases;

use Src\Search\Domain\Repositories\SearchRepository;

readonly class SearchUsersUseCase
{
    private const DEFAULT_LIMIT = 50;

    public function __construct(
        private SearchRepository $searchRepository,
    ) {}

    /**
     * @return mixed[]
     */
    public function __invoke(string $query): array
    {
        return $this->searchRepository->searchUsers($query, self::DEFAULT_LIMIT);
    }
}
