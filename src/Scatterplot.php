<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpScatterplot\Plotter;

/**
 * Class for management of scatter plot
 */
class Scatterplot extends Plotter
{
    private $validConfig = [
        'canvasWidth',
        'canvasHeight',
        'canvasBackgroundColor',
        'frameXRatio',
        'frameYRatio',
        'axisColor',
        'axisWidth',
        'gridColor',
        'gridWidth',
        'gridXPitch',
        'gridYPitch',
        'gridXMax',
        'gridXMin',
        'gridYMax',
        'gridYMin',
        'gridX',
        'gridY',
        'xLimitUpper',
        'xLimitLower',
        'yLimitUpper',
        'yLimitLower',
        'plotDiameter',
        'plotColor',
        'fontPath',
        'fontSize',
        'fontColor',
        'outlierDiameter',
        'outlierColor',
        'mean',
        'meanColor',
        'labels',
        'labelX',
        'labelY',
        'caption',
        'legend',
        'legendCount',
        'legends',
        'legendWidth',
        'legendFontSize',
        'colors',
    ];

    /**
     * sets a layer
     * @param array $layer
     * @return self
     */
    public function layer($layer)
    {
        if (!$this->isValidLayer($layer)) {
            return;
        }
        $this->layers[] = $layer;
        return $this;
    }

    /**
     * sets layers
     * @param array $layers
     * @return self
     */
    public function layers($layers)
    {
        if (!$this->isValidLayers($layers)) {
            return;
        }
        $this->layers = $layers;
        return $this;
    }

    /**
     * sets limits of x
     * @param float $lower
     * @param float $upper
     * @return self
     */
    public function limitX($lower, $upper)
    {
        if (!is_int($lower) && !s_float($lower)) {
            return;
        }
        if (!is_int($upper) && !is_float($upper)) {
            return;
        }
        if ($lower >= $upper) {
            return;
        }
        $this->xLimitUpper = $upper;
        $this->xLimitLower = $lower;
        return $this;
    }

    /**
     * sets limits of y
     * @param float $lower
     * @param float $upper
     * @return self
     */
    public function limitY($lower, $upper)
    {
        if (!is_int($lower) && !s_float($lower)) {
            return;
        }
        if (!is_int($upper) && !is_float($upper)) {
            return;
        }
        if ($lower >= $upper) {
            return;
        }
        $this->yLimitUpper = $upper;
        $this->yLimitLower = $lower;
        return $this;
    }

    /**
     * sets the width and height of the canvas
     * @param integer $width
     * @param integer $height
     * @return self
     */
    public function resize($width, $height)
    {
        if (!is_int($width) || !is_int($height)) {
            return;
        }
        if ($width < 100 || $height < 100) {
            return;
        }
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    /**
     * sets the frame (plot area) ratio
     * @param float $xRatio
     * @param float $yRatio
     * @return self
     */
    public function frame($xRatio, $yRatio)
    {
        if (!is_float($xRatio) || !is_float($yRatio)) {
            return;
        }
        if ($xRatio <= 0.0 || $xRatio > 1.0) {
            return;
        }
        if ($yRatio <= 0.0 || $yRatio > 1.0) {
            return;
        }
        $this->frameXRatio = $xRatio;
        $this->frameYRatio = $yRatio;
        return $this;
    }

    /**
     * sets the background color of the canvas
     * @param string $color
     * @return self
     */
    public function bgcolor($color)
    {
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->canvasBackgroundColor = $color;
        return $this;
    }

    /**
     * sets the width and color of the axis
     * @param integer $width
     * @param string $color
     * @return self
     */
    public function axis($width, $color = null)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (null !== $color && !$this->isColorCode($color)) {
            return;
        }
        $this->axisWidth = $width;
        if (null !== $color) {
            $this->axisColor = $color;
        }
        return $this;
    }

    /**
     * sets the width and color of the grids
     * @param integer $width
     * @param string $color
     * @return self
     */
    public function grid($width, $color = null)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (null !== $color && !$this->isColorCode($color)) {
            return;
        }
        $this->gridWidth = $width;
        if (null !== $color) {
            $this->gridColor = $color;
        }
        return $this;
    }

    /**
     * sets the grid pitch of x
     * @param float $pitch
     * @return self
     */
    public function gridXPitch($pitch)
    {
        if (!is_int($pitch) && !is_float($pitch)) {
            return;
        }
        if ($pitch <= 0) {
            return;
        }
        $this->gridXPitch = $pitch;
        return $this;
    }

    /**
     * sets the grid pitch of y
     * @param float $pitch
     * @return self
     */
    public function gridYPitch($pitch)
    {
        if (!is_int($pitch) && !is_float($pitch)) {
            return;
        }
        if ($pitch <= 0) {
            return;
        }
        $this->gridYPitch = $pitch;
        return $this;
    }

    /**
     * sets the color of dots
     * @param array $color
     * @return self
     */
    public function colors($colors)
    {
        if (!is_array($colors)) {
            return;
        }
        foreach ($colors as $index => $color) {
            if (!is_int($index)) {
                return;
            }
            if ($index < 0 || $index > LIMIT_LAYERS) {
                return;
            }
            if (!$this->isColorCode($color)) {
                return;
            }
            $this->colors[$index] = $color;
        }
        return $this;
    }

    /**
     * sets the size (diameter) of dots in pixels
     * @param integer $size
     * @return self
     */
    public function plotSize($size)
    {
        if (!is_int($size)) {
            return;
        }
        if ($size < 1) {
            return;
        }
        $this->plotDiameter = $size;
        return $this;
    }

    /**
     * sets the font path
     * @param string $path
     * @return self
     */
    public function fontPath($path)
    {
        if (!is_string($path)) {
            return;
        }
        if (strlen($path) < 5) {
            return;
        }
        if (!file_exists($path)) {
            return;
        }
        $pathinfo = pathinfo($path);
        if (0 !== strcmp("ttf", strtolower($pathinfo['extension']))) {
            return;
        }
        $this->fontPath = $path;
        return $this;
    }

    /**
     * sets the font size
     * @param integer $size
     * @return self
     */
    public function fontSize($size)
    {
        if (!is_int($size)) {
            return;
        }
        if ($size < 6) {
            return;
        }
        $this->fontSize = $size;
        return $this;
    }

    /**
     * sets the font color
     * @param string $color
     * @return self
     */
    public function fontColor($color)
    {
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->fontColor = $color;
        return $this;
    }

    /**
     * sets x, width and color of the reference line of x
     * @param float $x
     * @param integer $width
     * @param string $color
     * @return self
     */
    public function referenceLineX($x, $width = 1, $color = '#0000ff')
    {
        if (!is_int($x) && !is_float($x)) {
            return;
        }
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->referenceLineX = true;
        $this->referenceLineXValue = $x;
        $this->referenceLineXWidth = $width;
        $this->referenceLineXColor = $color;
        return $this;
    }

    /**
     * sets y, width and color of the reference line of y
     * @param float $y
     * @param integer $width
     * @param string $color
     * @return self
     */
    public function referenceLineY($y, $width = 1, $color = '#0000ff')
    {
        if (!is_int($y) && !is_float($y)) {
            return;
        }
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->referenceLineY = true;
        $this->referenceLineYValue = $y;
        $this->referenceLineYWidth = $width;
        $this->referenceLineYColor = $color;
        return $this;
    }

    /**
     * sets the specification limits of x
     * @param float $lower
     * @param float $upper
     * @param integer $width
     * @param string $color
     * @return self
     */
    public function specificationLimitX($lower, $upper, $width = 1, $color = '#ff00ff')
    {
        if (!is_int($lower) && !is_float($lower)) {
            return;
        }
        if (!is_int($upper) && !is_float($upper)) {
            return;
        }
        if ($lower >= $upper) {
            return;
        }
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
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
     * @param float $lower
     * @param float $upper
     * @param integer $width
     * @param string $color
     * @return self
     */
    public function specificationLimitY($lower, $upper, $width = 1, $color = '#ff00ff')
    {
        if (!is_int($lower) && !is_float($lower)) {
            return;
        }
        if (!is_int($upper) && !is_float($upper)) {
            return;
        }
        if ($lower >= $upper) {
            return;
        }
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
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
     * @param integer $width
     * @param array $color
     * @return self
     */
    public function regressionLine($width, $colors)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!is_array($colors)) {
            return;
        }
        foreach ($colors as $index => $color) {
            if (!is_int($index)) {
                return;
            }
            if ($index < 0 || $index > LIMIT_LAYERS) {
                return;
            }
            if (!$this->isColorCode($color)) {
                return;
            }
            $this->regressionLineColors[$index] = $color;
        }
        $this->regressionLine = true;
        $this->regressionLineWidth = $width;
        return $this;
    }

    /**
     * sets the label of x
     * @param string $label
     * @return self
     */
    public function labelX($label)
    {
        if (!is_string($label)) {
            return;
        }
        $this->labelX = $label;
        return $this;
    }

    /**
     * sets the label of y
     * @param string $label
     * @return self
     */
    public function labelY($label)
    {
        if (!is_string($label)) {
            return;
        }
        $this->labelY = $label;
        return $this;
    }

    /**
     * sets the caption
     * @param string $caption
     * @return self
     */
    public function caption($caption)
    {
        if (!is_string($caption)) {
            return;
        }
        $this->caption = $caption;
        return $this;
    }

    /**
     * sets the legends
     * @param array $legends
     * @return self
     */
    public function legends($legends)
    {
        if (!is_array($legends)) {
            return;
        }
        $this->legends = $legends;
        $this->legend = true;
        return $this;
    }

    /**
     * returns the config values
     * @param string $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if (null === $key) {
            $config = [];
            foreach ($this->validConfig as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (in_array($key, $this->validConfig)) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * sets grid of x on
     * @param
     * @return self
     */
    public function gridXOn()
    {
        $this->gridX = true;
        return $this;
    }

    /**
     * sets grid of x off
     * @param
     * @return self
     */
    public function gridXOff()
    {
        $this->gridX = false;
        return $this;
    }

    /**
     * sets grid of y on
     * @param
     * @return self
     */
    public function gridYOn()
    {
        $this->gridY = true;
        return $this;
    }

    /**
     * sets grid of y off
     * @param
     * @return self
     */
    public function gridYOff()
    {
        $this->gridY = false;
        return $this;
    }

    /**
     * sets reference line of x off
     * @param
     * @return self
     */
    public function referenceLineXOff()
    {
        $this->referenceLineX = false;
        return $this;
    }

    /**
     * sets reference line of y off
     * @param
     * @return self
     */
    public function referenceLineYOff()
    {
        $this->referenceLineY = false;
        return $this;
    }

    /**
     * sets reference lines off
     * @param
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
     * @param
     * @return self
     */
    public function specificationLimitXOff()
    {
        $this->specificationLimitX = false;
        return $this;
    }

    /**
     * sets specification limit of y off
     * @param
     * @return self
     */
    public function specificationLimitYOff()
    {
        $this->specificationLimitY = false;
        return $this;
    }

    /**
     * sets specification limits off
     * @param
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
     */
    public function regressionLineOn()
    {
        $this->regressionLine = true;
        return $this;
    }

    /**
     * sets regression line off
     * @param
     * @return self
     */
    public function regressionLineOff()
    {
        $this->regressionLine = false;
        return $this;
    }

    /**
     * sets legend off
     * @param
     * @return self
     */
    public function legendOff()
    {
        $this->legend = false;
        return $this;
    }
}
