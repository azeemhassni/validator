<?php namespace azi\Rules;

use azi\RuleInterface;

/**
 * Class EmailRule
 *
 * @package azi\Rules
 */
class EmailRule implements RuleInterface
{

    protected $pattern = '#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$#';
    protected $field = null;
    protected $message = null;

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return bool|mixed
     */
    public function run( $field, $value , $message = null)
    {
        $this->field = $field;
        $this->message = $message;

        if(preg_match($this->pattern, $value)){
            return true;
        }

        return false;
    }

    /**
     * Rule error message
     *
     * @return mixed
     */
    public function message()
    {
        if($this->message) {
            return $this->message;
        }

        return $this->field . " must contain a valid email address";
    }
}
