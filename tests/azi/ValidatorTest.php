<?php namespace azi;

/**
 * @property Validator validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase {

    public function setUp(){
        $this->validator = new Validator();
    }

    public function tearDown(){
        $this->validator->clear();
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
        $this->validator->validate(['username' => 'john'], ['username' => 'alnum']);
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

    
    public function testEqualMinLengthValidation(){
        $this->validator->validate(['title' => 'Length of this string is 35 symbols'], ['title' => 'max:35']);
        $this->assertTrue($this->validator->passed());
    }


    public function testEqualMaxLengthValidation(){
        $this->validator->validate(['description' => 'Length of this string is 35 symbols'], ['description' => 'min:35']);
        $this->assertTrue($this->validator->passed());
    }
    
    public function testNotEqualMinLengthValidation(){
        $this->validator->validate(['title' => 'Length of this string is 35 symbols'], ['title' => 'max:34']);
        $this->assertFalse($this->validator->passed());
    }
    
    
    public function testNotEqualMaxLengthValidation(){
        $this->validator->validate(['description' => 'Length of this string is 35 symbols'], ['description' => 'min:36']);
        $this->assertFalse($this->validator->passed());
    }

    public function testConditionalValidation(){
        $this->validator->validate(['gender' => 'female','age' => ''], ['gender' => 'required','age' => 'if:gender[male](required)']);
        $this->assertTrue($this->validator->passed());
    }

    /**
     * @expectedException azi\Exceptions\KeyExistsException
     */
    public function testThrownExceptionIfExistingExpressionKeyPassed(){
        $this->validator->registerExpression("alpha","#^[a-zA-Z]$#");
        // lets try to override by registering the expression again
        $this->validator->registerExpression("alpha","#^[a-zA-Z]$#");
    }


    public function testFindCharMethodWorking(){
        $this->assertTrue($this->validator->findChar("if:","if:country[Pakistan](required|alpha)"));
    }

    public function testValidatorIsValidatingIPFields(){
        $this->validator->validate(['ip' => '127.0.0.1'],['ip' => 'ip']);
        $this->assertTrue($this->validator->passed());
    }

    public function testValidatorIsValidatingUrlField(){
        $this->validator->validate(['url' => 'http://example.com'],['url' => 'url']);
        $this->assertTrue($this->validator->passed());
    }
    
    public function testSameRuleWithCustomMessage(){
            $this->validator->validate([
                'email' => 'john@example.com',
                'confirm_email' => 'john@example.com'
                ],['email' => 'required', 'confirm_email' => 'same:email--Enter the same email you typed above!']);
            $this->assertTrue($this->validator->passed());
        
    }


    public function testAddingCustomRules(){

        $this->validator->addRule('myname', function($field, $value){
            if($value != "Azi Baloch"){
                return $field." must be Azi Baloch";
            }

            return true;
        });

        $this->validator->validate(['name' => 'Azi Baloch'], ['name' => 'myname']);

        $this->assertTrue($this->validator->passed());

    }

}
 
