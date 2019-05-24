<?php

namespace packages\actionMofficernd\Models;

trait OfficeRNDApiHelpers
{


    /* simple encrypter and decrypter that can be used
    for data exchange between systems */
    public function encryptString($string)
    {
        $enc_key = openssl_digest(self::$salt, 'SHA256', TRUE);
        $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CTR'));
        $encrypted = openssl_encrypt($string, 'AES-128-CTR', $enc_key, null, $enc_iv) . "::" . bin2hex($enc_iv);
        return $encrypted;
    }

    public function decryptString($string)
    {
        if (preg_match("/^(.*)::(.*)$/", $string, $regs)) {
            // decrypt encrypted string
            list(, $crypted_token, $enc_iv) = $regs;
            $enc_method = 'AES-128-CTR';
            $enc_key = openssl_digest(self::$salt, 'SHA256', TRUE);
            $decrypted = openssl_decrypt($crypted_token, $enc_method, $enc_key, 0, hex2bin($enc_iv));
            return $decrypted;
        }

        return false;
    }


    /**
     *
     * @param $url
     * @param array $data
     * @param string $method
     * @param mixed $authentication
     * @return bool|mixed
     */
    function curlJsonCall($url, $data = array(), $method = 'GET', $authentication = false)
    {
        $call = $this->curlCall($url, $data, $method, $authentication);

        if (is_array($call)) {
            return $call;
        }

        sleep(1);

        /* simple retry */
        $call = $this->curlCall($url, $data, $method, $authentication);

        if (is_array($call)) {
            return $call;
        }

        return false;

    }

    private function curlCall($url, $data = array(), $method = 'GET', $authentication = false)
    {
        $ch = curl_init(self::$api_endpoint_base . $url);
        $header = array();

        if ($authentication == 'token') {
            array_push($header, "Authorization: Bearer " . self::$token);
        }

        array_push($header, "Content-Type: application/json");
        array_push($header, "Accept: application/json");

        $original_data = $data;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($ch);

        if (curl_error($ch)) {
            $this->errors[] = curl_error($ch);
            return false;
        }

        curl_close($ch);

        $test = @json_decode($data, true);

        $this->log[] = 'Url:' . self::$api_endpoint_base . $url;
        $this->log[] = (string)'Headers:' . json_encode($header) . chr(10);
        $this->log[] = (string)'Post:' . json_encode($original_data) . chr(10);
        $this->log[] = (string)'Response:' . $data . chr(10);
        $this->log[] = (string)'-----------------------------------' . chr(10);


        if (is_array($test)) {
            return $test;
        }

        return false;

    }


}
