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
        $value = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value);

        return !empty($value);
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
