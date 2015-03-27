<?php

require_once( "../src/azi/Validator.php" );
use azi\Validator;

$v = new Validator();

if($_POST) {

    $rules = array(
        'email' => 'email--Invalid email provided|required',
        'age'  => 'num|required--please provide your age',
    );

    $v->validate( $_POST, $rules );

    if ( !$v->passed() ) {
        $v->goBackWithErrors();
    }


    // validation passed do other stuffs here

}
