<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpScatterplot\Helpers\Config;
use Macocci7\PhpScatterplot\Plotter;

/**
 * Class for management of scatter plot
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Scatterplot extends Plotter
{
    private int $CANVAS_WIDTH_LIMIT_LOWER;
    private int $CANVAS_HEIGHT_LIMIT_LOWER;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadConf();
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'CANVAS_WIDTH_LIMIT_LOWER',
            'CANVAS_HEIGHT_LIMIT_LOWER',
        ];
        foreach (Config::get('props') as $prop => $value) {
            if (in_array($prop, $props, true)) {
                $this->{$prop} = $value;
            }
        }
    }

    /**
     * set config from specified resource
     * @param   string|mixed[]  $configResource
     * @return  self
     */
    public function config(string|array $configResource)
    {
        foreach (Config::filter($configResource) as $key => $value) {
            $this->{$key} = $value;
            if (strcmp('dataSet', $key) === 0 && empty($this->legends)) {
                $this->legends = array_keys($value);
            }
        }
        return $this;
    }

    /**
     * sets a layer
     * @param   array<string, array<int|float>> $layer
     * @return  self
     */
    public function layer(array $layer)
    {
        if (self::isValidLayer($layer)) {
            $this->layers[] = $layer;
        }
        return $this;
    }

    /**
     * sets layers
     * @param   array<int|string, array<string, list<int|float>>>   $layers
     * @return  self
     */
    public function layers(array $layers)
    {
        if (!self::isValidLayers($layers)) {
            throw new \Exception(
                "Invalid layers specified. "
                . "array<int|string, array<string, list<int|float>>> expected."
            );
        }
        $this->layers = $layers;
        return $this;
    }

    /**
     * sets limits of x
     * @param   int|float   $lower
     * @param   int|float   $upper
     * @return  self
     * @thrown  \Exception
     */
    public function limitX(int|float $lower, int|float $upper)
    {
        if ($lower >= $upper) {
            throw new \Exception("lower limit must be less than upper limit.");
        }
        $this->xLimitUpper = $upper;
        $this->xLimitLower = $lower;
        return $this;
    }

    /**
     * sets limits of y
     * @param   int|float   $lower
     * @param   int|float   $upper
     * @return  self
     * @thrown  \Exception
     */
    public function limitY(int|float $lower, int|float $upper)
    {
        if ($lower >= $upper) {
            throw new \Exception("lower limit must be less than upper limit.");
        }
        $this->yLimitUpper = $upper;
        $this->yLimitLower = $lower;
        return $this;
    }

    /**
     * sets the width and height of the canvas
     * @param   int     $width
     * @param   int     $height
     * @return  self
     * @thrown  \Exception
     */
    public function resize(int $width, int $height)
    {
        if ($width < $this->CANVAS_WIDTH_LIMIT_LOWER) {
            throw new \Exception(
                "width is below the lower limit "
                . $this->CANVAS_WIDTH_LIMIT_LOWER
            );
        }
        if ($height < $this->CANVAS_HEIGHT_LIMIT_LOWER) {
            throw new \Exception(
                "height is below the lower limit "
                . $this->CANVAS_HEIGHT_LIMIT_LOWER
            );
        }
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    /**
     * sets the frame (plot area) ratio
     * @param   float   $xRatio (0.0 < $xRatio < 1.0)
     * @param   float   $yRatio (0.0 < $yRatio < 1.0)
     * @return  self
     * @thrown  \Exception
     */
    public function frame($xRatio, $yRatio)
    {
        if ($xRatio <= 0.0 || $xRatio > 1.0) {
            throw new \Exception("Ratio must be: 0.0 < ratio <= 1.0.");
        }
        if ($yRatio <= 0.0 || $yRatio > 1.0) {
            throw new \Exception("Ratio must be: 0.0 < ratio <= 1.0.");
        }
        $this->frameXRatio = $xRatio;
        $this->frameYRatio = $yRatio;
        return $this;
    }

    /**
     * sets the background color of the canvas
     * @param   string|null $color = null (null results in transparent)
     * @return  self
     */
    public function bgcolor(string|null $color = null)
    {
        if (!self::isColorCode($color) && !is_null($color)) {
            throw new \Exception("param must be null or color code (in '#rgb' or '#rrggbb' format).");
        }
        $this->canvasBackgroundColor = $color;
        return $this;
    }

    /**
     * sets the width and color of the axis
     * @param   int         $width
     * @param   string|null $color
     * @return  self
     * @thrown  \Exception
     */
    public function axis(int $width, string|null $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be positive integer.");
        }
        if (!is_null($color) && !self::isColorCode($color)) {
            throw new \Exception("color code must be null or string (in '#rgb' or '#rrggbb' format).");
        }
        $this->axisWidth = $width;
        if (null !== $color) {
            $this->axisColor = $color;
        }
        return $this;
    }

    /**
     * sets the width and color of the grids
     * @param   int         $width
     * @param   string|null $color
     * @return  self
     * @thrown  \Exception
     */
    public function grid(int $width, string|null $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be positive integer.");
        }
        if (!is_null($color) && !self::isColorCode($color)) {
            throw new \Exception("color code must be null or string (in '#rgb' or '#rrggbb' format).");
        }
        $this->gridWidth = $width;
        if (null !== $color) {
            $this->gridColor = $color;
        }
        return $this;
    }

    /**
     * sets the grid pitch of x
     * @param   int|float   $pitch
     * @return  self
     */
    public function gridXPitch(int|float $pitch)
    {
        if ($pitch <= 0) {
            throw new \Exception("specify positive integer.");
        }
        $this->gridXPitch = $pitch;
        return $this;
    }

    /**
     * sets the grid pitch of y
     * @param   int|float   $pitch
     * @return  self
     */
    public function gridYPitch(int|float $pitch)
    {
        if ($pitch <= 0) {
            throw new \Exception("specify positive integer.");
        }
        $this->gridYPitch = $pitch;
        return $this;
    }

    /**
     * sets the color of dots
     * @param   string[]    $colors
     * @return  self
     * @thrown  \Exception
     */
    public function colors(array $colors)
    {
        if (!self::isColorCodesAll($colors)) {
            throw new \Exception("color codes must be in '#rgb' or '#rrggbb' format.");
        }
        foreach (
            array_slice(array_values($colors), 0, LIMIT_LAYERS) as $i => $color
        ) {
            $this->colors[$i] = $color;
        }
        return $this;
    }

    /**
     * sets the size (diameter) of dots in pixels
     * @param   int     $size
     * @return  self
     * @thrown  \Exception
     */
    public function plotSize(int $size)
    {
        if ($size < 1) {
            throw new \Exception("size must be positive integer.");
        }
        $this->plotDiameter = $size;
        return $this;
    }

    /**
     * sets the font path
     * @param   string  $path
     * @return  self
     * @thrown  \Exception
     */
    public function fontPath(string $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("File does not exists.");
        }
        if (!is_readable($path)) {
            throw new \Exception("Cannot read the file.");
        }
        $pathinfo = pathinfo($path);
        if (0 !== strcmp("ttf", strtolower($pathinfo['extension']))) {
            throw new \Exception("Specify .ttf file.");
        }
        $this->fontPath = $path;
        return $this;
    }

    /**
     * sets the font size
     * @param   int|float   $size
     * @return  self
     * @thrown  \Exception
     */
    public function fontSize(int|float $size)
    {
        if ($size < 6) {
            throw new \Exception("Size must be 6 or above.");
        }
        $this->fontSize = $size;
        return $this;
    }

    /**
     * sets the font color
     * @param   string  $color
     * @return  self
     * @thrown  \Exception
     */
    public function fontColor(string $color)
    {
        if (!self::isColorCode($color)) {
            throw new \Exception("Color code must be in '#rgb' or '#rrggbb' format.");
        }
        $this->fontColor = $color;
        return $this;
    }

    /**
     * sets x, width and color of the reference line of x
     * @param   int|float   $x
     * @param   int         $width
     * @param   string      $color = '#0000ff'
     * @return  self
     * @thrown  \Exception
     */
    public function referenceLineX(int|float $x, int $width = 1, string $color = '#0000ff')
    {
        if ($width < 1) {
            throw new \Exception("Width must be positive integer.");
        }
        if (!self::isColorCode($color)) {
            throw new \Exception("Color code must be in '#rgb' or '#rrggbb' format.");
        }
        $this->referenceLineX = true;
        $this->referenceLineXValue = $x;
        $this->referenceLineXWidth = $width;
        $this->referenceLineXColor = $color;
        return $this;
    }

    /**
     * sets y, width and color of the reference line of y
     * @param   int|float   $y
     * @param   int         $width
     * @param   string      $color = '#0000ff'
     * @return  self
     * @thrown  \Exception
     */
    public function referenceLineY(int|float $y, int $width = 1, string $color = '#0000ff')
    {
        if ($width < 1) {
            throw new \Exception("Width must be positive integer.");
        }
        if (!self::isColorCode($color)) {
            throw new \Exception("Color code must be in '#rgb' or '#rrggbb' format.");
        }
        $this->referenceLineY = true;
        $this->referenceLineYValue = $y;
        $this->referenceLineYWidth = $width;
        $this->referenceLineYColor = $color;
        return $this;
    }

    /**
     * sets the specification limits of x
     * @param   int|float   $lower
     * @param   int|float   $upper
     * @param   int         $width = 1
     * @param   string      $color = '#ff00ff'
     * @return  self
     * @thrown  \Exception
     */
    public function specificationLimitX(int|float $lower, int|float $upper, int $width = 1, string $color = '#ff00ff')
    {
        if ($lower >= $upper) {
            throw new \Exception("The lower and upper limits are opposite in size.");
        }
        if ($width < 1) {
            throw new \Exception("Width must be positive integer.");
        }
        if (!self::isColorCode($color)) {
            throw new \Exception("Color code msut be in '#rgb' or '#rrggbb' format.");
        }
        $this->specificationLimitX = true;
        $this->specificationLimitXLower = $lower;
        $this->specificationLimitXUpper = $upper;
        $this->specificationLimitXWidth = $width;
        $this->specificationLimitXColor = $color;
        return $this;
    }

    /**
     * sets the specification limits of y
     * @param   int|float   $lower
     * @param   int|float   $upper
     * @param   int         $width = 1
     * @param   string      $color = '#ff00ff'
     * @return  self
     * @thrown  \Exception
     */
    public function specificationLimitY(int|float $lower, int|float $upper, int $width = 1, string $color = '#ff00ff')
    {
        if ($lower >= $upper) {
            throw new \Exception("The lower and upper limits are opposite in size.");
        }
        if ($width < 1) {
            throw new \Exception("Width must be positive integer.");
        }
        if (!self::isColorCode($color)) {
            throw new \Exception("Color code msut be in '#rgb' or '#rrggbb' format.");
        }
        $this->specificationLimitY = true;
        $this->specificationLimitYLower = $lower;
        $this->specificationLimitYUpper = $upper;
        $this->specificationLimitYWidth = $width;
        $this->specificationLimitYColor = $color;
        return $this;
    }

    /**
     * sets the width and color of the regression line
     * @param   int         $width
     * @param   string[]    $colors
     * @return  self
     * @thrown  \Exception
     */
    public function regressionLine(int $width, array $colors)
    {
        if ($width < 1) {
            throw new \Exception("Width must be positive integer.");
        }
        if (!self::isColorCodesAll($colors)) {
            throw new \Exception("Color codes must be in '#rgb' or '#rrggbb' format.");
        }
        $this->regressionLineColors = array_slice(
            array_values($colors),
            0,
            LIMIT_LAYERS
        );
        $this->regressionLine = true;
        $this->regressionLineWidth = $width;
        return $this;
    }

    /**
     * sets the label of x
     * @param   string  $label
     * @return  self
     */
    public function labelX(string $label)
    {
        $this->labelX = $label;
        return $this;
    }

    /**
     * sets the label of y
     * @param   string  $label
     * @return  self
     */
    public function labelY(string $label)
    {
        $this->labelY = $label;
        return $this;
    }

    /**
     * sets the caption
     * @param   string  $caption
     * @return  self
     */
    public function caption(string $caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * sets the legends
     * @param   string[]    $legends
     * @return  self
     */
    public function legends(array $legends)
    {
        if (!self::isStringsAll($legends)) {
            throw new \Exception("Each elements of legends must be type of string.");
        }
        $this->legends = $legends;
        $this->legend = true;
        return $this;
    }

    /**
     * returns the config values
     * @param   string|null $key = null
     * @return  mixed
     */
    public function getConfig(string|null $key = null)
    {
        if (is_null($key)) {
            $config = [];
            foreach (array_keys(Config::get('validConfig')) as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (in_array($key, array_keys(Config::get('validConfig')))) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * sets grid of x on
     * @return  self
     */
    public function gridXOn()
    {
        $this->gridX = true;
        return $this;
    }

    /**
     * sets grid of x off
     * @return self
     */
    public function gridXOff()
    {
        $this->gridX = false;
        return $this;
    }

    /**
     * sets grid of y on
     * @return self
     */
    public function gridYOn()
    {
        $this->gridY = true;
        return $this;
    }

    /**
     * sets grid of y off
     * @return self
     */
    public function gridYOff()
    {
        $this->gridY = false;
        return $this;
    }

    /**
     * sets reference line of x off
     * @return self
     */
    public function referenceLineXOff()
    {
        $this->referenceLineX = false;
        return $this;
    }

    /**
     * sets reference line of y off
     * @return self
     */
    public function referenceLineYOff()
    {
        $this->referenceLineY = false;
        return $this;
    }

    /**
     * sets reference lines off
     * @return self
     */
    public function referenceLinesOff()
    {
        $this->referenceLineXOff();
        $this->referenceLineYOff();
        return $this;
    }

    /**
     * sets specification limit of x off
     * @return self
     */
    public function specificationLimitXOff()
    {
        $this->specificationLimitX = false;
        return $this;
    }

    /**
     * sets specification limit of y off
     * @return self
     */
    public function specificationLimitYOff()
    {
        $this->specificationLimitY = false;
        return $this;
    }

    /**
     * sets specification limits off
     * @return self
     */
    public function specificationLimitsOff()
    {
        $this->specificationLimitXOff();
        $this->specificationLimitYOff();
        return $this;
    }

    /**
     * sets regression line on
     * @return  self
     */
    public function regressionLineOn()
    {
        $this->regressionLine = true;
        return $this;
    }

    /**
     * sets regression line off
     * @return self
     */
    public function regressionLineOff()
    {
        $this->regressionLine = false;
        return $this;
    }

    /**
     * sets legend off
     * @return self
     */
    public function legendOff()
    {
        $this->legend = false;
        return $this;
    }
}
