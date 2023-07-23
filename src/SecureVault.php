<?php
/**
 * Base Two Technologies (https://basetwotech.com)
 * @package   Base Two Technologies
 * @author    Emmanuel Muswalo <emuswalo7@gmail.com>
 * @copyright Copyright (c) 2023, Base Two Technologies
 * @license   MIT license 
 * @country   Zambia
 */

 namespace Muswalo\Php101;
 use Firebase\JWT\JWT;
use \Exception;

class SecureVault {

  /**
   * Encrypts the provided string using sodium_crypto_secretbox($value, $nonce, $key)
   * @param string text
   * @param string key
   * @return string The encrypted text
   * 
   */
  public function cipher(string $value, string $key): string {

    try {
      $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
      $cipher = sodium_crypto_secretbox($value, $nonce, $key);
      return base64_encode($nonce.$cipher);
  
    } catch (\Throwable $e) {
      throw new Exception("Encrptyion failed:".$e->getMessage()); 
    }

  }


  /**
   * deCipher the encrypted string
   * then comparing the decrypted string with the original string.
   * @param string the cipherd string
   * @return bool true if the string is valid, false otherwise.
   * 
   */


  public function deCipher (string $cipher, string $key) : ?string {
    try {
      $decodedValue = base64_decode($cipher);
      $nonce = substr($decodedValue, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
      $cipher = substr($decodedValue, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

      return sodium_crypto_secretbox_open($cipher, $nonce, $key);

    } catch (\Throwable $e) {
      throw new Exception("Deciphering fialed: Could not decrypt the ciphered string: ".$e->getMessage());
    }
  }


  /**
   * retrive jwt from the file helper function
   * @param string data
   * @return string jwt
   * @return bool 
   */

  private function retrieveJWTFromFile(string $data): string|bool {

    // the delimeter or separator of the data and signature
    $delimeter = '::CONTENT::';

    // checks
    if (strpos($data, $delimeter) == false) {
      return false;
    }else {
      $array = explode($delimeter, $data);
    }
    return $array[0];

  }


  /**
   * retrive content from the file helper function
   * @param string data
   * @return string content
   * @return bool 
   */

   private function retrieveContentFromFile(string $data): string|bool {

    // the delimeter or separator of the data and signature
    $delimeter = '::CONTENT::';

    // checks
    if (strpos($data, $delimeter) == false) {
      return false;
    }else {
      $array = explode($delimeter, $data);
    }
    return $array[1];

  }


  /**
   * deciphers the content of a file that was encrypted 
   * 
   * @param string file directory
   * @param string the key used to encrypt
   * @throws files does not exist exeception
   * @throws invalid file format exception
   * @throws invalid json format exception
   * @return array
   * @return bool 
   *
   */

  public function fileDecipher (string $file, string $key): mixed {

    try {
          // check if the file exists
      if (!file_exists($file)) { 

        throw new Exception("The system could not find the file: $file in the specified directory.");
        
      }

      // verify the files extension and filename format ('value_timestamp_signature.enc')
      $fileName = basename($file);
      $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
      $pattern = "/^[^_]+_\d+_[^.]+\.enc$/";

      if (!preg_match($pattern, $file) || $fileExt !== 'enc') {
          throw new Exception("Invalid file name format. value_timestamp_signature.enc expected");
      }

      //get the files content
      $content = file_get_contents($file);
      $dataToDecipher = $this->retrieveContentFromFile($content);


      //decipher the content using the key if it fails return false
      $decipheredContent = $this->deCipher($dataToDecipher, $key);
      if (!$decipheredContent) {
          return false;
      }

      // retrive th jwt from the content
      $jwt = $this->retrieveJWTFromFile($content);

      // check if jwt was found
      if (!$jwt) {
        throw new Exception("Could not verify file integrity");
      }

      // verify the digital signtuure      
      if (!$this->verifySignature($dataToDecipher, $jwt, $key)) {
          return false;
      }

      // decode the content
      $decodedContent =  json_decode($content, true);

      if ($decodedContent === null) {
          throw new Exception("Invalid JSON format in the deciphered content: problem with the file content");
      }
      
      return $decodedContent;

    } catch (\Throwable $e) {
      throw new Exception("fileDecipher fialed:".$e->getMessage());
    }

  }

  /**
   * verifies the digital signature
   * @param string file content
   * @param string the jwt 
   * @param string the key
   * @return bool
   * 
   */

   public function verifySignature ($fileContent, $jwt, $secreteKey) :bool {

    try {
        $decoded =  JWT::decode($jwt, $secreteKey);

        if ($decoded) {
            $decipheredContent = $this->deCipher($fileContent, $secreteKey);
            
            if ($decipheredContent !== false) {
                $hash = hash('sha256', $decipheredContent); 
                $digest = hash('sha256', $hash.$secreteKey.$decoded->headers);
                return $digest === $decoded->signature;
            }
        }

        return false;

    } catch (\Throwable $e) {
      throw new Exception("could not verify signature: ".$e->getMessage());
    }

  }

  /**
   * generates the digital signature
   * @param string the data
   * @param string the encryption key
   * @return string the signature
   * 
   */
  public function generateSignature (string $data,string $key, array $extra = []) :string {
    $payLoad = [
        "extra" => $extra,
        "digest" => $data
    ]; 
    try {
        return JWT::encode($payLoad, $key, 'HS256');
    } catch (\Throwable $e) {
        throw new Exception("Error generating the signature: ".$e->getMessage());
    }
  }

  /**
   * encrypts data and writes it to a file
   * 
   * @param array the data to be encrypted
   * @param string the scerete key to used (SODIUM_CRYPTO_SCERETEBOX_KEY_BYTES)
   * @param string the location of the file.if not given uses the current dir
   * @param string the prefix to attach to the file name. By default is config
   */

  public function encryptToFile (array $fileContent, string $key, string $dir = __DIR__,string $prefix = 'config_') {

    // check if the given directory exists.
    if (!is_dir($dir)) {
        throw new Exception("The given directory: '$dir' is not a directory");
    }

    try {
      // hash the content and encrypt it.
      $hashedContent = hash('sha256', json_encode($fileContent));
      $encryptedContent = $this->cipher(json_encode($fileContent),$key);

      // generate the signature.
      $jwt = $this->generateSignature($hashedContent, $key, ['timeCreated'=>time()]);

      // generate the file name.
      $filename = $prefix.time().'_'.bin2hex(random_bytes(8)).'.enc';
      $path = $dir.$filename;

      // generate the files content structure jwt::CONTENT::content.
      $fileContent = $jwt.'::CONTENT::'.$encryptedContent;

      // create a file if it doest exist and put the encrypted content.
      return file_put_contents($path, $fileContent);

    } catch (\Throwable $e) {
      throw new Exception("Could not encrypt data to file: ".$e->getMessage());
    }
  }
}
