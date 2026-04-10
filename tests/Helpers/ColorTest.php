<?php

declare(strict_types=1);

namespace Macocci7\PhpScatterplot\Tests\Helpers;

use Intervention\Image\Color as ImageColor;
use Intervention\Image\Interfaces\ColorInterface;
use Macocci7\PhpScatterplot\Helpers\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ColorTest extends TestCase
{
    public static function provide_parse_can_return_correct_color(): array
    {
        return [
            'null' => ['color' => null, 'expected' => ImageColor::transparent()],
            '#ff0000' => ['color' => '#ff0000', 'expected' => ImageColor::rgb(255, 0, 0)],
            'rgb(255, 0, 0)' => ['color' => 'rgb(255, 0, 0)', 'expected' => ImageColor::rgb(255, 0, 0)],
            'rgba(255, 0, 0, 1)' => ['color' => 'rgba(255, 0, 0, 1)', 'expected' => ImageColor::rgb(255, 0, 0, 1)],
        ];
    }

    #[DataProvider('provide_parse_can_return_correct_color')]
    public function test_parse_can_return_correct_color(string|null $color, ColorInterface $expected): void
    {
        $result = Color::parse($color);
        $this->assertSame($expected->red()->value(), $result->red()->value());
        $this->assertSame($expected->green()->value(), $result->green()->value());
        $this->assertSame($expected->blue()->value(), $result->blue()->value());
        $this->assertSame($expected->alpha()->value(), $result->alpha()->value());
    }
}
