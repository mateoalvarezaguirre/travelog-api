<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Domain\Services\ExcerptGenerator;
use Tests\TestCase;

/**
 * @internal
 */
class ExcerptGeneratorTest extends TestCase
{
    public function test_generate_strips_html_tags_and_trims_whitespace(): void
    {
        $html = '<p>Hello  <strong>World</strong></p>  <br>  <em>Goodbye</em>';

        $result = ExcerptGenerator::generate($html);

        $this->assertSame('Hello World Goodbye', $result);
    }

    public function test_generate_returns_full_text_when_shorter_than_max_length(): void
    {
        $html = '<p>Short text</p>';

        $result = ExcerptGenerator::generate($html, 200);

        $this->assertSame('Short text', $result);
        $this->assertStringNotContainsString('...', $result);
    }

    public function test_generate_truncates_at_word_boundary_and_adds_ellipsis(): void
    {
        $html = 'The quick brown fox jumps over the lazy dog and then runs away';

        $result = ExcerptGenerator::generate($html, 30);

        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual(33, mb_strlen($result)); // 30 + '...'
        $this->assertStringNotContainsString('  ', $result);
    }

    public function test_generate_decodes_html_entities(): void
    {
        $html = '<p>Tom &amp; Jerry&#039;s &quot;adventure&quot; &lt;fun&gt;</p>';

        $result = ExcerptGenerator::generate($html);

        $this->assertSame('Tom & Jerry\'s "adventure" <fun>', $result);
    }

    public function test_generate_collapses_multiple_whitespace_characters(): void
    {
        $html = "<p>Line one</p>\n\n<p>Line   two</p>\t\t<p>Line three</p>";

        $result = ExcerptGenerator::generate($html);

        $this->assertSame('Line one Line two Line three', $result);
    }
}
