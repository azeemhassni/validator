<?php namespace azi\Rules;

use azi\RuleInterface;

/**
 * Class RequiredRule
 *
 * @package azi\Rules
 */
class RequiredRule implements RuleInterface
{

    /**
     * this holds the field name
     *
     * @var null
     */
    protected $field = null;

    /**
     * this holds the error message
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
        if ( ! empty( trim($value) )  ) {
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

        return $this->field . " is required ";
    }
}