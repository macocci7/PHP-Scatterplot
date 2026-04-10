<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpScatterplot\Helpers\Config;
use Macocci7\PhpScatterplot\Plotter;

/**
 * Class for management of scatter plot
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Scatterplot extends Plotter
{
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
     */
    private function loadConf(): void
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
     */
    public function config(string|array $configResource): self
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
     */
    public function layer(array $layer): self
    {
        if (self::isValidLayer($layer)) {
            $this->layers[] = $layer;
        }
        return $this;
    }

    /**
     * sets layers
     * @param   array<int|string, array<string, list<int|float>>>   $layers
     */
    public function layers(array $layers): self
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
     * returns the config values
     */
    public function getConfig(string|null $key = null): mixed
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
}
