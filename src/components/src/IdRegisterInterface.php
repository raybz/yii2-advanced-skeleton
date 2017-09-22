<?php
namespace Components;

interface IdRegisterInterface
{
    public static function register($username, $password, $nick = null);
}