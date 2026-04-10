<?php

namespace Macocci7\PhpScatterplot\Traits;

trait VisibilityAppendixTrait
{
    protected bool $referenceLineX = false;
    protected bool $referenceLineY = false;
    protected bool $specificationLimitX = false;
    protected bool $specificationLimitY = false;
    protected bool $regressionLine = false;
    protected bool $outlier = true;
    protected bool $mean = false;

    /**
     * sets reference line of x off
     */
    public function referenceLineXOff(): self
    {
        $this->referenceLineX = false;
        return $this;
    }

    /**
     * sets reference line of y off
     */
    public function referenceLineYOff(): self
    {
        $this->referenceLineY = false;
        return $this;
    }

    /**
     * sets reference lines off
     */
    public function referenceLinesOff(): self
    {
        $this->referenceLineXOff();
        $this->referenceLineYOff();
        return $this;
    }

    /**
     * sets specification limit of x off
     */
    public function specificationLimitXOff(): self
    {
        $this->specificationLimitX = false;
        return $this;
    }

    /**
     * sets specification limit of y off
     */
    public function specificationLimitYOff(): self
    {
        $this->specificationLimitY = false;
        return $this;
    }

    /**
     * sets specification limits off
     */
    public function specificationLimitsOff(): self
    {
        $this->specificationLimitXOff();
        $this->specificationLimitYOff();
        return $this;
    }

    /**
     * sets regression line on
     */
    public function regressionLineOn(): self
    {
        $this->regressionLine = true;
        return $this;
    }

    /**
     * sets regression line off
     */
    public function regressionLineOff(): self
    {
        $this->regressionLine = false;
        return $this;
    }
}
