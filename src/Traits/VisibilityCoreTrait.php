<?php

namespace Macocci7\PhpScatterplot\Traits;

trait VisibilityCoreTrait
{
    protected bool $gridX = false;
    protected bool $gridY = false;

    /**
     * sets grid of x on
     */
    public function gridXOn(): self
    {
        $this->gridX = true;
        return $this;
    }

    /**
     * sets grid of x off
     */
    public function gridXOff(): self
    {
        $this->gridX = false;
        return $this;
    }

    /**
     * sets grid of y on
     */
    public function gridYOn(): self
    {
        $this->gridY = true;
        return $this;
    }

    /**
     * sets grid of y off
     */
    public function gridYOff(): self
    {
        $this->gridY = false;
        return $this;
    }

    /**
     * sets legend off
     */
    public function legendOff(): self
    {
        $this->legend = false;
        return $this;
    }
}
