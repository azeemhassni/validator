<?php
/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 4/11/2015
 * Time: 7:44 PM
 */

namespace azi\Rules;


use azi\RuleInterface;

class SameRule implements RuleInterface {

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return mixed
     */
    public function run( $field, $value, $message = null ) {
        // TODO: Implement run() method.
    }

    /**
     * Rule error message
     *
     * @return mixed
     */
    public function message() {
        // TODO: Implement message() method.
    }
}