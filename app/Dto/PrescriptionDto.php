<?php

namespace App\Dto;

use App\Models\Product;

class PrescriptionDto
{
    public $name;
    public $productId;
    public $product;
    public $duration;
    public $dosage;
    public $route;
    public $frequency;

    public function __construct($data)
    {
        $this->name = $data['name'] ?? '';
        $this->productId = $data['productId'] ?? '';
        $this->duration = $data['duration'] ?? '';
        $this->dosage = $data['dosage'] ?? '';
        $this->route = $data['route'] ?? '';
        $this->frequency = $data['frequency'] ?? '';
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->name  = $product->name;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    public function setDosage($v)
    {
        $this->dosage = $v;
    }
    public function setRoute($v)
    {
        $this->route = $v;
    }
    public function setFrequency($v)
    {
        $this->frequency = $v;
    }

    public function __set($name, $value)
    {
        throw new \Exception("attempting to directly set a value on a DTO");
    }
}
