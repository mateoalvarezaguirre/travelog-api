<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Services;

class ExcerptGenerator
{
    public static function generate(string $htmlContent, int $maxLength = 200): string
    {
        $text = strip_tags($htmlContent);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim((string) $text);

        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $maxLength);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false && $lastSpace > $maxLength * 0.8) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}
