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

use Firebase\JWT\JWT;
use Php101\Php101\Psh;
use \PDO;
use \Exception;

class UserInterface extends ErrorLogger{

    /**
     * @property string userDI;
     * 
     */

    protected $userID;

    /**
     * @property object database connection object
     */

    protected $conn;
    /**
     * creates an instance of a User
     * @param string UserID
     * 
     */


    public function __construct(string $userID, PDO $conn){
        $this->userID = $userID;
        $this->conn = $conn;
    }


    /**
     * 
     * This method creates a new user if they dont already exists.
     * This method genertates a random userID using sha256 hashing algorithm
     * This methos takes in a pre-hashed password
     * 
     * @param string userName
     * @param string email
     * @param string phoneNumber
     * @param string password pre-hashed password from the password class
     * @return string usersId 
     * @return string error('could not create user, user alredy exists')
     * @return string error('throwable error')
     * 
     */

    public static function creatUser(object $conn,string $userName, string $email, string $phoneNumber, string $passWord, string $fname, string $lname, string $role){

        try {

            $user = new UserInterface(hash('sha256',random_bytes(32)),$conn); //instanciate the user class and pass in the user id

            //check if usrname already exists
            $stmt = $conn->prepare('SELECT `user_id` FROM `users` WHERE `user_name` = ?');
            $stmt->execute([$userName]);
    
            if($stmt->fetch()){
                return 'err1';
            }
            // create new user if the username does not exist
    
            $stmt = $conn->prepare('INSERT INTO `users`(`user_id`, `user_name`, `fname`, `lname`, `password`, `email`, `phone`, `role`) 
            VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$user->getUserId(),$userName,$fname,$lname,$passWord,$email,$phoneNumber,$role]);
    
            //return the users id
            return $user->getUserId();
    
        } catch(\Throwable $e) {
            //throw $e
            new ErrorLogger($e->getMessage(),null,$user->conn);
            return 'err2';
        }
    
    }

    /**
     * this method logs in the user
     * 
     * @param object the connection obejct
     * @param array the users credentials
     * @return bool wether the user is loged in or not.
     * 
     */

    public static function loginUser (object $conn, array $userCred, string $secreteKey, string $encryptionKey) {
        try {
            $stmt = $conn->prepare("SELECT users.user_id,users.password FROM `users` WHERE `email` = ?");
            $stmt->execute([$userCred['email']]);
            $result = $stmt->fetchAll();
            $count = count($result);
    
            if ($count <= 0 or $count > 1) {
    
                return false;
    
            }else {
    
                if (Passwordhandler::validate_password($result[0]['password'],$userCred['password'])) {

                    $payLoad = array (
                        // 'user_id' => 
                    );
                    return true;
                }else {
                    return false;
                }
            }
    
        } catch (\Throwable $e) {
            new ErrorLogger($e->getMessage(),null,$conn->conn);
            die('something went wrong');
        }
    }


    /**
     * 
     * get the users id
     * 
     * @return string userID
     * 
     */
    public function getUserId():string{

        return $this->userID;

    }

    /**
     * 
     * deletes a user 
     * 
     * @return bool returns true if user if deleted 
     * @return bool returns false if he user is not deleted
     * 
     */

    public function deleteUser() {

        try {

            //check if user  exists
            $stmt = $this->conn->prepare('SELECT `user_id` FROM `users` WHERE `user_id` = ?');
            $stmt->execute([$this->userID]);

            if(!$stmt->fetch()){
                return false;
            } 

            // if user exists delete them
            $stmt = $this->conn->prepare('DELETE FROM `users` WHERE `user_id` = ?');

            $stmt->execute([$this->userID]);

            return true;

        } catch (\Throwable $e) {
            new ErrorLogger($e->getMessage(),null,$this->conn);
            return false;
        }

    }

    /**
     * 
     * retrieves a specified value from database (e.g,'password,lname,fname,phone,email')
     * @param string field the field you are querying
     * @param string table the table form which the information si to be retrieved
     * 
     * @return mixed
     */

    public function getUserInfo(string $field, string $table){

        try {

            // check if field exists in tables colums 
            $stmt = $this->conn->prepare('DESCRIBE `users`');
            $stmt->execute();
            $colums = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if(!in_array($field, $colums)){
                throw new Exception('the field you gave does not exist',2);
            }

            //execute the query
            $stmt = $this->conn->prepare('SELECT '.$field.' FROM '.$table.' WHERE user_id = ?');
            $stmt->execute([$this->userID]);

            // fetch the result and return the requested field value or false if it could not be found
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result[$field] : throw new Exception('doesnt exist');

        } catch (\Throwable $e) {
            //throw $e;
            new ErrorLogger($e->getMessage(),null,$this->conn);
            return false;
        }
    }

    /**
     * 
     * updates a specified value in database (e.g,'password,lname,fname,phone,email')
     * 
     * @param string field the field you are querying
     * @param string table the table form which the information si to be retrieved
     * @param string value the update value
     * 
     * @return bool
     */

    public function updateUserInfo(string $field, string $table, string $value) : bool{

        try {

            //check if value is a password
            if($field === 'password'){
                throw new Exception('the requested method can not be used to update the password');
            }
            // check if field exists in tables colums 
            $stmt = $this->conn->prepare('DESCRIBE `users`');
            $stmt->execute();
            $colums = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if(!in_array($field, $colums)){
                throw new Exception('the field you gave does not exist',2);
            }

            //execute the query
            $stmt = $this->conn->prepare('UPDATE `'.$table.'` SET `'.$field.'`= ?  WHERE `user_id` = ?');
            if(!$stmt->execute([$value,$this->userID])){
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            //throw $th;
            new ErrorLogger($e->getMessage(),null,$this->conn);
            return false;

        }
    }

    /**
     * this method checks for the login status of the user.
     * 
     * @return bool weather loged in or not .
     * 
     */

    public static function islogedin(){

        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }
        if(!isset($_SESSION['user_id'])){
            return false;
        }
                        
        if(session_id() !== $_SESSION['session_id']){
            session_regenerate_id(true);
            $_SESSION['session_id'] = session_id();
        }

        if(isset($_SESSION['islogedin'])){
            
            if($_SESSION['islogedin'] === true){
                return true;
            }else{
                return false;
            }
        }

        return false;
    }

}

