<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Anup
 * Date: 12/24/13
 * Time: 4:40 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Infrastructure;

class Validation {

    function __construct() {
        $this->id = 0;
    }

    function AddFields($validateTo, $ValidationType, $error) {
        $index = $this->id++;
        $this->check_vars[$index]['data'] = $validateTo;
        $this->check_vars[$index]['validationType'] = strtolower($ValidationType);
        $this->check_vars[$index]['error'] = $error;
    }

    function validate() {
        $errorMsg = "";
        for($i = 0; $i < $this->id; $i++) {
            $postVar  = $this->check_vars[$i]['data'];
            $validationType = $this->check_vars[$i]['validationType'];
            $error    = $this->check_vars[$i]['error'];

            switch($validationType) {

                case "required": {
                    if(! $this->ValidateRequired(trim($postVar))) {
                       $errorMsg .= $error.".";
                    }
                    break;
                }

                case "alphabet": {
                    $regexp = '/^[A-za-z]$/';
                    if (!preg_match($regexp, $postVar)) {
                        $length = strlen($postVar);
                        if($length)
                            $errorMsg .= $error.".";
                    }
                    break;
                }

                case "number": {
                    $regexp = '/^[0-9]+$/';
                    if (!preg_match($regexp, $postVar)) {
                        $length = strlen($postVar);
                        if($length)
                            $errorMsg .= $error.".";
                    }
                    break;
                }

                case "date": {
                    if (! $this->ValidateDate($postVar)) {
                            $errorMsg .= $error.".";
                    }
                    break;
                }

                case "time": {
                    if (! $this->ValidateTime($postVar)) {
                        $errorMsg .= $error.".";
                    }
                    break;
                }

                case "datetime": {
                    if (! $this->ValidateDateTime($postVar)) {
                        $errorMsg .= $error.".";
                    }
                    break;
                }
                case "dateorder": {
                    if (!$this->ValidateDate($postVar['DateFrom'])){
                        $errorMsg .= "Invalid Date Format for DateFrom";
                        break;
                    }
                    if(!$this->ValidateDate($postVar['DateTo'])){
                        $errorMsg .= "Invalid Date Format for DateTo";
                        break;
                    }
                    if(!$this->ValidateDateRange($postVar['DateFrom'],$postVar['DateTo']))
                        $errorMsg .= $error;
                    break;
                }
                case "confirmpassword": {
                    if (! $this->ValidateConfirmPassword($postVar)) {
                        $errorMsg .= $error.".";
                    }
                    break;
                }
                case "regex": {
                    if (! $this->ValidateByRegEx($postVar)) {
                        $errorMsg .= $error.".";
                    }
                    break;
                }
                case "lowertimelimit": {
                    if (! $this->ValidateLowerTimeLimit($postVar)) {
                        $errorMsg .= $error.".";
                    }
                    break;
                }
                case "uppertimelimit": {
                if (! $this->ValidateUpperTimeLimit($postVar)) {
                    $errorMsg .= $error.".";
                }
                break;
            }
            }
        }
        return $errorMsg;
    }

    function ValidateRequired($variable)
    {
        if($variable == "")
            return false;
        return true;
    }

    function ValidateConfirmPassword($passwords)
    {
        if($passwords['Password'] != $passwords['ConfirmPassword'])
        {
            return false;
        }
        return true;
    }

    function ValidateDate($date)
    {
        if(preg_match("/^(\d{4})\-(\d{1,2})\-(\d{1,2})$/", trim($date), $matches))
        {
            if(checkdate($matches[2], $matches[3], $matches[1]))
            {
                return true;
            }
        }
        return false;
    }

    function ValidateTime($time)
    {
        return (bool)preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/",
            $time);
    }

    function ValidateDateTime($dateTime)
    {
        $dateTime = explode(" ", trim($dateTime));
        if( $this->ValidateDate($dateTime[0]) && $this->ValidateTime($dateTime[1]))
        {
            return true;
        }

        return false;
    }

    function ValidateDateRange($dateFrom,$dateTo)
    {
        if($dateFrom < $dateTo)
            return true;
        return false;
    }

    function ValidateByRegEx($fieldsToValidate)
    {
        return (bool)preg_match($fieldsToValidate['RegEx'],$fieldsToValidate['FieldToValidate']);
    }

    function ValidateLowerTimeLimit($timeRange)
    {
        /*if(!$this->ValidateTime($timeRange['FieldToValidate']))
        {
            echo "<script type='text/javascript'>alert('".date('H:i:s', strtotime($timeRange['FieldToValidate']))."');</script>";
            $error = "Not Valid Time Format";
            return false;
        }*/

        if($timeRange['FieldToValidate'] >= $timeRange['validateWith'])
            return true;
        return false;
    }

    function ValidateUpperTimeLimit($timeRange)
    {
       /* if($this->ValidateTime($timeRange['FieldToValidate']))
        {
            echo "<script type='text/javascript'>alert('".$timeRange['FieldToValidate']."');</script>";
            $error = "Not Valid Time Format";
            return false;
        }
        */
        if($timeRange['FieldToValidate'] <= $timeRange['validateWith'])
            return true;
        return false;
    }

}