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

/**
 * this class uses an external library called php send this to send emials
 * for any queries visit the github repository and documentation
 * i dont own the rights for the library
 */ 

namespace Php101\Php101; // the classes namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use \Exception;

class MessageInterface extends PHPMailer{

    /**
     * @property string msg, the message to be sent
     * @property string type, the type of message to be sent
     * @property string address, the address to be sent to
     * @property object content, the content if applicable
     * 
     */


    private $type; private $address; private $content;

    /**
     * this class instanciates a new message object (e.g 'email','sms','whatsapp')
     * 
     * @param string msg, the message to be sent.
     * @param string type, the type of messge to be sent. 
     */

    public function __construct(string $type, string $address, array $content){
        $this->address = $address;
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * 
     * sends the requested message type 
     * @return void 
     */

    public function createMsg () : bool {

        $method = 'send'.ucfirst($this->type);

        if (!method_exists($this, $method)) {
            throw new Exception ("Invalid Msg type: $this->type");
        }

        if (call_user_func([$this,$method])) {
            return true;
        }else{
            return true;
        }
    }

    /**
     * this method sends an email
     * @return bool
     * 
     */

    private function sendMail(){
        
        try {
            //Server settings
            // $this->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $this->isSMTP();                                            //Send using SMTP
            $this->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $this->SMTPAuth   = true;                                   //Enable SMTP authentication
            $this->Username   = '';                     //SMTP username
            $this->Password   = '';                               //SMTP password
            $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $this->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $this->setFrom($this->content['sender_email'], $this->content['sender_name']); 
            $this->addAddress($this->address, $this->content['recipient_name']);     //Add a recipient
            $this->addReplyTo($this->content['sender_email'], $this->content['sender_name']);


            // check if an attachment is present
            if ($this->content['attachment'] !== false) {
                $this->addAttachment($this->content['attachment']);
            }

            // the message 
            if ($this->content['ishtml']) {

                $this->isHTML(true);                                  //Set email format to HTML
                $this->Subject = $this->content['subject'];
                $this->Body    = $this->content['msg'];
                $this->AltBody = $this->content['altmsg'];    

            }else {
                $this->isHTML(false);
                $this->Subject = $this->content['subject'];
                $this->Body    = $this->content['msg'];
            }
            
            if ($this->send()) {
                return true;
            }
            else {
                return false;
            }

        } catch (\Exception $e) {

            throw new Exception("Message could not be sent. Mailer Error: {$this->ErrorInfo} {$e->getMessage()}");

        }
    }

    /**
     * this method sends a whatsapp msg
     * @return bool
     * 
     */

    private function sendWhatsapp () {
        echo 'whatsapp sent';
    }

    /**
     * this method sends an sms
     * @return bool
     * 
     */
    private function sendSms () {
        return true;
    }
}