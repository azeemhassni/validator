<?php namespace azi;

use azi\Exceptions\KeyExistsException;

/**
 * Class Validator
 *
 * @package    azi
 * @author     Azi Baloch <http://www.azibaloch.com>
 * @version    1.0
 * @license    The MIT License (MIT)
 *             The MIT License (MIT)
 *             Copyright (c) [2015] [Azi Baloch]
 *             Permission is hereby granted, free of charge, to any person obtaining a copy
 *             of this software and associated documentation files (the "Software"), to deal
 *             in the Software without restriction, including without limitation the rights
 *             to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *             copies of the Software, and to permit persons to whom the Software is
 *             furnished to do so, subject to the following conditions:
 *             The above copyright notice and this permission notice shall be included in all
 *             copies or substantial portions of the Software.
 *             THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *             IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *             FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *             AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *             LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *             OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *             SOFTWARE.

 */
class Validator {


    const SESSION_DATA_KEY = 'form_validation_errors';

    /**
     * RegExp patterns
     *
     * @var array
     */
    public $expressions = [ ];

    /**
     * Custom RegExp error messages
     *
     * @var array
     */
    private $error_messages = [ ];

    /**
     * holds validation errors
     *
     * @var array
     */
    private $validation_errors = [ ];

    /**
     * @var array
     */
    private static $errors = [ ];

    /**
     * @var null
     */
    private static $instance = null;


    /**
     * @var array
     */
    private $builtin_rules = [ ];

    /**
     * Base name space for Rule Classes
     *
     * @var string
     */
    protected $rulesBaseNamespace = 'azi\Rules';

    /**
     *  Class Constructor
     */
    public function __construct() {
        static::$instance = $this;
    }


    /**
     * @param $key
     * @param $expression
     * @param null $message
     *
     * @return bool
     * @throws \Exception
     */
    public function registerExpression( $key, $expression, $message = null ) {
        if ( ! isset( $this->expressions[ $key ] ) ) {
            $this->expressions[ $key ] = $expression;
            if ( $message ) {
                $this->error_messages[ $key ] = $message;
            }

            return true;
        }

        throw new KeyExistsException( "Expression key already exists" );

    }

    /**
     * @param $key
     * @param $newExpression
     * @param null $message
     *
     * @return bool
     */
    public function updateExpression( $key, $newExpression, $message = null ) {
        if ( $this->expressions[ $key ] ) {

            $this->expressions[ $key ] = $newExpression;
            if ( $message ) {
                $this->error_messages[ $key ] = $message;
            }

            return true;
        }

        return false;
    }

    /**
     * returns true if validation passed
     * @return bool
     */
    public function passed() {
        if ( count( self::$errors ) < 1 ) {
            return true;
        }

        return false;
    }

    /**
     * this will return true if validation fails
     * @return bool
     */
    public function failed(){
        if ( count( self::$errors ) > 0 ) {
            return true;
        }

        return false;
    }

    /**
     * retrieve error messages after validation
     *
     * @return array
     */
    public function getErrors() {
        return self::$errors;
    }


    /**
     * Get error message of a field
     *
     * @param $fieldKey
     *
     * @return mixed
     */
    public static function error( $fieldKey, $template = null ) {

        if ( ! session_id() ) {
            session_start();
        }

        if ( isset( $_SESSION[ static::SESSION_DATA_KEY ] ) ) {
            if ( count( $_SESSION[ static::SESSION_DATA_KEY ] ) > 0 ) {
                self::$errors = $_SESSION[ static::SESSION_DATA_KEY ];
                unset( $_SESSION[ static::SESSION_DATA_KEY ] );
            }
        }

        if ( isset( self::$errors[ $fieldKey ] ) ) {
            $message = self::$errors[ $fieldKey ];
            if ( ! is_null( $template ) ) {
                $message = str_ireplace( ":message", $message, $template );
            }

            return $message;
        }

        return false;
    }


    /**
     * @param $char
     * @param $string
     *
     * @return bool
     */
    public function findChar( $char, $string ) {
        if ( preg_match( "#{$char}#", $string ) ) {
            return true;
        }

        return false;
    }


    /**
     * @param array $fields the array of form fields - ( $_POST , $_GET, $_REQUEST )
     * @param $rules
     *
     * @return Validator $this
     */
    public function validate( $fields, $rules ) {

        // loop through rules array
        foreach ( $rules as $field => $ruleString ) {
            $value    = $fields[ $field ];

            // rules string to array required|email will bary [required,email]


            if($this->isConditional($ruleString)) {
                $ruleObj = $this->conditionalIf($ruleString);

                if($this->isConditionMatches($fields, $ruleObj->field, $ruleObj->value)) {
                    $ruleString = $ruleObj->rules;
                } else {
                    // skip execution to next
                    continue;
                }
            }

            $theRules = $this->extractRules( $ruleString );

            foreach ( $theRules as $theRule ) {

                // extract custom messages passed with rule eg. email--Invalid Email
                $message = $this->extractCustomMessage( $theRule );

                $this->validateByRule( $field, $value, $theRule, $message );


                // check if current rule is registered by user at runtime
                if ( array_key_exists( $theRule, $this->expressions ) ) {
                    // run the validation against Runtime registered regular expression
                    $this->validateAgainstExpression( $field, $value, $theRule, $message );
                }

            }

        }

        return $this;
    }

    /**
     * @param $rule
     *
     * @return bool
     */
    private function isConditional($rule){;

        if($this->findChar('if:', $rule)) {
            return true;
        }

        return false;
    }


    /**
     * @param $rules
     *
     * @return mixed
     */
    private function conditionalRules( $rules ) {
        return $this->conditionalIf($rules)->rules;
    }

    /**
     * @param $rule
     *
     * @return object
     */
    private function conditionalIf($rule){
        preg_match( "#if:(.*)\\[(.*)\\]\\((.*)\\)#", $rule, $matches );
        $result = [
            'field' => $matches[1],
            'value' => $matches[2],
            'rules' => $matches[3]
        ];

        return (object) $result;
    }


    /**
     * Convert an array key to Label eg. full_name to Full Name
     *
     * @param $key
     *
     * @return string
     */
    private function keyToLabel( $key ) {
        return ucwords( str_replace( [ '-', '_', '+' ], " ", $key ) );
    }

    /**
     * save errors in session and go back to form
     */
    public function goBackWithErrors() {
        if ( ! session_id() ) {
            session_start();
        }

        $_SESSION[ static::SESSION_DATA_KEY ] = self::$errors;


        header( 'Location: ' . $_SERVER[ 'HTTP_REFERER' ] );
        exit;
    }

    /**
     * Returns error message passed with a rule
     * @param $theRule
     *
     * @return null|mixed
     */
    public function extractCustomMessage( $theRule ) {

        if ( $this->findChar( '--', $theRule ) ) {
            return end( explode( '--', $theRule ) );
        }

        return null;

    }


    /**
     * explode/split rules
     * @param $rules
     *
     * @return array
     */
    public function extractRules( $rules ) {

        if ( $this->findChar( '|', $rules ) ) {
            return explode( '|', $rules );
        }

        return [ $rules ];
    }

    /**
     * Validate a rule against a custom registered expression
     * @param $field
     * @param $value
     * @param $rule
     * @param null $message
     *
     * @return bool
     */
    private function validateAgainstExpression( $field, $value, $rule, $message = null ) {
        if ( preg_match( $this->expressions[ $rule ], $value ) ) {
            return true;
        }

        if ( ! $message ) {
            $message = $this->error_messages[ $rule ];
        }

        static::$errors[ $field ] = $message;

        return false;
    }

    /**
     * Validate a Rule against built-in rules
     * @param $field
     * @param $value
     * @param $rule
     * @param null $message
     *
     * @return mixed
     */
    private function validateByRule( $field, $value, $rule, $message = null ) {
        $ruleClassName = $this->ruleToClassName( $this->getRuleName( $rule ) );

        if ( ! class_exists( $ruleClassName ) ) {
            return false;
        }

        $ruleObject = new $ruleClassName();

        if ( $this->isLengthRule( $rule ) ) {
            $ruleObject->setLength( $this->extractLength( $rule ) );;
        }
        $result = $ruleObject->run( $this->keyToLabel( $field ), $value, $message );

        if ( ! $result ) {
            static::$errors[ $field ] = $ruleObject->message();
        }

        return $result;
    }

    /**
     * returns length from length rules eg. 6 from min:6 and 9 from max:9
     * @param $rule
     *
     * @return mixed
     */
    private function extractLength( $rule ) {
        return end( explode( ':', $rule ) );
    }

    /**
     * detects a rule is length rule
     * @param $rule
     *
     * @return bool
     */
    private function isLengthRule( $rule ) {
        return $this->findChar( 'min:', $rule ) || $this->findChar( 'max:', $rule );
    }


    /**
     * extracts rule name from rule string
     * @param $ruleName
     *
     * @return string
     */
    private function getRuleName( $ruleName ) {
        if ( $this->findChar( '--', $ruleName ) ) {
            $ruleName = explode( '--', $ruleName )[ 0 ];
        } else if ( $this->findChar( ':', $ruleName ) ) {
            $ruleName = explode( ':', $ruleName )[ 0 ];
        }

        return ucwords( $ruleName ) . "Rule";
    }

    /**
     * converts a rule name to its corresponding class name
     * eg. alpha to azi\Rules\AlphaRule
     *
     * @param $rule
     *
     * @return string
     */
    private function ruleToClassName( $rule ) {
        return $this->rulesBaseNamespace . '\\' . $rule;
    }

    /**
     * @param $fields
     * @param $field
     * @param $value
     *
     * @return bool
     */
    private function isConditionMatches( $fields, $field, $value ) {
        if($fields[$field] == $value ) {
            return true;
        }

        return false;
    }


}
