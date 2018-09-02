<?php


namespace Infrastructure;

class AverageColoring
{
    private $average = 0;

    function ColorAverages($array)
    {
        $blue = 0;

        $array = array_unique($array);

        sort($array);

        $this->average = $average = array_sum($array) / count($array);

        $valueColor = array();

        array_push($valueColor, array("value" => $array[0], "r" => 0, "g" => 255, "b" => $blue));
        array_push($valueColor, array("value" => $array[count($array) - 1], "r" => 255, "g" => 0, "b" => $blue));

        $equalIndex = $this->GetEqual($average, $array);

        if ($equalIndex != -1) {
            array_push($valueColor, array("value" => $array[$equalIndex], "r" => 255, "g" => 255, "b" => $blue));
        }

        $arrayLessThanAverage = array_filter($array, array($this, "LessThan"));
        $arrayGreaterThanAverage = array_filter($array, array($this, "GreaterThan"));

        for ($i = 1; $i < count($array) - 1; $i++) {

            if ($i == $equalIndex) {
                continue;
            }

            $topDifference = abs($array[$i] - $array[0]);
            $bottomDifference = abs($array[count($array) - 1] - $array[$i]);

            if ($topDifference <= $bottomDifference) {
                $red = intval(($array[$i] / $arrayLessThanAverage[count($arrayLessThanAverage) - 1]) * 254);
                $green = 255;

                if ($red > 255) {
                    $red = 255;
                }

                array_push($valueColor, array("value" => $array[$i], "r" => $red, "g" => $green, "b" => $blue));

            } else {
                $green = 254 - intval(($array[$i] / $arrayGreaterThanAverage[count($array) - 1]) * 254);
                $red = 255;

                if ($green > 255) {
                    $green = 255;
                }
                array_push($valueColor, array("value" => $array[$i], "r" => $red, "g" => $green, "b" => $blue));
            }
        }

        return $valueColor;

    }

    function ArrayAverage($array, $startIndex, $endIndex)
    {
        $sum = 0;

        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $sum += $array[$i];
        }

        return $sum / (($endIndex - $startIndex) + 1);
    }

    function GetClosest($search, $arr)
    {
        $closest = null;
        $closestIndex = null;

        foreach ($arr as $key => $item) {
            if ($closest == null || abs($search - $closest) > abs($item - $search)) {
                $closestIndex = $key;
                $closest = $item;
            }
        }
        return $closestIndex;
    }

    function GetEqual($search, $arr)
    {
        $equalIndex = -1;

        foreach ($arr as $key => $item) {
            if ($search == $item) {
                $equalIndex = $key;
                break;
            }
        }
        return $equalIndex;
    }

    function LessThan($value)
    {
        return $value < $this->average;
    }

    function GreaterThan($value)
    {
        return $value > $this->average;
    }

    function multi_array_unique($arr) {
        foreach ($arr as &$elm) {
            $elm = serialize($elm);
        }

        $arr = array_unique($arr);

        foreach ($arr as &$elm) {
            $elm = unserialize($elm);
        }

        return $arr;
    }

}