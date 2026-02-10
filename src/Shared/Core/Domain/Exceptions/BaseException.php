<?php

declare(strict_types=1);

namespace Src\Shared\Core\Domain\Exceptions;

use Illuminate\Http\JsonResponse;

class BaseException extends \Exception
{
    public function __construct(
        private string $errorCode,
        private int $statusCode,
        private ?array $extraParams = null
    ) {
        parent::__construct(
            $this->translate($this->errorCode),
            $this->statusCode
        );
    }

    public function render(): JsonResponse
    {
        $response = [
            'code'    => $this->getErrorCode(),
            'message' => $this->getMessage(),
        ];

        if ($this->extraParams !== null) {
            $response['extra'] = $this->extraParams;
        }

        return response()->json($response, $this->statusCode);
    }

    private function getErrorCode(): string
    {
        $arrayErrorCode = explode('.', $this->errorCode);

        return (string) last($arrayErrorCode);
    }

    private function translate(string $errorCode): string
    {
        try {
            return (string) __($errorCode);
        } catch (\Throwable $exception) {
            return '';
        }
    }
}
