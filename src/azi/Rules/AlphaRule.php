<?php namespace azi\Rules;

use azi\RuleInterface;

/**
 * Class AlphaRule
 *
 * @package azi\Rules
 */
class AlphaRule implements RuleInterface
{

    protected $pattern = '#^([a-zA-Z\s])+$#';
    protected $field = null;
    protected $message = null;

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return bool|mixed
     */
    public function run( $field, $value, $message = null ) {
        $this->field = $field;
        $this->message = $message;
        if ( preg_match( $this->pattern, $value ) ) {
            return true;
        }

        return false;
    }

    /**
     * Rule error message
     *
     * @return mixed|void
     */
    public function message() {
        if($this->message) {
            return $this->message;
        }
        return $this->field . " must be alpha";
    }
}