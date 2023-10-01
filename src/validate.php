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

namespace Php101\Php101;
use \Exception;
// this class validates the input
class validate{
    private $inputData; private $Errors; private $Rules;

    /**
     * @param array $inputData is an array for the user input fields
     * @param array $Rules is an array for the validation Rules
     * @property array $Errors is a container for validation error that might occur
     */

     public function __construct(array $inputData, array $Rules){
        $this->inputData = $inputData;
        $this->Rules = $Rules;
     }

     /**
      * initialise the rules and call method to validate the data
      * @return void this method doest return anything 
      */

     public function validate():void {

        foreach ($this->Rules as $field => $Rules) {

          foreach ($Rules as $rule) {
            $params = [];
    
            // Check if the rule has any parameters (e.g. "min:3")
            if (strpos($rule, ':') !== false) {
              list($rule, $param) = explode(':', $rule);
              $params = explode(',', $param);
            }
    
            // Generate the validation method name based on the rule name
            $method = 'validate' . ucfirst($rule);
    
            // Check if the validation method exists
            if (!method_exists($this, $method)) {
              throw new Exception("Invalid validation rule: $rule");
            }
    
            // Call the validation method with the field name and any parameters
            call_user_func_array([$this, $method], array_merge([$field], $params));
          }

        }

      }

    
      /**
       * 
       * get errors if any
       * 
       * @return error return an error if any
       */
      public function getErrors():array{
        return $this->Errors;
      }
    

      /**
       * 
       * get the number of errors returned
       * 
       * 
       * @return count number of errors returned
       */

      public function hasErrors(): int {
        if (is_array($this->Errors)) {

          return count($this->Errors) > 0;
          

        }
        return 0;
      }


      /**
       * 
       * check for empty fields
       * 
       * 
       * @return void this method returns void
       */

      private function validateRequired($field):void {
        if (empty($this->inputData[$field])) {
          $this->addError($field, "The $field field is required.");
        }
      }


      /**
       * validate the email address
       * 
       * @return void this method returns void
       */

      private function validateEmail($field):void {
        if (!filter_var($this->inputData[$field], FILTER_VALIDATE_EMAIL)) {
          $this->addError($field, "The $field field must be a valid email address.");
        }
      }



      /**
       * validate the minimun number of chars required
       * 
       * @return void this method returns void
       */
      private function validateMin(string $field, int $min):void {
        if (strlen($this->inputData[$field]) < $min) {
          $this->addError($field, "The $field field must be at least $min characters long.");
        }
      }


      /**
       * vlaidte the maximum require chars
       * 
       * @return void this method returns void
       */
      private function validateMax(string $field, int $max):void {
        if (strlen($this->inputData[$field]) > $max) {
          $this->addError($field, "The $field field must be no more than $max characters long.");
        }
      }


      /**
       * 
       * validate alpha chars
       * 
       * @return void this method returns void
       */
      private function validateAlpha(string $field):void {
        if (!ctype_alpha($this->inputData[$field])) {
          $this->addError($field, "The $field field must only contain alphabetic characters.");
        }
      }


      /**
       * 
       * validate alphanumeric chars
       * 
       * @return void this methosd returns void
       */
      private function validateAlnum(string $field):void {
        if (!ctype_alnum($this->inputData[$field])) {
          $this->addError($field, "The $field field must only contain alphanumeric characters.");
        }
      }

      /**
       * push error to the errors array
       * 
       * @return void this mehod returns void
       */
      private function addError(string $field, string $message):void {
        if (!isset($this->Errors[$field])) {
          $this->Errors[$field] = [];
        }
    
        // Add the error message to the array of Errors for this field
        $this->Errors[$field][] = $message;
      }

    }
    
    
    
    
    