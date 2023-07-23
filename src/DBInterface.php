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
use \PDO;

class DBInterface{
    private $DBHOST; private $DBNAME; private $DBUSER; private $DBPASS;

    /**
     * create a new database instance
     * 
     * 
     * @param string $DBHOST the database host
     * @param string $DBNAME the database name
     * @param string $DBUSER the database user name
     * @param string $$DBPASS
     * 
     */


    public function __construct(string $DBHOST, string $DBUSER, string $DBPASS, string $DBNAME){

        $this->DBHOST = $DBHOST;
        $this->DBUSER = $DBUSER;
        $this->DBPASS = $DBPASS;
        $this->DBNAME = $DBNAME;
        
    }
    
    /**
     * create a new database instancew
     * 
     * 
     * @return object this method returns a mysqli object
     * @return error it also retuns an error if the connection failed
     */

    public function conn():object{
        try {

            $dbh = new PDO('mysql:host='.$this->DBHOST.';dbname='.$this->DBNAME,$this->DBUSER,$this->DBPASS);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);

            return $dbh; //return the connection object
        } catch (\PDOException $e) {

            return $e; // return an error
            
        }
    }

}