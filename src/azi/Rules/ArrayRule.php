<?php

namespace azi\Rules;

use azi\RuleInterface;

/**
 * Class ArrayRule
 * 
 * @author farzak
 */
class ArrayRule implements RuleInterface {

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
        return (is_array( $value ) && $value);
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

        return $this->field . " must contain at least one item";
    }
}