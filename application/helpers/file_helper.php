<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('read_file')) {
    function read_file($path)
    {
        if (is_string($path) && file_exists($path)) {
            return file_get_contents($path);
        } else {
            return false;
        }
    }
}