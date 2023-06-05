<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpScatterplot\Analyzer;
use Intervention\Image\ImageManagerStatic as Image;

class Plotter extends Analyzer
{
    protected $layers;
    protected $image;
    protected $canvasWidth = 600;
    protected $canvasHeight = 500;
    protected $canvasBackgroundColor = '#ffffff';
    protected $frameXRatio = 0.8;
    protected $frameYRatio = 0.7;
    protected $axisColor = '#666666';
    protected $axisWidth = 1;
    protected $gridColor = '#999999';
    protected $gridWidth = 1;
    protected $gridWidthPitch;
    protected $gridHeightPitch;
    protected $pixGridWidth;
    protected $pixGridHeight;
    protected $gridHeightMax;
    protected $gridHeightMin;
    protected $gridWidthMax;
    protected $gridWidthMin;
    protected $gridVertical = false;
    protected $gridHorizontal = false;
    protected $vLimitUpper;
    protected $vLmitLower;
    protected $hLimitUpper;
    protected $hLimitLower;
    protected $plotDiameter = 2;
    protected $plotColor = '#000000';
    protected $pixHeightPitch;
    protected $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //protected $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    protected $fontSize = 16;
    protected $fontColor = '#333333';
    protected $baseX;
    protected $baseY;
    protected $outlier = true;
    protected $outlierDiameter = 2;
    protected $outlierColor = '#ff0000';
    protected $mean = false;
    protected $meanColor = '#0000ff';
    protected $labels;
    protected $labelX;
    protected $labelY;
    protected $caption;
    protected $legend = false;
    protected $legendCount;
    protected $legends;
    protected $legendWidth = 100;
    protected $legendFontSize = 10;
    protected $colors = [
        '#9999cc',
        '#cc9999',
        '#99cc99',
        '#99cccc',
        '#cc6666',
        '#ffcc99',
        '#cccc99',
        '#cc99cc',
    ];

    public function __construct()
    {
        Image::configure(['driver' => 'imagick']);
    }

    protected function setProperties()
    {
        $this->ft = new FrequencyTable();
        $this->legendCount = count($this->layers);
        if (!$this->boxBackgroundColor) $this->boxBackgroundColor = $this->colors;
        $counts = [];
        foreach ($this->layers as $values) {
            $counts[] = count($values);
        }
        $this->boxCount = max($counts);
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) * 3 / 4);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
        $maxValue = $this->layerMax($this->layer);
        $minValue = $this->layerMin($this->layer);
        if (isset($this->limitUpper)) {
            $this->gridMax = $this->limitUpper;
        } else {
            $this->gridMax = ((int) ($maxValue + ($maxValue - $minValue) * 0.1) * 10 ) / 10;
        }
        if (isset($this->limitLower)) {
            $this->gridMin = $this->limitLower;
        } else {
            $this->gridMin = ((int) ($minValue - ($maxValue - $minValue) * 0.1) * 10 ) / 10;
        }
        $gridHeightSpan = $this->gridMax - $this->gridMin;
        // Note:
        // - The Class Range affects the accuracy of the Mean Value.
        // - This value should be set appropriately: 10% of $gridHeightSpan in this case.
        $clsasRange = ((int) ($gridHeightSpan * 10)) / 100;
        $this->ft->setClassRange($clsasRange);
        $this->pixHeightPitch = $this->canvasHeight * $this->frameYRatio / ($this->gridMax - $this->gridMin);
        // Note:
        // - If $this->gridHeightPitch has a value, that value takes precedence.
        // - The value of $this->girdHeightPitch may be set by the funciton gridHeightPitch().
        if (!$this->gridHeightPitch) {
            $this->gridHeightPitch = 1;
            if ($this->gridHeightPitch < 0.125 * $gridHeightSpan)
                $this->gridHeightPitch = ( (int) (0.125 * $gridHeightSpan * 10)) / 10;
            if ($this->gridHeightPitch > 0.2 * $gridHeightSpan)
                $this->gridHeightPitch = ( (int) (0.200 * $gridHeightSpan * 10)) / 10;
        }
        $this->pixGridWidth = $this->canvasWidth * $this->frameXRatio / $this->boxCount;
        // Creating an instance of intervention/image.
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
        // Note:
        // - If $this->labels has values, those values takes precedence.
        // - The values of $this->labels may be set by the function labels().
        if (empty($this->labels)) $this->labels = array_keys($this->data[0]);
        return $this;
    }

    public function limit($lower, $upper)
    {
        if (!is_int($lower) && !s_float($lower)) return;
        if (!is_int($upper) && !is_float($upper)) return;
        if ($lower >= $upper) return;
        $this->limitUpper = $upper;
        $this->limitLower = $lower;
        return $this;
    }

}