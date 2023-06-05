<?php

namespace Macocci7\PhpScatterplot;

use Macocci7\PhpFrequencyTable\FrequencyTable;

class Analyzer
{
    public $ft;
    public $parsed;

    public function __construct()
    {
        $this->ft = new FrequencyTable();
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

    public function layerMax($layer)
    {
        $xMax = [];
        $yMax = [];
        foreach ($layers as $layer) {
            foreach ($layer['x'] as $key => $values) {
                $xMax[] = max($values);
            }
            foreach ($layer['y'] as $key => $values) {
                $yMax[] = max($values);
            }
        }
        return [max($xMax), max($yMax)];
    }

    public function layerMin($layer)
    {
        $xMin = [];
        $yMin = [];
        foreach ($layers as $layer) {
            foreach ($layer['x'] as $key => $values) {
                $xMin[] = min($values);
            }
            foreach ($layer['y'] as $key => $values) {
                $yMin[] = min($values);
            }
        }
        return [min($xMin), min($yMin)];
    }
}
