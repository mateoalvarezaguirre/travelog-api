<?php

declare(strict_types=1);

namespace Src\Search\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Search\Application\UseCases\SearchUsersUseCase;
use Src\Shared\Infrastructure\Http\Resources\UserSummaryResource;

readonly class SearchUsersController
{
    public function __construct(
        private SearchUsersUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $users = ($this->useCase)($query);

        $data = UserSummaryResource::collection($users);

        return response()->json($data);
    }
}
