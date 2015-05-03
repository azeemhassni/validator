<?php

namespace azi\Rules;


use azi\RuleInterface;

/**
 * Class IpRule
 *
 * @package azi\Rules
 */
class IpRule implements RuleInterface {

    protected $field = null;
    protected $message = null;

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return mixed
     */
    public function run( $field, $value, $message = null ) {
        $this->field = $field;
        $this->message = $message;
        return filter_var( $value, FILTER_VALIDATE_IP ) !== false;
    }

    /**
     * Rule error message
     *
     * @return mixed
     */
    public function message() {
        if($this->message) {
            return $this->message;
        }

        return $this->field . " must contain a valid IP address";
    }
}