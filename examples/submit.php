<?php

require_once( "vendor/autoload.php" );

use azi\Validator;

$v = new Validator();

if($_POST) {

    $rules = array(
        'email' => 'email|required',
        'password'  => 'required',
        'confirm_password'  => 'same:password|required',
    );

    $v->validate( $_POST, $rules );

    if ( !$v->passed() ) {
        $v->goBackWithErrors();
    }


    // validation passed do other stuffs here

}
