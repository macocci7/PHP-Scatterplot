<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpFrequencyTable\FrequencyTable;

define("LIMIT_LAYERS", 8);

/**
 * Class for analysis
 */
class Analyzer
{
    public $ft;
    public $parsed;

    /**
     * constructor
     * @param
     * @return
     */
    public function __construct()
    {
        $this->ft = new FrequencyTable();
    }

    /**
     * judges whether $layers is valid or not
     * @param array $layers
     * @return bool
     */
    public function isValidLayers($layers)
    {
        if (!is_array($layers)) return false;
        if (count($layers) > LIMIT_LAYERS) {
            echo "too many layers. there's more than " . LIMIT_LAYERS . " layers.\n";
            return false;
        }
        foreach ($layers as $layer) {
            if (!$this->isValidLayer($layer)) return false;
        }
        return true;
    }

    /**
     * judges whether $layer is valid or not
     * @param array $layer
     * @return bool
     */
    public function isValidLayer($layer)
    {
        if (!is_array($layer)) return false;
        if (empty($layer)) return false;
        if (!array_key_exists('x', $layer)) return false;
        if (!array_key_exists('y', $layer)) return false;
        foreach ($layer['x'] as $value) {
            if (!is_int($value) && !is_float($value)) return false;
        }
        foreach ($layer['y'] as $value) {
            if (!is_int($value) && !is_float($value)) return false;
        }
        return true;
    }

    /**
     * judgees whether $data is valid or not for analysis
     * @param array $data
     * @return bool
     */
    public function isValid($data)
    {
        if (!is_array($data)) return false;
        if (empty($data)) return false;
        foreach ($data as $value) {
            if (!is_int($value) && !is_float($value)) return false;
        }
        return true;
    }

    /**
     * calculates mean of $data
     * @param array $data
     * @return float
     */
    public function mean($data)
    {
        if (!$this->isValid($data)) return;
        return array_sum($data) / count($data);
    }

    /**
     * calculates variance of $data
     * @param array $data
     * @return float
     */
    public function variance($data)
    {
        if (!$this->isValid($data)) return;
        $mean = $this->mean($data);
        $deviation2 = 0;
        foreach ($data as $value) {
            $deviation2 += ($value - $mean) ** 2;
        }
        return $deviation2 / count($data);
    }

    /**
     * calculates covariance of $dataX and $dataY
     * @param array $dataX
     * @param array $dataY
     * @return float
     */
    public function covariance($dataX, $dataY)
    {
        if (!$this->isValid($dataX) || !$this->isValid($dataY)) return;
        if (count($dataX) !== count($dataY)) return;
        $count = count($dataX);
        $meanX = $this->mean($dataX);
        $meanY = $this->mean($dataY);
        $deviationXY = 0;
        for ($i = 0; $i < $count; $i++) {
            $deviationXY += ($dataX[$i] - $meanX) * ($dataY[$i] - $meanY);
        }
        return $deviationXY / $count;
    }

    /**
     * calculates standard deviation of $data
     * @param array $data
     * @return float
     */
    public function standardDeviation($data)
    {
        if (!$this->isValid($data)) return;
        return sqrt($this->variance($data));
    }

    /**
     * calculates correlation coefficient of $dataX and $dataY
     * @param array $dataX
     * @param array $dataY
     * @return float
     */
    public function correlationCoefficient($dataX, $dataY)
    {
        if (!$this->isValid($dataX) || !$this->isValid($dataY)) return;
        if (count($dataX) !== count($dataY)) return;
        $sx = $this->standardDeviation($dataX);
        $sy = $this->standardDeviation($dataY);
        if (!($sx * $sy)) return;
        return $this->covariance($dataX, $dataY) / ($sx * $sy);
    }

    /**
     * derives the regression line formula
     * @param array $dataX
     * @param array $dataY
     * @return array
     */
    public function regressionLineFormula($dataX, $dataY)
    {
        if (!$this->isValid($dataX) || !$this->isValid($dataY)) return;
        if (count($dataX) !== count($dataY)) return;
        $varianceX = $this->variance($dataX);
        if (!$varianceX) return;
        $a = $this->covariance($dataX, $dataY) / $varianceX;
        $b = $this->mean($dataY) - $a * $this->mean($dataX);
        return [
            'a' => $a,
            'b' => $b,
        ];
    }

    /* future version
    public function regressionCurveFormula($dataX, $dataY)
    {
        if (!$this->isValid($dataX || !$this->isValid($dataY))) return;
        if (count($dataX) !== count($dataY)) return;
    }
    */

    /**
     * calculates the upper control limit of $data
     * @param array $data
     * @return float
     */
    public function getUcl($data)
    {
        if (!$this->isValid($data)) return;
        $this->ft->setClassRange(1);
        $this->ft->setData($data);
        $parsed = $this->ft->parse();
        if (!array_key_exists('ThirdQuartile', $parsed)) return;
        if (!array_key_exists('InterQuartileRange', $parsed)) return;
        return $parsed['ThirdQuartile'] + 1.5 * $parsed['InterQuartileRange'];
    }

    /**
     * calculates the lower control limit of $data
     * @param array $data
     * @return float
     */
    public function getLcl($data)
    {
        if (!$this->isValid($data)) return;
        $this->ft->setClassRange(1);
        $this->ft->setData($data);
        $parsed = $this->ft->parse();
        if (!array_key_exists('FirstQuartile', $parsed)) return;
        if (!array_key_exists('InterQuartileRange', $parsed)) return;
        return $parsed['FirstQuartile'] - 1.5 * $parsed['InterQuartileRange'];
    }

    /**
     * detects outliers and returns them
     * @param array $data
     * @return array
     */
    public function outliers($data)
    {
        if (!$this->isValid($data)) return;
        $this->ft->setClassRange(1);
        $this->ft->setData($data);
        $this->parsed = $this->ft->parse();
        $ucl = $this->getUcl($data);
        $lcl = $this->getLcl($data);
        if (null === $ucl || null === $lcl) return;
        $outliers = [];
        foreach ($data as $value) {
            if ($value > $ucl || $value < $lcl) $outliers[] = $value;
        }
        return $outliers;
    }

    /**
     * returns x-max and y-max of $layers
     * @param array $layers
     * @return array
     */
    public function layerMax($layers)
    {
        if (!$this->isValidLayers($layers)) return;
        $xMax = [];
        $yMax = [];
        foreach ($layers as $layer) {
            $xMax[] = max($layer['x']);
            $yMax[] = max($layer['y']);
        }
        return [max($xMax), max($yMax)];
    }

    /**
     * return x-min and y-min of $layers
     * @param array $layers
     * @return array
     */
    public function layerMin($layers)
    {
        if (!$this->isValidLayers($layers)) return;
        $xMin = [];
        $yMin = [];
        foreach ($layers as $layer) {
            $xMin[] = min($layer['x']);
            $yMin[] = min($layer['y']);
        }
        return [min($xMin), min($yMin)];
    }

    /**
     * returns parsed data of layers
     * @param array $layers
     * @return array
     */
    public function parse($layers)
    {
        if (!$this->isValidLayers($layers)) return;
        $parsed = [];
        foreach ($layers as $name => $layer) {
            $parsed[$name] = [
                'count' => count($layer['x']),
                'x' => [
                    'Mean' => $this->mean($layer['x']),
                    'Max' => max($layer['x']),
                    'Min' => min($layer['x']),
                    'Variance' => $this->variance($layer['x']),
                    'StandardDeviation' => $this->standardDeviation($layer['x']),
                ],
                'y' => [
                    'Mean' => $this->mean($layer['y']), 
                    'Max' => max($layer['y']),
                    'Min' => min($layer['y']),
                    'Variance' => $this->variance($layer['y']),
                    'StandardDeviation' => $this->standardDeviation($layer['y']),
                ],
                'Covariance' => $this->covariance($layer['x'], $layer['y']),
                'CorrelationCoefficient' => $this->correlationCoefficient($layer['x'], $layer['y']),
                'RegressionLineFormula' => $this->regressionLineFormula($layer['x'], $layer['y']),
            ];
        }
        return $parsed;
    }
}
