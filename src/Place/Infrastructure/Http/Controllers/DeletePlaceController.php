<?php

declare(strict_types=1);

namespace Src\Place\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Place\Application\UseCases\DeletePlaceUseCase;
use Src\Place\Domain\Exceptions\PlaceNotFoundException;
use Src\Place\Domain\Exceptions\UnauthorizedPlaceActionException;

readonly class DeletePlaceController
{
    public function __construct(
        private DeletePlaceUseCase $useCase,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        try {
            ($this->useCase)($id, $request->user()->id);
        } catch (PlaceNotFoundException) {
            return response('Lugar no encontrado', 404);
        } catch (UnauthorizedPlaceActionException) {
            return response('No autorizado', 403);
        }

        return response()->noContent();
    }
}
