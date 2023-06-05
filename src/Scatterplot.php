<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpScatterplot\Plotter;

class Scatterplot extends Plotter
{

    private $layers;
    
    public function data($data)
    {
        if (!$this->validData($data)) return;
        $this->layers[] = $data;
        return $this;
    }

    public function layers($layers)
    {
        if (!$this->validLayer($layers)) return;
        $this->layers = $layers;
        return $this;
    }

    /* Future version
    public function matrix()
    {

    }
    */
}
