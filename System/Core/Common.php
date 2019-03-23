<?php

function Redirect($uri, $httpResponseCode = 302)
{
    if (!preg_match('#^https?://#i', $uri)) {
        $uri = BASE_URL . $uri;
        header("Location: " . $uri, TRUE, $httpResponseCode);
        die();
    }
}

function GetUri($uri)
{
    if (!preg_match('#^https?://#i', $uri)) {
        $uri = BASE_URL . $uri;
        return $uri;
    }
}

function PageNotFound()
{
    $pageNotFound = new \WebInterface\Controllers\PageNotFound();

    $pageNotFound->IndexAction();

    exit;
}

function replaceString($string, $replaceArray)
{
    foreach ($replaceArray as $k => $v) {
        if (is_array($v)) {
            $v = implode(",", $v);
        }
        $string = str_replace($k, $v, $string);

    }
    return $string;
}

function dd($var1 = "here", $var2 = "", $var3 = "")
{
    echo("<pre>");
    var_dump($var1);
    var_dump($var2);
    var_dump($var3);
    echo("</pre>");
    exit;
}

function generateRandomAlphaNumericString($length = 7)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateSlug($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}


