<?php

namespace Macocci7\PhpScatterplot\Enums;

use Macocci7\PhpScatterplot\Traits\EnumTrait;

enum ImageDriver: string
{
    use EnumTrait;

    case Gd = 'gd';
    case Imagick = 'imagick';
    case Vips = 'vips';

    /**
     * returns a fully qualified class name of the image driver
     */
    public function classname(): string
    {
        return match ($this) {
            self::Gd => 'Intervention\Image\Drivers\Gd\Driver',
            self::Imagick => 'Intervention\Image\Drivers\Imagick\Driver',
            self::Vips => 'Intervention\Image\Drivers\Vips\Driver',
        };
    }
}
