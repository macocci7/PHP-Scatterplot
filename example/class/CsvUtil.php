<?php

namespace Macocci7;

class CsvUtil
{
    protected $csv;
    protected $headline;
    protected $offset;
    protected $columns;
    protected $cast;

    public function __construct()
    {

    }

    public function load($path)
    {
        if (!is_string($path)) return;
        if (!file_exists($path)) return;
        $this->csv = array_map('str_getcsv', file($path));
        return $this;
    }

    public function encode($from, $to)
    {
        foreach ($this->csv as $index => $row) {
            foreach ($row as $column => $value) {
                if (!is_null($value))
                    $this->csv[$index][$column] = mb_convert_encoding($value, $to, $from);
            }
        }
        return $this;
    }

    public function bool()
    {
        $this->cast = 'bool';
        return $this;
    }

    public function integer()
    {
        $this->cast = 'integer';
        return $this;
    }

    public function float()
    {
        $this->cast = 'float';
        return $this;
    }

    public function string()
    {
        $this->cast = 'string';
        return $this;
    }

    public function raw()
    {
        $this->cast = null;
        return $this;
    }

    public function offset($offset)
    {
        if (!is_int($offset)) return;
        if ($offset < 0) return;
        $this->offset = $offset;
        return $this;
    }

    public function heads($row)
    {
        if (!is_int($row)) return;
        if ($row < 1) return;
        if (empty($this->csv)) return;
        return $this->csv[$row];
    }

    public function column($column)
    {
        if (!is_int($column)) return;
        if ($column < 0) return;
        $csv = $this->offset ? array_slice($this->csv, $this->offset) : $this->csv;
        $data = array_column($csv, $column);
        //var_dump($data);
        if (!$data) return;
        if ($this->cast) {
            foreach ($data as $index => $value) {
                if (0 === strcmp('bool', $this->cast)) $data[$index] = (bool) $value;
                if (0 === strcmp('integer', $this->cast)) $data[$index] = (int) $value;
                if (0 === strcmp('float', $this->cast)) $data[$index] = (float) $value;
                if (0 === strcmp('string', $this->cast)) $data[$index] = (string) $value;
            }
        }
        return $data;
    }
}
