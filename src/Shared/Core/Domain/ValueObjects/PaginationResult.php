<?php

namespace Src\Shared\Core\Domain\ValueObjects;

readonly class PaginationResult
{
    public function __construct(
        private array $items,
        private int   $currentPage,
        private int   $lastPage,
        private int   $perPage,
        private int   $total,
    ) {}

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function toMeta(): array
    {
        return [
            'currentPage' => $this->currentPage,
            'lastPage' => $this->lastPage,
            'perPage' => $this->perPage,
            'total' => $this->total,
        ];
    }
}
