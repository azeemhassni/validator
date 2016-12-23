<?php
/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 5/3/2015
 * Time: 9:29 PM
 */

namespace azi\Rules;


use azi\RuleInterface;

class UrlRule implements RuleInterface {


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

        return filter_var( $value, FILTER_VALIDATE_URL ) !== false;
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

        return $this->field . " must be a valid URL";
    }
}