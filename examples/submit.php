<?php

require_once( "../vendor/autoload.php" );

use azi\Validator;

$v = new Validator();

$post = ['the_field' => '123A'];
$rules = ['the_field' => 'num'];

#var_dump(Validator::error('the_field'));
var_dump($v->test($post, $rules)->passed());

exit;




























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
