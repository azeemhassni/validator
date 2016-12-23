<?php namespace azi\Rules;


use azi\RuleInterface;

/**
 * Compare a field with an other
 * eg. Password and Confirm Password validation
 * Class SameRule
 *
 * @package azi\Rules
 */
class SameRule implements RuleInterface {

    /**
     * Holds the fields array
     *
     * @var null
     */
    private $fields = null;

    /**
     * the field key to be compared
     *
     * @var null
     */
    private $fieldKey = null;

    /**
     * Field Label
     *
     * @var null
     */
    protected $field = null;

    /**
     * the error message to be returned if validation fails.
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

        if ( ! $this->fieldKey || ! $this->fields ) {
            return false;
        }

        if ( $this->fields[ $this->fieldKey ] == $value ) {
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

        return $this->field . " must be same as " . $this->fieldKey;
    }

    /**
     * Set Rule Required properties
     *
     * @param $fields
     * @param $fieldKey
     */
    public function prepareRule( $fields, $fieldKey ) {
        $this->fields   = $fields;
        $this->fieldKey = $fieldKey;
    }

}
