<?php


namespace App\Http\Libs;


class Data
{
    private $data;

    public function __construct($data)
    {
        $this->data = (object)$data;
    }

    public function toArray()
    {
        return (array)$this->data;
    }

    public function count()
    {
        return count($this->toArray());
    }
}
