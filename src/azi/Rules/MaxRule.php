<?php namespace azi\Rules;

use azi\Exceptions\LengthNotSetException;
use azi\RuleInterface;

/**
 * Class MaxRule
 *
 * @package azi\Rules
 */

class MaxRule implements RuleInterface
{

    /**
     * Field label
     *
     * @var null
     */
    protected $field = null;

    /**
     * the error message to be returned
     *
     * @var null
     */
    protected $message = null;

    /**
     * length to be compared
     *
     * @var null
     */
    private $length = null;

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return bool|mixed
     * @throws LengthNotSetException
     */
    public function run( $field, $value, $message = null ) {
        $this->field   = $field;
        $this->message = $message;
        if ( $this->length ) {
            if ( strlen( $value ) <= $this->length ) {
                return true;
            }
        } else {
            throw new LengthNotSetException( "Length must be set before running the validation" );
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

        return $this->field . " must be less than  " . $this->length . " characters";
    }

    /**
     * set length for rule
     *
     * @param $length
     */
    public function setLength( $length ) {
        $this->length = $length;
    }

}
