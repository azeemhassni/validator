<?php namespace azi\Rules;


use azi\RuleInterface;

/**
 * Class NumRule
 *
 * @package azi\Rules
 */
class NumRule implements RuleInterface
{

    /**
     * @var string
     */
    protected $pattern = '#^([0-9])+$#';

    /**
     * field label eg. Price
     * @var null
     */
    protected $field = null;

    /**
     * holds the error message to be returned
     * @var null
     */
    protected $message = null;

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return mixed
     */
    public function run( $field, $value, $message = null ) {
        $this->field   = $field;
        $this->message = $message;
        if ( preg_match( $this->pattern, $value ) ) {
            return true;
        }

        return false;
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
        return $this->field . " must be numeric";
    }
}
