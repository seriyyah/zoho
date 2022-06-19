<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ZohoDealController extends Controller
{
    public function getDeals()
    {
        Session::forget('auth_error');

        try {
            $token = (string) Storage::disk('local')->get('zoho_crm_token.txt');
        } catch (Exception $e) {
            Session::forget('auth_success');
            return redirect()->route('home');
        }

        $url = "https://www.zohoapis.com/crm/v2/Deals?";

        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken " .$token,
            ]
        ]);

        $decode = json_decode($response->getBody()->getContents(),
            false, 512, JSON_THROW_ON_ERROR
        );
        if (isset($decode->code) && $decode->code === 'INVALID_TOKEN') {
            Session::forget('auth_success');
            Session::put('auth_error', 'Invalid token, try to reauth');
            return redirect()->route('/');
        }

        return view('deals', ['deals' => $decode['data']]);
    }

    public function addDeal(Request $request): ?RedirectResponse
    {
        Session::forget('success');
        Session::forget('error');

        try {
            $token = (string) Storage::disk('local')->get('zoho_crm_token.txt');
        } catch (Exception $e) {
            return redirect()->route('/');
        }

        $postData = $request->all();

        if (isset($token) && $token != '') {
            $serviceUrl = 'https://www.zohoapis.com/crm/v2/Deals';

            $recordData = new stdClass();
            $recordData->Deal_Name = $postData['deal_name'];
            $recordData->Account_Name = $postData['deal_name'];
            $recordData->Amount = $postData['amount'];
            $recordData->Stage = 'Qualification';
            $jsonData = json_encode($recordData, JSON_THROW_ON_ERROR);
            $data = "{\n    \"data\": [\n ".$jsonData."\n    ]\n}\n\n";

            $headers = array(
                'Authorization: Zoho-oauthtoken ' . $token,
                'Content-Type: application/json'
            );

            $dealResponse = $this->addData($serviceUrl, $data, $headers);

            $client = new Client();
            $response = $client->request('post', $serviceUrl, [
                'headers' => $headers,
                'body' => $data,
            ]);
        }

        $decode = $response->getBody()->getContents();

        if (isset($decode->code) && $decode->code === 'INVALID_TOKEN') {
            Session::forget('auth_success');
            Session::put('error', 'Invalid token, try to reauth');
            return redirect()->route('home');
        }

        $code = $decode->code;

        if (isset($code) && $code === 'SUCCESS') {
            Session::put('success', 'Deal created in ZohoCRM successfully!');
        } else {
            Session::put('error', 'Deal not create, please try again!');
        }
        return redirect()->route('deals');
    }
}
