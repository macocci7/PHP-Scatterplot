<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpScatterplot\Helpers\Config;
use Macocci7\PhpScatterplot\Traits\JudgeTrait;
use Macocci7\PhpScatterplot\Analyzer;
use Macocci7\PhpFrequencyTable\FrequencyTable;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class for plotting
 */
class Plotter extends Analyzer
{
    use JudgeTrait;

    protected $imageDriver = 'imagick';
    protected $layers;
    protected $image;
    protected $canvasWidth = 600;
    protected $canvasHeight = 500;
    protected $canvasBackgroundColor = '#ffffff';
    protected $frameXRatio = 0.8;
    protected $frameYRatio = 0.7;
    protected $axisColor = '#666666';
    protected $axisWidth = 1;
    protected $gridColor = '#dddddd';
    protected $gridWidth = 1;
    protected $gridXPitch;
    protected $gridYPitch;
    protected $gridXMax;
    protected $gridXMin;
    protected $gridYMax;
    protected $gridYMin;
    protected $gridX = false;
    protected $gridY = false;
    protected $pixPitchX;
    protected $pixPitchY;
    protected $xLimitUpper;
    protected $xLimitLower;
    protected $yLimitUpper;
    protected $yLimitLower;
    protected $plotDiameter = 2;
    protected $plotColors = '#000000';
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
    protected $referenceLineX = false;
    protected $referenceLineXValue;
    protected $referenceLineXWidth = 1;
    protected $referenceLineXColor = '#0000ff';
    protected $referenceLineY = false;
    protected $referenceLineYValue;
    protected $referenceLineYWidth = 1;
    protected $referenceLineYColor = '#0000ff';
    protected $specificationLimitX = false;
    protected $specificationLimitXLower;
    protected $specificationLimitXUpper;
    protected $specificationLimitXWidth = 1;
    protected $specificationLimitXColor = '#ff0000';
    protected $specificationLimitY = false;
    protected $specificationLimitYLower;
    protected $specificationLimitYUpper;
    protected $specificationLimitYWidth = 1;
    protected $specificationLimitYColor = '#ff0000';
    protected $regressionLine = false;
    protected $regressionLineWidth = 2;
    protected $regressionLineColor = '#00cc00';
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
        '#3333cc',
        '#cc3333',
        '#339933',
        '#33cccc',
        '#cc3333',
        '#ffcc33',
        '#cccc33',
        '#cc33cc',
    ];
    protected $regressionLineColors = [
        '#ff0000',
        '#ff6666',
        '#ff9933',
        '#ff0066',
        '#ff00cc',
        '#ff6600',
        '#ffcc00',
        '#ff00ff',
    ];

    /**
     * constructor
     * @param
     * @return
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadConf();
        Image::configure(['driver' => 'imagick']);
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'imageDriver',
            'layers',
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
            'pixPitchX',
            'pixPitchY',
            'xLimitUpper',
            'xLimitLower',
            'yLimitUpper',
            'yLimitLower',
            'plotDiameter',
            'plotColors',
            'fontPath',
            '#fontPath',
            'fontSize',
            'fontColor',
            'outlier',
            'outlierDiameter',
            'outlierColor',
            'mean',
            'meanColor',
            'referenceLineX',
            'referenceLineXValue',
            'referenceLineXWidth',
            'referenceLineXColor',
            'referenceLineY',
            'referenceLineYValue',
            'referenceLineYWidth',
            'referenceLineYColor',
            'specificationLimitX',
            'specificationLimitXLower',
            'specificationLimitXUpper',
            'specificationLimitXWidth',
            'specificationLimitXColor',
            'specificationLimitY',
            'specificationLimitYLower',
            'specificationLimitYUpper',
            'specificationLimitYWidth',
            'specificationLimitYColor',
            'regressionLine',
            'regressionLineWidth',
            'regressionLineColor',
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
            'regressionLineColors',
        ];
        foreach (Config::get('props') as $prop => $value) {
            if (in_array($prop, $props, true)) {
                $this->{$prop} = $value;
            }
        }
    }

    /**
     * sets properties for preparation
     * @return self
     */
    protected function setProperties()
    {
        $this->ft = new FrequencyTable();
        $this->legendCount = count($this->layers);
        $counts = [];
        foreach ($this->layers as $values) {
            $counts[] = count($values);
        }
        $this->boxCount = max($counts);
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) * 3 / 4);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
        list($xMax, $yMax) = $this->layerMax($this->layers);
        list($xMin, $yMin) = $this->layerMin($this->layers);
        if (isset($this->xLimitUpper)) {
            $this->gridXMax = $this->xLimitUpper;
        } else {
            $this->gridXMax = (int) (($xMax + ($xMax - $xMin) * 0.1) * 10 ) / 10;
        }
        if (isset($this->xLimitLower)) {
            $this->gridXMin = $this->xLimitLower;
        } else {
            $this->gridXMin = (int) (($xMin - ($xMax - $xMin) * 0.1) * 10 ) / 10;
        }
        if (isset($this->yLimitUpper)) {
            $this->gridYMax = $this->yLimitUpper;
        } else {
            $this->gridYMax = (int) (($yMax + ($yMax - $yMin) * 0.1) * 10 ) / 10;
        }
        if (isset($this->yLimitLower)) {
            $this->gridYMin = $this->yLimitLower;
        } else {
            $this->gridYMin = (int) (($yMin - ($yMax - $yMin) * 0.1) * 10 ) / 10;
        }
        $gridXRange = $this->gridXMax - $this->gridXMin;
        $gridYRange = $this->gridYMax - $this->gridYMin;
        $this->pixPitchX = $this->canvasWidth * $this->frameXRatio / $gridXRange;
        $this->pixPitchY = $this->canvasHeight * $this->frameYRatio / $gridYRange;
        $this->ft->setClassRange(1);
        // Note:
        // - If $this->gridXPitch has a value, that value takes precedence.
        // - The value of $this->girdXPitch may be set by the funciton gridXPitch().
        if (!$this->gridXPitch) {
            $this->gridXPitch = 1;
            if ($this->gridXPitch < 0.125 * $gridXRange) {
                $this->gridXPitch = ( (int) (0.125 * $gridXRange * 10)) / 10;
            }
            if ($this->gridXPitch > 0.2 * $gridXRange) {
                $this->gridXPitch = ( (int) (0.200 * $gridXRange * 10)) / 10;
            }
        }
        if (!$this->gridYPitch) {
            $this->gridYPitch = 1;
            if ($this->gridYPitch < 0.125 * $gridYRange) {
                $this->gridYPitch = ( (int) (0.125 * $gridYRange * 10)) / 10;
            }
            if ($this->gridYPitch > 0.2 * $gridYRange) {
                $this->gridYPitch = ( (int) (0.200 * $gridYRange * 10)) / 10;
            }
        }
        // Creating an instance of intervention/image.
        $this->image = Image::canvas(
            $this->canvasWidth,
            $this->canvasHeight,
            $this->canvasBackgroundColor
        );
        // Note:
        // - If $this->labels has values, those values takes precedence.
        // - The values of $this->labels may be set by the function labels().
        if (empty($this->labels)) {
            $keys = array_keys($this->layers);
            $this->labels = array_keys($this->layers[$keys[0]]);
        }
        return $this;
    }

    /**
     * calculates the x-coordinate in pixels
     * @param   float   $x
     * @return  int
     */
    public function pX(float $x)
    {
        return (int) ($this->baseX + ($x - $this->gridXMin) * $this->pixPitchX);
    }

    /**
     * calculates the y-coordinate in pixels
     * @param   float   $y
     * @return  int
     */
    public function pY(float $y)
    {
        return (int) ($this->baseY - ($y - $this->gridYMin) * $this->pixPitchY);
    }

    /**
     * plots axis
     * @return  self
     */
    public function plotAxis()
    {
        // horizontal axis
        $x1 = (int) $this->pX($this->gridXMin);
        $y1 = (int) $this->pY($this->gridYMin);
        $x2 = (int) $this->pX($this->gridXMax);
        $y2 = (int) $y1;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->axisColor);
                $draw->width($this->axisWidth);
            }
        );
        // vertical axis
        $x1 = (int) $this->pX($this->gridXMin);
        $y1 = (int) $this->pY($this->gridYMax);
        $x2 = (int) $x1;
        $y2 = (int) $this->pY($this->gridYMin);
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->axisColor);
                $draw->width($this->axisWidth);
            }
        );
        return $this;
    }

    /**
     * plots x-grids
     * @return  self
     */
    public function plotGridsX()
    {
        if (!$this->gridX) {
            return $this;
        }
        for ($i = $this->gridXMin; $i <= $this->gridXMax; $i += $this->gridXPitch) {
            $x1 = (int) $this->pX($i);
            $y1 = (int) $this->pY($this->gridYMax);
            $x2 = (int) $x1;
            $y2 = (int) $this->pY($this->gridYMin);
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->gridColor);
                    $draw->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots y-grids
     * @return  self
     */
    public function plotGridsY()
    {
        if (!$this->gridY) {
            return $this;
        }
        for ($i = $this->gridYMin; $i <= $this->gridYMax; $i += $this->gridYPitch) {
            $x1 = (int) $this->pX($this->gridXMin);
            $y1 = (int) $this->pY($i);
            $x2 = (int) $this->pX($this->gridXMax);
            $y2 = (int) $y1;
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->gridColor);
                    $draw->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots grid values of x
     * @return  self
     */
    public function plotGridValuesX()
    {
        for ($i = $this->gridXMin; $i <= $this->gridXMax; $i += $this->gridXPitch) {
            $x = $this->pX($i);
            $y = $this->baseY + $this->fontSize * 1.2;
            $this->image->text(
                (string) $i,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
        return $this;
    }

    /**
     * plots grid values of y
     * @return  self
     */
    public function plotGridValuesY()
    {
        for ($i = $this->gridYMin; $i <= $this->gridYMax; $i += $this->gridYPitch) {
            $x = $this->baseX - $this->fontSize * 0.4;
            $y = $this->pY($i) + $this->fontSize * 0.4;
            $this->image->text(
                (string) $i,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('right');
                    $font->valign('bottom');
                }
            );
        }
        return $this;
    }

    /**
     * plots x-label
     * @return  self
     */
    public function plotLabelX()
    {
        $x = (int) $this->canvasWidth / 2;
        $y = $this->baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3 ;
        $this->image->text(
            (string) $this->labelX,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        return $this;
    }

    /**
     * plots y-label
     * @return  self
     */
    public function plotLabelY()
    {
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        $image = Image::canvas($width, $height, $this->canvasBackgroundColor);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $image->text(
            (string) $this->labelY,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        $image->rotate(90);
        $this->image->insert($image, 'left');
        return $this;
    }

    /**
     * plots caption
     * @return  self
     */
    public function plotCaption()
    {
        $x = $this->canvasWidth / 2;
        $y = $this->canvasHeight * (1 - $this->frameYRatio) / 3;
        $this->image->text(
            (string) $this->caption,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        return $this;
    }

    /**
     * plots layers
     * @return  self
     */
    public function plotLayers()
    {
        if (!self::isValidLayers($this->layers)) {
            return $this;
        }
        $i = 0;
        foreach ($this->layers as $layer => $data) {
            $this->plotColor = $this->colors[$i];
            $this->regressionLineColor = $this->regressionLineColors[$i];
            $this->plotLayer($data);
            $this->plotRegressionLine($data);
            $i++;
        }
        return $this;
    }

    /**
     * plots layer
     * @param   array<string, array<int|float>> $data
     * @return  self
     */
    public function plotLayer(array $data)
    {
        $count = count($data['x']);
        for ($i = 0; $i < $count; $i++) {
            $this->plotXY($data['x'][$i], $data['y'][$i]);
        }
        return $this;
    }

    /**
     * plots a dot
     * @param   int|float   $x
     * @param   int|float   $y
     * @return  self
     */
    public function plotXY(int|float $x, int|float $y)
    {
        if (!self::isNumber($x) || !self::isNumber($y)) {
            return $this;
        }
        $px = $this->pX($x);
        $py = $this->pY($y);
        $this->image->circle(
            $this->plotDiameter,
            $px,
            $py,
            function ($draw) {
                $draw->background($this->plotColor);
            }
        );
        return $this;
    }

    /**
     * plots a regression line
     * @param   array<int|string, array<string, array<int|float>>>   $layer
     * @return  self
     */
    public function plotRegressionLine(array $layer)
    {
        if (!$this->regressionLine) {
            return $this;
        }
        if (!self::isValidLayer($layer)) {
            return $this;
        }
        $formula = $this->regressionLineFormula($layer['x'], $layer['y']);
        $a = $formula['a'];
        $b = $formula['b'];
        $xMin = min($layer['x']);
        $xMax = max($layer['x']);
        $x1 = $this->pX($xMin);
        $y1 = $this->pY($a * $xMin + $b);
        $x2 = $this->pX($xMax);
        $y2 = $this->pY($a * $xMax + $b);
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->regressionLineWidth);
                $draw->color($this->regressionLineColor);
            }
        );
        return $this;
    }

    /**
     * plots a reference line of x
     * @return  self
     */
    public function plotReferenceLineX()
    {
        if (!$this->referenceLineX) {
            return $this;
        }
        $x1 = (int) $this->pX($this->referenceLineXValue);
        $y1 = (int) $this->pY($this->gridYMax);
        $x2 = (int) $x1;
        $y2 = (int) $this->pY($this->gridYMin);
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->referenceLineXWidth);
                $draw->color($this->referenceLineXColor);
            }
        );
        return $this;
    }

    /**
     * plots a reference line of Y
     * @return  self
     */
    public function plotReferenceLineY()
    {
        if (!$this->referenceLineY) {
            return $this;
        }
        $x1 = (int) $this->pX($this->gridXMin);
        $y1 = (int) $this->pY($this->referenceLineYValue);
        $x2 = (int) $this->pX($this->gridXMax);
        $y2 = (int) $y1;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->referenceLineYWidth);
                $draw->color($this->referenceLineYColor);
            }
        );
        return $this;
    }

    /**
     * plots specification limit lines of X
     * @return  self
     */
    public function plotSpecificationLimitX()
    {
        if (!$this->specificationLimitX) {
            return $this;
        }
        // lower limit
        $x1 = (int) $this->pX($this->specificationLimitXLower);
        $y1 = (int) $this->pY($this->gridYMax);
        $x2 = (int) $x1;
        $y2 = (int) $this->pY($this->gridYMin);
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->specificationLimitXWidth);
                $draw->color($this->specificationLimitXColor);
            }
        );
        // upper limit
        $x1 = (int) $this->pX($this->specificationLimitXUpper);
        $x2 = (int) $x1;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->specificationLimitXWidth);
                $draw->color($this->specificationLimitXColor);
            }
        );
        return $this;
    }

    /**
     * plots specification limit lines of y
     * @return  self
     */
    public function plotSpecificationLimitY()
    {
        if (!$this->specificationLimitY) {
            return $this;
        }
        // lower limit
        $x1 = (int) $this->pX($this->gridXMin);
        $y1 = (int) $this->pY($this->specificationLimitYLower);
        $x2 = (int) $this->pX($this->gridXMax);
        $y2 = (int) $y1;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->specificationLimitYWidth);
                $draw->color($this->specificationLimitYColor);
            }
        );
        // upper limit
        $y1 = (int) $this->pY($this->specificationLimitYUpper);
        $y2 = (int) $y1;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->width($this->specificationLimitYWidth);
                $draw->color($this->specificationLimitYColor);
            }
        );
        return $this;
    }

    /**
     * plots legends
     * @return  self
     */
    public function plotLegend()
    {
        if (!$this->legend) {
            return $this;
        }
        $baseX = $this->canvasWidth * (3 + $this->frameXRatio) / 4 - $this->legendWidth;
        $baseY = 10;
        $x1 = $baseX;
        $y1 = $baseY;
        $x2 = $x1 + $this->legendWidth;
        $y2 = $y1 + $this->legendFontSize * 1.2 * $this->legendCount + 8;
        $this->image->rectangle(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->background($this->canvasBackgroundColor);
                $draw->border($this->axisWidth, $this->axisColor);
            }
        );
        for ($i = 0; $i < $this->legendCount; $i++) {
            if (empty($this->legends[$i])) {
                $label = 'unknown ' . $i;
            } else {
                $label = $this->legends[$i];
            }
            $x1 = (int) ($baseX + 4);
            $y1 = (int) ($baseY + $i * $this->legendFontSize * 1.2 + 4);
            $x2 = (int) ($x1 + 20);
            $y2 = (int) ($y1 + $this->legendFontSize);
            $this->image->rectangle(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) use ($i) {
                    $draw->background($this->colors[$i]);
                    $draw->border(1, $this->axisColor);
                }
            );
            $x = $x2 + 4;
            $y = $y1;
            $this->image->text(
                $label,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->legendFontSize);
                    $font->color($this->fontColor);
                    $font->align('left');
                    $font->valign('top');
                }
            );
        }
        return $this;
    }

    /**
     * creates a scatter plot image and save it
     * @param   string  $filePath
     * @return  self
     * @thrown  \Exception
     */
    public function create(string $filePath)
    {
        if (strlen($filePath) == 0) {
            throw new \Exception("Empty string specified for file path.");
        }
        $this->setProperties();
        $this->plotLabelX();
        $this->plotLabelY();
        $this->plotCaption();
        $this->plotGridsX();
        $this->plotGridsY();
        $this->plotGridValuesX();
        $this->plotGridValuesY();
        $this->plotLayers();
        $this->plotAxis();
        $this->plotReferenceLineX();
        $this->plotReferenceLineY();
        $this->plotSpecificationLimitX();
        $this->plotSpecificationLimitY();
        $this->plotLegend();
        $this->image->save($filePath);
        return $this;
    }
}
