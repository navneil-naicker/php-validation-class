<?php

require_once( dirname(__FILE__) . '/class.validation.php');

$request = $_POST;
echo cx_validate($request, [
    'name' => 'required|min:2|max:55',
    'email' => 'required|min:5|max:55|type:email',
    'website' => 'required|type:url',
    'message' => 'required|min:5|max:255|disallow_links',
]);