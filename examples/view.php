<?php

require_once( "../src/Validator.php" );
use azi\Validator;

$v = new Validator();

if($_POST) {

    $rules = array(
        'name' => 'alpha|required',
        'age'  => 'num--Only Numeric value expected|required--please provide your age',
    );

    $v->validate( $_POST, $rules );

    if ( !$v->passed() ) {
        $v->goBackWithErrors();
    }


    // validation passed do other stuffs here

}
