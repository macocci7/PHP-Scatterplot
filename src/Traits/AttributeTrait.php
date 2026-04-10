<?php

namespace Macocci7\PhpScatterplot\Traits;

trait AttributeTrait
{
    protected int $CANVAS_WIDTH_LIMIT_LOWER;
    protected int $CANVAS_HEIGHT_LIMIT_LOWER;
    protected int|float $xLimitUpper;
    protected int|float $xLimitLower;
    protected int|float $yLimitUpper;
    protected int|float $yLimitLower;
    protected int $canvasWidth = 600;
    protected int $canvasHeight = 500;
    protected float $frameXRatio = 0.8;
    protected float $frameYRatio = 0.7;
    protected string $labelX;
    protected string $labelY;
    protected string $caption;
    protected bool $legend = false;
    /**
     * @var string[]    $legends
     */
    protected array $legends;

    /**
     * sets limits of x
     * @thrown  \Exception
     */
    public function limitX(int|float $lower, int|float $upper): self
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
     * @thrown  \Exception
     */
    public function limitY(int|float $lower, int|float $upper): self
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
     * @thrown  \Exception
     */
    public function resize(int $width, int $height): self
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
     * @thrown  \Exception
     */
    public function frame(float $xRatio, float $yRatio): self
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
     * sets the label of x
     */
    public function labelX(string $label): self
    {
        $this->labelX = $label;
        return $this;
    }

    /**
     * sets the label of y
     */
    public function labelY(string $label): self
    {
        $this->labelY = $label;
        return $this;
    }

    /**
     * sets the caption
     */
    public function caption(string $caption): self
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * sets the legends
     * @param   string[]    $legends
     */
    public function legends(array $legends): self
    {
        if (!self::isStringsAll($legends)) {
            throw new \Exception("Each elements of legends must be type of string.");
        }
        $this->legends = $legends;
        $this->legend = true;
        return $this;
    }
}
