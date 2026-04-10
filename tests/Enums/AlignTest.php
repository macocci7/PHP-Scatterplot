<?php

declare(strict_types=1);

namespace Macocci7\PhpScatterplot\Tests\Enums;

use Intervention\Image\Alignment;
use Macocci7\PhpScatterplot\Enums\Align;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AlignTest extends TestCase
{
    protected array $aligns = [
        'Left' => 'left',
        'Center' => 'center',
        'Right' => 'right',
        'Top' => 'top',
        'Middle' => 'middle',
        'Bottom' => 'bottom',
    ];

    public function test_cases_can_return_cases_correctly(): void
    {
        $this->assertSame(array_keys($this->aligns), Align::names());
    }

    public function test_values_can_return_values_correctly(): void
    {
        $this->assertSame(array_values($this->aligns), Align::values());
    }

    public function test_asArray_can_return_array_correctly(): void
    {
        $this->assertSame($this->aligns, Align::asArray());
    }

    public function test_get_can_return_enum_correctly(): void
    {
        foreach ($this->aligns as $key => $value) {
            $enum = Align::get($value);
            $this->assertSame($key, $enum->name);
        }
    }

    public static function provide_parse_can_return_correct_alignment(): array
    {
        return [
            'left' => ['align' => 'left', 'expected' => Alignment::LEFT],
            'center' => ['align' => 'center', 'expected' => Alignment::CENTER],
            'right' => ['align' => 'right', 'expected' => Alignment::RIGHT],
            'top' => ['align' => 'top', 'expected' => Alignment::TOP],
            'middle' => ['align' => 'middle', 'expected' => Alignment::CENTER],
            'bottom' => ['align' => 'bottom', 'expected' => Alignment::BOTTOM],
            'hoge' => ['align' => 'hoge', 'expected' => null],
        ];
    }

    #[DataProvider('provide_parse_can_return_correct_alignment')]
    public function test_parse_can_return_correct_alignment(string $align, Alignment|null $expected): void
    {
        $this->assertSame($expected, Align::parse($align));
    }
}
