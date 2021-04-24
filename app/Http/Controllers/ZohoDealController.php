<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoDealController extends Controller
{
    public function getDeals()
    {
        \Session::forget('auth_error');
        
        try {
            $token = (string) \Storage::disk('local')->get('zoho_crm_token.txt');
        } catch (\Exception $e) {
            return redirect()->route('zoho');
        }
        
        $curl_pointer = curl_init();
        $curl_options = array();
        $url = "https://www.zohoapis.com/crm/v2/Deals?";
        $parameters = array();
        // $parameters["page"] = "1";
        // $parameters["per_page"] ="2";

        foreach ($parameters as $key => $value) {
            $url = $url.$key."=".$value."&";
        }

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "GET";
        $headersArray = array();
        $headersArray[] = "Authorization". ":" . "Zoho-oauthtoken " .$token;

        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;
        
        curl_setopt_array($curl_pointer, $curl_options);
        
        $result = curl_exec($curl_pointer);
        $responseInfo = curl_getinfo($curl_pointer);
        
        curl_close($curl_pointer);
        
        list ($headers, $content) = explode("\r\n\r\n", $result, 2);
        
        if(strpos($headers," 100 Continue")!==false) {
            list( $headers, $content) = explode( "\r\n\r\n", $content , 2);
        }
        
        $headerArray = (explode("\r\n", $headers, 50));
        $headerMap = array();
        
        foreach ($headerArray as $key) {
            if (strpos($key, ":") != false) {
                $firstHalf = substr($key, 0, strpos($key, ":"));
                $secondHalf = substr($key, strpos($key, ":") + 1);
                $headerMap[$firstHalf] = trim($secondHalf);
            }
        }
        
        $jsonResponse = json_decode($content, true);

        if (isset($jsonResponse['code']) && $jsonResponse['code'] == 'INVALID_TOKEN') {
            \Session::forget('auth_success');
            \Session::put('auth_error', 'Invalid token, try to reauth');
            return redirect()->route('/');
        }
        
        if ($jsonResponse == null && $responseInfo['http_code'] != 204) {
            list ($headers, $content) = explode("\r\n\r\n", $content, 2);
            $jsonResponse = json_decode($content, true);
        }

        $data = $jsonResponse['data'];

        return view('deals', ['deals' => $data]);
    }

    public function addDeal(Request $request)
    {
        \Session::forget('success');
        \Session::forget('error');
        
        try {
            $token = (string) \Storage::disk('local')->get('zoho_crm_token.txt');
        } catch (\Exception $e) {
            return redirect()->route('/');
        }
        
        $postData = $request->all();

        if (isset($token) && $token != '') {
            $dealName = $postData['deal_name'];
            $accountName = $postData['deal_name'];
            $amount = $postData['amount'];
            $stage = 'Qualification';

            $serviceUrl = 'https://www.zohoapis.com/crm/v2/Deals';

            $recordData = [];
            $recordData['Deal_Name'] = $dealName;
            $recordData['Account_Name'] = $accountName;
            $recordData['Amount'] = $amount;
            $recordData['Stage'] = $stage;

            $jsonData = json_encode($recordData);
            $data = "{\n    \"data\": [\n ".$jsonData."\n    ]\n}\n\n";
      
            $curl = curl_init($serviceUrl);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Zoho-oauthtoken ' . $token,
                'Content-Type: application/json'
            ));
      
            $cResponse = curl_exec($curl);
            curl_close($curl);

            $dealResponse = json_decode($cResponse);

        }

        if (isset($dealResponse->code) && $dealResponse->code == 'INVALID_TOKEN') {
            \Session::forget('auth_success');
            \Session::put('error', 'Invalid token, try to reauth');
            return redirect()->route('/');
        }
    
        $code = $dealResponse->data[0]->code;
    
        if (isset($code) && $code == 'SUCCESS') {
            \Session::put('success', 'Deal created in ZohoCRM successfully!');
            return redirect()->route('deals');
        } else {
            \Session::put('error', 'Deal not create, please try again!');
            return redirect()->route('deals');
        }
    }
}
