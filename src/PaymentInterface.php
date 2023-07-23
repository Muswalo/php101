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
 class PaymentInterface {

    public function AirtelMoney () {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://openapiuat.airtel.africa/auth/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "client_id" => "6e4b237f-919a-4fe7-81fe-db5c277b39d0",
                "client_secret" => "****************************",
                "grant_type" => "client_credentials"
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: */*"
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            // Handle the error
        }

        curl_close($curl);

        if (!empty($response)) {
            // Handle the response
            var_dump($response);
        }
    }

}


