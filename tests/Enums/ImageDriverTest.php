<?php

declare(strict_types=1);

namespace Macocci7\PhpScatterplot\Tests\Enums;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Macocci7\PhpScatterplot\Enums\ImageDriver;

final class ImageDriverTest extends TestCase
{
    protected array $drivers = [
        'Gd' => 'gd',
        'Imagick' => 'imagick',
        'Vips' => 'vips',
    ];

    public function test_cases_can_return_cases_correctly(): void
    {
        $this->assertSame(array_keys($this->drivers), ImageDriver::names());
    }

    public function test_values_can_return_values_correctly(): void
    {
        $this->assertSame(array_values($this->drivers), ImageDriver::values());
    }

    public function test_asArray_can_return_array_correctly(): void
    {
        $this->assertSame($this->drivers, ImageDriver::asArray());
    }

    public function test_get_can_return_enum_correctly(): void
    {
        foreach ($this->drivers as $key => $value) {
            $enum = ImageDriver::get($value);
            $this->assertSame($key, $enum->name);
        }
    }

    public static function provide_classname_can_return_correct_classname(): array
    {
        return [
            'gd' => ['driverName' => 'gd', 'expected' => 'Intervention\\Image\\Drivers\\Gd\\Driver'],
            'imagick' => ['driverName' => 'imagick', 'expected' => 'Intervention\\Image\\Drivers\\Imagick\\Driver'],
            'vips' => ['driverName' => 'vips', 'expected' => 'Intervention\\Image\\Drivers\\Vips\\Driver'],
            'hoge' => ['driverName' => 'hoge', 'expected' => null],
        ];
    }

    #[DataProvider('provide_classname_can_return_correct_classname')]
    public function test_classname_can_return_correct_classname(string $driverName, string|null $expected): void
    {
        $this->assertSame($expected, ImageDriver::tryFrom($driverName)?->classname());
    }
}
