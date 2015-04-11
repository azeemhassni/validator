<?php namespace azi;

require "../../src/azi/Validator.php";

/**
 * @property Validator validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase {


    public function setUp(){
        $this->validator = new Validator();
    }

    public function testValidatorIsValidatingEmails(){
        $this->validator->validate(['email' => 'foo@example.com'], ['email' => 'email']);
        $this->assertTrue($this->validator->passed());
    }


    public function testValidatorIsValidatingAlpha(){
        $this->validator->validate(['name' => 'John Doe'], ['name' => 'alpha']);
        $this->assertTrue($this->validator->passed());
    }

    public function testValidatorIsValidatingNumeric(){
        $this->validator->validate(['age' => '20'], ['age' => 'num']);
        $this->assertTrue($this->validator->passed());
    }

    public function testValidatorIsValidatingAlphaNumeric(){
        $this->validator->validate(['bio' => 'My Name is Azi and i am 21 years old'], ['bio' => 'alpha-num']);
        $this->assertTrue($this->validator->passed());
    }

    public function testValidatorValidatingRequiredFields(){
        $this->validator->validate(['full_name' => 'John Doe'], ['full_name' => 'required']);
        $this->assertTrue($this->validator->passed());
    }


    public function testMinLengthValidation(){
        $this->validator->validate(['title' => 'Lorem Ipsum Dummy Title'], ['title' => 'max:30']);
        $this->assertTrue($this->validator->passed());
    }


    public function testMaxLengthValidation(){
        $this->validator->validate(['description' => 'lorem ipsum dummy description'], ['description' => 'min:10']);
        $this->assertTrue($this->validator->passed());
    }


    public function testConditionalValidation(){
        $this->validator->validate(['gender' => 'female','age' => ''], ['gender' => 'required','age' => 'if:gender[male](required)']);
        $this->assertTrue($this->validator->passed());
    }

    /**
     * @expectedException \Exception
     */
    public function testThrownExceptionIfExistingExpressionKeyPassed(){
        $this->validator->registerExpression("alpha","#^[a-zA-Z]$#");
    }


    public function testFindCharMethodWorking(){
        $this->assertTrue($this->validator->findChar("if:","if:country[Pakistan](required|alpha)"));
    }

}
 