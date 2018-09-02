<?php

namespace Infrastructure;


use ViewModel\BeepCallReportingDaily;
use ViewModel\BeepCallReportingDailyAverages;
use ViewModel\BeepCallReportingHourly;
use ViewModel\BeepCallReportingHourlyAverages;

class MergeArray
{
    private $array = array();

    private $columns = array();

    private $arrayMerged = array();

    private $commonField;

    private $dateOrTime;

    function MergeArrayData($array, $columns, $source, $dateOrTime = null, $sortExpression = null, $sortOrder = null, $offset = null, $rowNumber = null, $pageNumber = null)
    {
        $shared_keys = array();

        foreach ($array as $data) {
            $shared_keys = array_merge($shared_keys, array_keys($data));
        }

        $shared_keys = array_unique($shared_keys);

        sort($shared_keys);

        foreach ($array as $data) {
            foreach ($shared_keys as $sharedKey) {
                if (!array_key_exists($sharedKey, $data)) {

                    if ($source == 'beep-count-hourly') {

                        $beepCallReportingHourly = new BeepCallReportingHourly();
                        $beepCallReportingHourly->DateTime = $sharedKey;
                        $data[$sharedKey] = (array)$beepCallReportingHourly;

                    } elseif ($source == 'beep-count-daily') {

                        $beepCallReportingDaily = new BeepCallReportingDaily();
                        $beepCallReportingDaily->Date = $sharedKey;
                        $data[$sharedKey] = (array)$beepCallReportingDaily;

                    } elseif ($source == 'HourlyAverages') {

                        $beepCallReportingHourlyAverages = new BeepCallReportingHourlyAverages();
                        $beepCallReportingHourlyAverages->DateTime = $sharedKey;
                        $data[$sharedKey] = (array)$beepCallReportingHourlyAverages;

                    } elseif ($source == 'DailyAverages') {

                        $beepCallReportingDailyAverages = new BeepCallReportingDailyAverages();
                        $beepCallReportingDailyAverages->Date = $sharedKey;
                        $data[$sharedKey] = (array)$beepCallReportingDailyAverages;

                    }
                }
            }

            array_push($this->array, $data);
        }


        $this->columns = $columns;
        $this->dateOrTime = $dateOrTime;

        $this->SumMergeSharedData($shared_keys, $source);

        for ($i = 0; $i < count($this->array); $i++) {

            $unmatched_keys = array();

            foreach ($this->array[$i] as $key => $value) {
                if (!in_array($key, $shared_keys)) {
                    array_push($unmatched_keys, $key);
                }
            }

            if (count($unmatched_keys) > 0) {
                $this->SetUnmatchedKeysData($unmatched_keys, $i, $source);
            }
        }

        if ($sortExpression == null) {
            return $this->arrayMerged;
        }

        $this->arrayMerged = $this->Sorting($this->arrayMerged, $sortExpression, $sortOrder);

        $list['RowCount'] = count($this->arrayMerged);

        if ($offset != null) {
            $this->arrayMerged = array_slice($this->arrayMerged, $offset, $rowNumber);
        }

        $list['Data'] = $this->arrayMerged;
        $list['PageNumber'] = $pageNumber;




        return $list;
    }

    function SumMergeSharedData($shared_keys, $source)
    {
        foreach ($shared_keys as $shared_key) {

            $arrayMerged = array();

            if ($this->dateOrTime != null)
                $arrayMerged[$this->dateOrTime] = $shared_key;

            foreach ($this->columns as $column) {

                $arrayMerged[$column] = 0;

                for ($i = 0; $i < count($this->array); $i++) {
                    $arrayMerged[$column] += intval($this->array[$i][$shared_key][$column]);
                }

            }

            if ($source == 'beep-count-hourly') {

                $arrayMerged['AverageBeep'] = 0;

                for ($i = 0; $i < count($this->array); $i++) {
                    $arrayMerged['AverageBeep'] += intval($this->array[$i][$shared_key]['TotalBeeps']);
                }

                $arrayMerged['AverageBeepPerMinute'] = round($arrayMerged['AverageBeep'] / 60);
                $arrayMerged['AverageBeepPerSecond'] = round($arrayMerged['AverageBeep'] / 3600);

            }

            if ($source == 'beep-count-daily') {

                $arrayMerged['AverageBeep'] = 0;

                for ($i = 0; $i < count($this->array); $i++) {
                    $arrayMerged['AverageBeep'] += intval($this->array[$i][$shared_key]['TotalBeeps']);
                }

                $arrayMerged['AverageBeepPerMinute'] = round($arrayMerged['AverageBeep'] / (24 * 60));
                $arrayMerged['AverageBeepPerSecond'] = round($arrayMerged['AverageBeep'] / (24 * 3600));
            }

            if ($source == 'HourlyAverages') {

                $arrayMerged = 0;

                for ($i = 0; $i < count($this->array); $i++) {
                    $arrayMerged += intval($this->array[$i][$shared_key]['TotalBeeps']);
                }

                $arrayMerged = round($arrayMerged / 60);
            }

            if ($source == 'DailyAverages') {

                $arrayMerged = 0;

                for ($i = 0; $i < count($this->array); $i++) {
                    $arrayMerged += intval($this->array[$i][$shared_key]['TotalBeeps']);
                }

                $arrayMerged = round($arrayMerged / (24 * 60));
            }

            array_push($this->arrayMerged, $arrayMerged);
        }
    }

    function SetUnmatchedKeysData($unmatched_keys, $serverNumber, $source)
    {
        foreach ($unmatched_keys as $unmatched_key) {

            $arrayMerged = array();

            if ($this->dateOrTime != null)
                $arrayMerged[$this->dateOrTime] = $unmatched_key;

            foreach ($this->columns as $column) {

                $arrayMerged[$column] = intval($this->array[$serverNumber][$unmatched_key][$column]);
            }

            if ($source == 'beep-count-hourly') {
                $arrayMerged['AverageBeepPerMinute'] = round(intval($this->array[$serverNumber][$unmatched_key]['TotalBeeps']) / 60);

                $arrayMerged['AverageBeepPerSecond'] = round(intval($this->array[$serverNumber][$unmatched_key]['TotalBeeps']) / 3660);
            }

            if ($source == 'beep-count-daily') {
                $arrayMerged['AverageBeepPerMinute'] = round(intval($this->array[$serverNumber][$unmatched_key]['TotalBeeps']) / (24 * 60));

                $arrayMerged['AverageBeepPerSecond'] = round(intval($this->array[$serverNumber][$unmatched_key]['TotalBeeps']) / (24 * 3660));
            }

            if ($source == 'HourlyAverages') {
                $arrayMerged = round(intval($this->array[$serverNumber][$unmatched_key]['TotalBeeps']) / 60);
            }

            if ($source == 'DailyAverages') {
                $arrayMerged = round(intval($this->array[$serverNumber][$unmatched_key]['TotalBeeps']) / (24 * 60));
            }

            array_push($this->arrayMerged, $arrayMerged);
        }
    }

    function Sorting($array, $sortExpression, $sortOrder)
    {
        foreach ($array as $key => $row) {
            $column[$key] = $row[$sortExpression];
        }

        if ($sortOrder == 'asc')
            array_multisort($column, SORT_ASC, $array);
        elseif ($sortOrder == 'desc')
            array_multisort($column, SORT_DESC, $array);

        return $array;
    }

    function MergeArrayDataWithOutSum($array, $columns, $commonField)
    {
        $this->array = $array;
        $this->columns = $columns;
        $this->commonField = $commonField;

        if (count($this->array) == 2)
            $shared_keys = array_intersect(array_keys($this->array[0]), array_keys($this->array[1]));
        else if (count($this->array) == 3)
            $shared_keys = array_intersect(array_keys($this->array[0]), array_keys($this->array[1]), array_keys($this->array[2]));

        $this->MergeSharedKeys($shared_keys);

        for ($i = 0; $i < count($this->array); $i++) {

            $unmatched_keys = array();

            foreach ($this->array[$i] as $key => $value) {
                if (!in_array($key, $shared_keys)) {
                    array_push($unmatched_keys, $key);
                }
            }

            if (count($unmatched_keys) > 0) {
                $this->SetUnmatchedKeysForCharts($unmatched_keys, $i);
            }

            $this->arrayMerged = $this->Sorting($this->arrayMerged, $this->commonField, 'asc');

            return $this->arrayMerged;
        }
    }

    function MergeSharedKeys($shared_keys)
    {
        foreach ($shared_keys as $shared_key) {

            $arrayMerged = array();
            $arrayMerged[$this->commonField] = $shared_key;

            foreach ($this->columns as $column) {
                for ($i = 0; $i < count($this->array); $i++) {
                    if ($i > 0)
                        $arrayMerged[$column . $i] = $this->array[$i][$shared_key][$column];
                    else
                        $arrayMerged[$column] = $this->array[$i][$shared_key][$column];
                }
            }

            array_push($this->arrayMerged, $arrayMerged);
        }

    }

    function SetUnmatchedKeysForCharts($unmatched_keys, $server_number)
    {
        foreach ($unmatched_keys as $unmatched_key) {
            $arrayMerged = array();

            $arrayMerged[$this->commonField] = $unmatched_key;

            foreach ($this->columns as $column) {
                for ($i = 0; $i < count($this->array); $i++) {
                    if ($i == $server_number) {
                        if ($i > 0)
                            $arrayMerged[$column . $i] = $this->array[$i][$unmatched_key][$column];
                        else
                            $arrayMerged[$column] = $this->array[$i][$unmatched_key][$column];
                    } else {
                        if ($i > 0)
                            $arrayMerged[$column . $i] = 0;
                        else
                            $arrayMerged[$column] = 0;
                    }
                }
            }

            array_push($this->arrayMerged, $arrayMerged);
        }
    }

}