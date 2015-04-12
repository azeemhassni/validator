<?php
var_dump(trim("              "));exit;
require_once( "../vendor/autoload.php" );

use azi\Validator;

$v = new Validator();

if ( $_POST ) {

    $rules = array(
        'email'            => 'email|required',
        'password'         => 'required',
        'confirm_password' => 'same:password|required',
    );

    if ( $v->validate( $_POST, $rules )->failed() ) {
        $v->goBackWithErrors();
    }


    echo "data validated do whatever you want here...";



}

