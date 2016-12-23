<?php namespace azi\Rules;


use azi\RuleInterface;

/**
 * Class AlnumRule
 *
 * @package azi\Rules
 */
class AlnumRule implements RuleInterface
{

    /**
     * RegExp pattern
     *
     * @var string
     */
    protected $pattern = '#^([a-zA-Z0-9])+$#';

    /**
     * @var null
     */
    protected $field = null;

    /**
     * holds the error message to be returned
     *
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
        if ( $this->message ) {
            return $this->message;
        }

        return $this->field . " must not contain special characters";
    }
}
