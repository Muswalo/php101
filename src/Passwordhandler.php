<?php
/**
 * Base Two Technologies (https://basetwotech.com)
 * @package   Base Two Technologies
 * @author    Emmanuel Muswalo
 * @email     emuswalo7@gmail.com
 * @copyright Copyright (c) 2023, Base Two Technologies
 * @license   MIT license 
 * @country   Zambia
 */

 namespace Muswalo\Php101;

// use \Exception;

// this class handles passwords
class Passwordhandler {

  private $password;

  /**
   * Constructor function that sets the password to be handled.
   * @param string $password The password to be managed.
   * 
   */


  public function __construct(string $password) {

    $this->password = $password;

  }

  /**
   * Validates the strength of the password, checking for at least 8 characters,
   * one uppercase letter, one lowercase letter, one number, and one special character.
   * @return array returns an array of missing parts
   * 
   */


  public function validateStrength(){
    // this array will contain errors
    $errors = [];

    // this will check for Uppercase letters
    if(!preg_match('@[A-Z]@', $this->password)){
      array_push($errors,'password needs uppercase letter');
    }

    // this will check for lowercase letters
    if(!preg_match('@[a-z]@', $this->password)){
      array_push($errors,'password needs lowercase letter');
    }

    // this will check for numbers
    if(!preg_match('@[0-9]@', $this->password)){
      array_push($errors,'password needs a number');
    }

    // this will check for special cheracters (e.g '#$&*%')
    if(!preg_match('@[^\w]@', $this->password)){
      array_push($errors,'password needs special charecters');
    }

    // this will check the length of the password
    if(strlen($this->password) < 8){
      array_push($errors,'password needs not less 8 charecters');
    }

    // if they are errors the method returns the errors
    if(!empty($errors)){

      return $errors;
        
    }else{
      return [];
    }
  }


  /**
   *  Generate a hashed password given a plain text password
   * @return string the hashed password
   */
  public function hash_password() : string {
    
    return password_hash($this->password, PASSWORD_DEFAULT);

  }

  /**
   * Check if a plain text password matches a hashed password
   * 
   * @param string the hashed password
   * @param string the actual password
   * @return bool return a boolean
   *
   */

  public static function validate_password(string $hash, string $pass) : bool {

    return password_verify($pass, $hash);

  }
}