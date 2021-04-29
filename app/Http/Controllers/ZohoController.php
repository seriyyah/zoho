<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoController extends Controller
{
    public function getData($url, $parameters = array(), $headers = array()) {
        $curl_pointer = curl_init();
        $curl_options = array();

        foreach ($parameters as $key => $value) {
            $url = $url.$key."=".$value."&";
        }

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "GET";
        $curl_options[CURLOPT_HTTPHEADER] = $headers;
        
        curl_setopt_array($curl_pointer, $curl_options);
        
        $result = curl_exec($curl_pointer);
        $responseInfo = curl_getinfo($curl_pointer);
        
        curl_close($curl_pointer);

        list ($headers, $content) = explode("\r\n\r\n", $result, 2);
        
        if(strpos($headers," 100 Continue") !== false) {
            list($headers, $content) = explode("\r\n\r\n", $content , 2);
        }

        $result = json_decode($content, true);

        return $result;
    }

    public function prepareData($url, $params = array(), $headers = array())
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_VERBOSE, 0);     
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);   
        curl_setopt($ch, CURLOPT_POST, TRUE); 
        curl_setopt($ch, CURLOPT_URL, $url);     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        curl_error ($ch);
        curl_close($ch);
        return json_decode($result);
    }
}
