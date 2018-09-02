<?php

namespace Infrastructure;

class Encryption
{

    static function EncryptPassword($password)
    {
        $encryption_salt = "8f5d0eae5947135741cd0aef3teg6eb2";
        $encrypted = hash("sha256", $encryption_salt . $password);
        return $encrypted;
    }

    static function EncryptAuthenticationKey($authenticationKey)
    {
        $encryption_salt = "b80dc013e006c7a5266fd9c57925fd";
        $encrypted = hash("sha256", $encryption_salt . $authenticationKey);
        return $encrypted;
    }
}