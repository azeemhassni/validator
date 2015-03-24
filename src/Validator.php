<?php namespace azi;

/**
 * Class Validator
 *
 * @package azi
 * @author  Azi Baloch <http://www.azibaloch.com>
 * @version 1.0
 * @license The MIT License (MIT)
 *
 *   The MIT License (MIT)
 *
 *   Copyright (c) [2015] [Azi Baloch]
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 *
 */


class Validator {


    /**
     * RegExp patterns
     *
     * @var array
     */
    private $expressions = [ ];

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
     * @var string
     */
    private static $session_data_key = "form_validation_errors";

    /**
     *  Class Constructor
     */
    public function __construct() {

        // load built-in expressions
        $this->expressions = array(
            'alpha'     => '#^([a-zA-Z\s])+$#',
            'num'       => '#^([0-9])+$#',
            'alpha-num' => '#^([a-zA-Z0-9\s])$#',
        );

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

        throw new \Exception( "Expression key already exists" );

    }

    /**
     * @param $key
     * @param $newExpression
     * @param null $message
     *
     * @return bool
     * @throws \Exception
     */
    public function updateExpression( $key, $newExpression, $message = null ) {
        if ( $this->expressions[ $key ] ) {

            $this->expressions[ $key ] = $newExpression;
            if ( $message ) {
                $this->error_messages[ $key ] = $message;
            }

            return true;
        }

        throw new \Exception( "Expression dose not exists" );
    }

    /**
     * @return bool
     */
    public function passed() {
        if ( count( $this->validation_errors ) < 1 ) {
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
        return $this->validation_errors;
    }



    /**
     * Get error message of a field
     *
     * @param $fieldKey
     *
     * @return mixed
     */
    public static function error( $fieldKey ) {

        if(!session_id()) {
            session_start();
        }

        if(isset($_SESSION[static::$session_data_key])) {
            if(count($_SESSION[static::$session_data_key]) > 0) {
                self::$errors = $_SESSION[ static::$session_data_key ];
                unset($_SESSION[static::$session_data_key]);
            }
        } else {
            if(!is_null(static::$instance)) {
                self::$errors = static::$instance->validation_errors;
            }
        }

        if ( isset( self::$errors[ $fieldKey ][ 'message' ] ) ) {
            return self::$errors[ $fieldKey ][ 'message' ];
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


        $return = [ ];

        foreach ( $fields as $key => $field ) {

            if ( ! array_key_exists( $key, $rules ) ) {
                continue;
            }

            $r       = $rules[ $key ];
            $matches = [ ];
            $not     = false;
            if ( preg_match( '#if:#', $r ) ) {

                preg_match( "#if:(.*)\\[\\!(.*)\\]:(.*)\\[!(.*)\\]\\((.*)\\)#", $r, $matches );

                if ( count( $matches ) < 1 ) {
                    preg_match( "#if:(.*)\\[(.*)\\]\\((.*)\\)#", $r, $matches );
                } else {
                    $not = true;
                }

                $r = end( $matches );

            }

            if ( ! strpos( $r, "|" ) ) {
                $r .= "|IGNORE_ME5";
            }


            $theRules = explode( "|", $r );


            foreach ( $theRules as $theRule ) {

                if ( $theRule == "IGNORE_ME5" ) {
                    continue;
                }
                $customMessage = [ ];
                if ( strpos( $theRule, "--" ) ) {
                    $rcm     = explode( "--", $theRule ); // custom message for current rule
                    $theRule = $rcm[ 0 ];
                    if ( strpos( $rcm[ 0 ], ":" ) ) {
                        $rcm[ 0 ] = explode( ":", $rcm[ 0 ] )[ 0 ];
                    }
                    $customMessage[ $rcm[ 0 ] ] = $rcm[ 1 ];
                }


                if ( count( $matches ) > 0 ) {

                    if ( $not ) {
                        if ( $fields[ $matches[ 1 ] ] == $matches[ 2 ] && $fields[ $matches[ 3 ] ] == $matches[ 4 ] ) {
                            continue;
                        }
                    } else {
                        if ( $fields[ $matches[ 1 ] ] != $matches[ 2 ] ) {
                            continue;
                        }
                    }
                }


                if ( strtolower( $theRule ) == "required" ) {
                    if ( ! empty( $customMessage[ 'required' ] ) ) {
                        $theMessage = $customMessage[ 'required' ];
                    } else {
                        $theMessage = $this->keyToLabel( $key ) . ' is required';
                    }
                    if ( $field == "" ) {
                        $return[ $key ] = [
                            'error'   => 'required',
                            'message' => $theMessage
                        ];
                        continue;
                    }
                }


                if ( strtolower( $theRule ) == "alpha" ) {
                    if ( ! preg_match( $this->expressions[ 'alpha' ], $field ) ) {
                        if ( ! empty( $customMessage[ 'alpha' ] ) ) {
                            $theMessage = $customMessage[ 'alpha' ];
                        } else {
                            $theMessage = $this->keyToLabel( $key ) . ' must not contain numbers and special characters';
                        }
                        $return[ $key ] = [
                            'error'   => 'alpha',
                            'message' => $theMessage
                        ];
                        continue;
                    }
                }

                if ( strtolower( $theRule ) == "num" ) {

                    if ( ! empty( $customMessage[ 'num' ] ) ) {
                        $theMessage = $customMessage[ 'num' ];
                    } else {
                        $theMessage = $this->keyToLabel( $key ) . ' may only contain numbers';
                    }

                    if ( ! preg_match( $this->expressions[ 'num' ], $field ) ) {
                        $return[ $key ] = [
                            'error'   => 'num',
                            'message' => $theMessage
                        ];
                        continue;
                    }
                }


                if ( strtolower( $theRule ) == "alpha-num" ) {

                    if ( ! empty( $customMessage[ 'alpha-num' ] ) ) {
                        $theMessage = $customMessage[ 'alpha-num' ];
                    } else {
                        $theMessage = $this->keyToLabel( $key ) . ' may only contain alpha numeric characters';
                    }


                    if ( ! preg_match( $this->expressions[ 'alpha-num' ], $field ) ) {
                        $return[ $key ] = [
                            'error'   => 'alpha-num',
                            'message' => $theMessage
                        ];
                        continue;
                    }
                }


                if ( strpos( $theRule, ':' ) ) {
                    $theRule = explode( ":", $theRule );
                    if ( strtolower( $theRule[ 0 ] ) == "min" ) {
                        if ( ! empty( $customMessage[ 'min' ] ) ) {
                            $theMessage = $customMessage[ 'min' ];
                        } else {
                            $theMessage = $this->keyToLabel( $key ) . ' must be at least ' . $theRule[ 1 ] . " characters long";
                        }

                        if ( strlen( $field ) < $theRule[ 1 ] ) {
                            $return[ $key ] = [
                                'error'   => 'min',
                                'message' => $theMessage
                            ];
                            continue;
                        }
                    }

                    if ( strtolower( $theRule[ 0 ] ) == "max" ) {

                        if ( ! empty( $customMessage[ 'max' ] ) ) {
                            $theMessage = $customMessage[ 'max' ];
                        } else {
                            $theMessage = $this->keyToLabel( $key ) . ' must be less than ' . $theRule[ 1 ] . " characters";
                        }

                        if ( strlen( $field ) > $theRule[ 1 ] ) {
                            $return[ $key ] = [
                                'error'   => 'max',
                                'message' => $theMessage
                            ];
                            continue;
                        }
                    }
                }

                /* Custom Expressions */
                if ( array_key_exists( $theRule, $this->expressions ) ) {

                    if ( ! preg_match( $this->expressions[ $theRule ], $field ) ) {
                        if ( array_key_exists( $theRule, $this->error_messages ) ) {
                            $error_message = $this->error_messages[ $theRule ];
                        } else {
                            $error_message = $this->keyToLabel( $key ) . ' dose\'t match the required pattern';
                        }
                        $return[ $key ] = [
                            'error'   => $theRule,
                            'message' => $error_message
                        ];
                        continue;
                    }

                }

            }

        }

        $this->validation_errors = $return;

        return $this;
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


    public function goBackWithErrors(){
        if(!session_id()) {
            session_start();
        }

        $_SESSION[static::$session_data_key] = $this->validation_errors;

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }




}