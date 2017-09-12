<?php

require_once( dirname(__FILE__) . '/class.validation.php');

$request = $_POST;

$cx_validate = new cx_validate;

class cx_validate2 extends cx_validate{

}

echo $cx_validate->validate($request, [
    'name' => 'required|min:2|max:55|type:name',
    'email' => 'required|min:5|max:55|type:email',
    'phone' => 'required|type:phone',
    'website' => 'required|type:url',
    'message' => 'required|min:5|max:255|disallow_links',
]);