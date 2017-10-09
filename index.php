<?php

require_once( dirname(__FILE__) . '/class.validation.php');

$cx_validate = new cx_validate;

echo $cx_validate->validate($_POST, [
    'name' => 'required|min:2|max:55|name',
    'email' => 'required|min:5|max:55|email',
    'phone' => 'phone',
    'website' => 'required|url',
    'message' => 'required|min:5|max:255|disallow_links',
]);