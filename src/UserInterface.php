<?php

/**
 * Class UserInterface
 *
 * This class provides an interface to interact with user-related functionality.
 *
 * @package Php101\Php101
 * @author Emmanuel Muswalo
 * @email emuswalo7@gmail.com
 * @copyright Copyright (c) 2023, Base Two Technologies
 * @license MIT license
 * @country Zambia
 */

namespace Php101\Php101;

use Firebase\JWT\JWT;
use Php101\Php101\Psh;
use \PDO;
use \Exception;

class UserInterface extends ErrorLogger
{

    /**
     * @var string The unique identifier for the user (user ID).
     */

    protected $userID;

    /**
     * @var object The database connection object.
     */

    protected $conn;


    /**
     * Creates an instance of a User.
     *
     * @param string $userID The unique identifier for the user.
     * @param PDO $conn The database connection object.
     */


    public function __construct(string $userID, PDO $conn)
    {
        $this->userID = $userID;
        $this->conn = $conn;
    }

    /**
     * Creates a new user if they don't already exist.
     *
     * This method takes in a pre-hashed password and other user details to create a new user
     * in the database.
     *
     * @param object $conn The database connection object.
     * @param string $user_id The unique identifier for the user.
     * @param string $userName The username of the new user.
     * @param string $email The email address of the new user.
     * @param string $phoneNumber The phone number of the new user.
     * @param string $passWord The pre-hashed password from the password class.
     * @param string $fname The first name of the new user.
     * @param string $lname The last name of the new user.
     * @param string $role The role of the new user.
     *
     * @return string The unique identifier (usersId) of the newly created user.
     * @return string 'err1' if the username already exists (could not create user, user already exists).
     * @return string 'err2' if there is a throwable error during user creation.
     */

    public static function createUser(object $conn, string $user_id, string $userName, string $email, string $phoneNumber, string $passWord, string $fname, string $lname, string $role)
    {

        try {

            $user = new UserInterface($user_id, $conn); //instanciate the user class and pass in the user id

            //check if usrname already exists
            $stmt = $conn->prepare('SELECT `id` FROM `users` WHERE `user_name` = ?');
            $stmt->execute([$userName]);

            if ($stmt->fetch()) {
                return 'err1';
            }
            // create new user if the username does not exist

            $stmt = $conn->prepare('INSERT INTO `users`(`id`, `user_name`,`email`, `phone`, `password`, `first_name`, `last_name`, `role`) 
            VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$user->getUserId(), $userName, $email, $phoneNumber, $passWord, $fname, $lname, $role]);

            //return the users id
            return $user->getUserId();
        } catch (\Throwable $e) {
            //throw $e
            new ErrorLogger($e->getMessage(), null, $user->conn);
            return 'err2';
        }
    }


    /**
     * Generates a UUID with a cryptographically secure random value.
     *
     * @param string $prefix The prefix for the UUID.
     * @return string The generated UUID.
     */
    public static function generateUUID(string $prefix = 'eswap_'): string
    {
        /* Generate cryptographically secure random bytes */
        $randomBytes = random_bytes(16);
        $randomString = bin2hex($randomBytes);
        $microtime = str_replace('.', '', microtime(true));

        /* Concatenate the prefix, random string, and microtime */
        $uuid = $prefix . $randomString . '_' . $microtime;
        return $uuid;
    }


    /**
     * This method logs in the user.
     *
     * @param object $conn The connection object to the database.
     * @param array $userCred The user's credentials.
     * @param string $secreteKey The secret key for JWT.
     * @param string $encryptionKey The encryption key for JWT.
     *
     * @return string|false The JWT token if the login is successful, or false otherwise.
     */
    
    public static function loginUser(object $conn, array $userCred, string $secreteKey, string $encryptionKey)
    {
        try {
            $stmt = $conn->prepare("SELECT users.user_id,users.password FROM `users` WHERE `email` = ?");
            $stmt->execute([$userCred['email']]);
            $result = $stmt->fetchAll();
            $count = count($result);

            if ($count !== 1) {
                return false;
            }

            // Check if the provided password matches the hashed password stored in the database
            if (Passwordhandler::validate_password($result[0]['password'], $userCred['password'])) {
                // User login successful
                $userId = $result[0]['user_id'];

                // Generate a JWT token for the user
                $payload = array(
                    'user_id' => $userId,
                );

                $jwtToken = JWT::encode($payload, $secreteKey, $encryptionKey);

                return $jwtToken;
            } else {
                return false;
            }
        } catch (\Throwable $e) {
            // Handle the exception, log the error if needed, and return false
            new ErrorLogger($e->getMessage(), null, $conn->conn);
            return false;
        }
    }


    /**
     * Get the user's ID.
     *
     * This method returns the unique identifier (user ID) of the current user.
     *
     * @return string The user's unique identifier (user ID).
     */
    public function getUserId(): string
    {

        return $this->userID;
    }


    /**
     * Deletes a user.
     *
     * This method attempts to delete the user with the specified user ID from the database.
     *
     * @return bool Returns true if the user is deleted successfully.
     *              Returns false if the user does not exist or there is an error during deletion.
     */

    public function deleteUser()
    {

        try {

            //check if user  exists
            $stmt = $this->conn->prepare('SELECT `id` FROM `users` WHERE `id` = ?');
            $stmt->execute([$this->userID]);

            if (!$stmt->fetch()) {
                return false;
            }

            // if user exists delete them
            $stmt = $this->conn->prepare('DELETE FROM `users` WHERE `id` = ?');

            $stmt->execute([$this->userID]);

            return true;
        } catch (\Throwable $e) {
            new ErrorLogger($e->getMessage(), null, $this->conn);
            return false;
        }
    }

    /**
     * Retrieves a specified value from the database (e.g., 'password', 'lname', 'fname', 'phone', 'email').
     *
     * This method queries the database to retrieve the specified field's value from the given table
     * for the current user (user ID).
     *
     * @param string $field The field you are querying.
     * @param string $table The table from which the information is to be retrieved.
     *
     * @return mixed The value of the requested field if found, or false if the field does not exist
     *               or there is an error during the query.
     *
     * @throws Exception If the specified field does not exist in the table (with code 2).
     */

    public function getUserInfo(string $field, string $table)
    {

        try {

            // check if field exists in tables colums 
            $stmt = $this->conn->prepare('DESCRIBE `users`');
            $stmt->execute();
            $colums = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array($field, $colums)) {
                throw new Exception('the field you gave does not exist', 2);
            }

            //execute the query
            $stmt = $this->conn->prepare('SELECT ' . $field . ' FROM ' . $table . ' WHERE user_id = ?');
            $stmt->execute([$this->userID]);

            // fetch the result and return the requested field value or false if it could not be found
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result[$field] : throw new Exception('doesnt exist');
        } catch (\Throwable $e) {
            //throw $e;
            new ErrorLogger($e->getMessage(), null, $this->conn);
            return false;
        }
    }

    /**
     * Updates a specified value in the database (e.g., 'password', 'lname', 'fname', 'phone', 'email').
     *
     * This method updates the specified field's value in the given table for the current user (user ID).
     * Note that updating the password is not allowed using this method.
     *
     * @param string $field The field you are querying.
     * @param string $table The table from which the information is to be retrieved.
     * @param string $value The update value for the specified field.
     *
     * @return bool Returns true if the value is successfully updated.
     *              Returns false if the specified field does not exist, or if updating the password is attempted.
     *
     * @throws Exception If updating the password is attempted (with code 1) or if the specified field does not exist (with code 2).
     */

    public function updateUserInfo(string $field, string $table, string $value): bool
    {

        try {

            //check if value is a password
            if ($field === 'password') {
                throw new Exception('the requested method can not be used to update the password');
            }
            // check if field exists in tables colums 
            $stmt = $this->conn->prepare('DESCRIBE `users`');
            $stmt->execute();
            $colums = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array($field, $colums)) {
                throw new Exception('the field you gave does not exist', 2);
            }

            //execute the query
            $stmt = $this->conn->prepare('UPDATE `' . $table . '` SET `' . $field . '`= ?  WHERE `user_id` = ?');
            if (!$stmt->execute([$value, $this->userID])) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            //throw $th;
            new ErrorLogger($e->getMessage(), null, $this->conn);
            return false;
        }
    }

    /**
     * Checks the login status of the user.
     *
     * This method checks whether the user is logged in or not by verifying the presence of
     * 'user_id' and 'islogedin' session variables.
     *
     * @return bool Returns true if the user is logged in.
     *              Returns false if the user is not logged in or session variables are not set.
     */

    public static function islogedin()
    {

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (session_id() !== $_SESSION['session_id']) {
            session_regenerate_id(true);
            $_SESSION['session_id'] = session_id();
        }

        if (isset($_SESSION['islogedin'])) {

            if ($_SESSION['islogedin'] === true) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}
