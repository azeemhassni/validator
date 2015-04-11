<?php namespace azi;

/**
 * Interface Rule
 *
 * @package azi
 */

interface RuleInterface
{

    /**
     * @param $field
     * @param $value
     * @param null $message
     *
     * @return mixed
     */
    public function run($field, $value, $message = null);

    /**
     * Rule error message
     * @return mixed
     */
    public function message();
} 