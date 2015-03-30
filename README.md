# PHP Server Side Form Validation

This is a small PHP class that makes it easy to validate forms in your project specially larger forms. 


## Installation Guide:
You can install **Validator** either via package download from github or via composer install. I encourage you to do the latter:
 
```json  
{ 
  "require": {
    "azi/validator": "dev-master"
  }
} 
```


##Usage 
to get started 

* require composer autoloader 

```php
require __DIR__ . '/../vendor/autoload.php';
```

* Instantiate the Validator class
```php
use azi\Validator;
$v = new Validator();
```
* Define Rules for each form field
```php
  $rules = array(
        'name' => 'alpha|required',
        'age'  => 'num|required',
    );
```
* Run the **validator** 
```php
$v->validate( $_POST, $rules );
```
* check validator for errors, if validation fails redirect back to the form
```php
if ( !$v->passed() ) {
        $v->goBackWithErrors();
    }
```

* show validation errors to user
```html
 <label>Name :
      <input type="text" name="name">
 </label>
 <?= Validator::error('name') ? Validator::error('name') : ""; ?>
```

you can wrap error messages with custom HTML markup

```php
  Validator::error('confirm_password', '<span class="error">:message</span>');
```


## Rules
 * required
 * num
 * alpha
 * alpha-num
 * email
 * min:[number]
 * max:[number]
 * same:[field_name]


## Custom Expressions & Messages
* Custom Expressions

you can register your custom RegExp before running the validator

```php
$v->registerExpression( 'cnic', '#^([0-9]{13})$#', 'Invalid CNIC number' );
```

registerExpression method takes 3 arguments
* expressionID - unique name for the expression
* pattern - the RegExp string
* message [optional] - the error message to be retured if the validation fails

```Validator::registerExpression($expressionID , $pattern, $message)```


* Custom Messages

you can also pass a custom error message with each rule

```php
 $rules['full_name'] = "required--Please Enter your name|alpha-- Please don't use special charators and numbers";
```

## Conditional Rules
you can spacify conditional rules for a field
```php
 $rules['age'] = 'if:gender[Male](required|min:2|num)';
```

## Comparison Rules

you can also compare a field with another
```php
  $rules['password'] = 'required|min:8';
  $rules['confirm_password'] = 'same:password';
```


