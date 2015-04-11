<?php

require_once( "../vendor/autoload.php" );

use azi\Validator;

$v = new Validator();
//
//$post  = [ 'the_field' => '123A' ];
//$rules = [ 'the_field' => 'num' ];
//
//var_dump( Validator::error( 'the_field' ) );
//var_dump( $v->validate( $post, $rules )->passed() );
//
//exit;


if ( $_POST ) {

    $rules = array(
        'email'            => 'email|required',
        'password'         => 'required',
        'confirm_password' => 'if:password[123456](required|same:password)',
    );
    unset($rules['email'],$rules['password']);
    $v->validate( $_POST, $rules );
    var_dump($v->passed());
    if ( ! $v->passed() ) {
       # $v->goBackWithErrors();
    } else {
        exit("Validation Passed");
    }



    // validation passed do other stuffs here

}
