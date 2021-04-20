<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoCrmController extends Controller
{
    public function oauth(Request $request)
    {
        $input = $request->all();
        
        \Session::put('client_id', $input['client_id']);
        \Session::put('client_secret', $input['client_secret']);
        \Session::put('redirect_uri', $input['redirect_uri']);

        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
            [
                'client_id' => $input['client_id'],
                'redirect_uri' => $input['redirect_uri'],
                'scope' => 'ZohoCRM.modules.all',
                'response_type' => 'code',
            ]);

        return redirect($redirectTo);
    }

    public function generateToken(Request $request)
    {
        if (\Session::has('client_id') && \Session::has('client_secret') && \Session::has('redirect_uri')) {
            $client_id = \Session::get('client_id');
            $client_secret = \Session::get('client_secret');
            $redirect_uri = \Session::get('redirect_uri');
        }

        $input = $request->all();

        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code='.$input['code'].'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.$redirect_uri.'&grant_type=authorization_code';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
        curl_setopt($curl, CURLOPT_POST, TRUE); 
        curl_setopt($curl, CURLOPT_URL, $tokenUrl);     
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
        $result = curl_exec($curl);
        curl_close($curl);
        $tokenResult = json_decode($result);
    
        $this->writeTokenToFile($tokenResult->access_token);
        
        return redirect()->route('deals');
    }

    public function getDeals()
    {
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
            \Session::put('error', 'Invalid token, try to reauth');
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

    private function writeTokenToFile(string $token)
    {
        try {
            \Storage::put('zoho_crm_token.txt', $token);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
