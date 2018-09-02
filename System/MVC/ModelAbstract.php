<?php

namespace System\MVC;

abstract class ModelAbstract
{
    public function MapParameters($params, $nullEmpty = true)
    {   
        foreach ($params as $key => $val) {
            if (property_exists($this, $key)) {
                if ($nullEmpty && $val === '') {
                    $this->$key = null;
                } else {
                    $this->$key = trim($val);
                }
            }
        }
    }
}