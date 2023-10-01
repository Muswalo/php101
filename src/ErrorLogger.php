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

use Exception;

class ErrorLogger{
    /**
     * this instaciates a new error and logs it to the database
     */
    public function __construct(string $msg, $time, $conn){
        try {
            
            $stmt = $conn->prepare('INSERT INTO `errors`(`time`, `msg`) VALUES (?,?)');        
            $stmt->execute([$time,$msg]);
    
        } catch (\Throwable $th) {
            throw new Exception("something went wrong when logging the error: {$msg}");
            // die('something went wrong error logger');
        }
    }
}