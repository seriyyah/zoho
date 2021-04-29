<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoDealController extends ZohoController
{
    public function getDeals()
    {
        \Session::forget('auth_error');
        
        try {
            $token = (string) \Storage::disk('local')->get('zoho_crm_token.txt');
        } catch (\Exception $e) {
            \Session::forget('auth_success');
            return redirect()->route('/');
        }
        
        $url = "https://www.zohoapis.com/crm/v2/Deals?";
        $parameters = array();
        $parameters['per_page'] = 5;
        $headers = array();
        $headers[] = "Authorization". ":" . "Zoho-oauthtoken " .$token;
        
        $result = $this->getData($url, $parameters, $headers);

        if (isset($result['code']) && $result['code'] == 'INVALID_TOKEN') {
            \Session::forget('auth_success');
            \Session::put('auth_error', 'Invalid token, try to reauth');
            return redirect()->route('/');
        }
        
        return view('deals', ['deals' => $result['data']]);
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
      
            $headers = array(
                'Authorization: Zoho-oauthtoken ' . $token,
                'Content-Type: application/json'
            );
      
            $dealResponse = $this->addData($serviceUrl, $data, $headers);
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
