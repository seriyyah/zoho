<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZohoLeadsCreateRequest;
use App\Http\Requests\ZohoLeadsUpdateRequest;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use stdClass;

class ZohoLeadsController extends Controller
{
    private const FIRST_STEP = 'Step 1';
    private const SECOND_STEP = 'Step 2';

    public function index()
    {
        return view('leads');
    }

    public function store(ZohoLeadsCreateRequest $request): RedirectResponse
    {
        Session::forget('success');
        Session::forget('error');

        try {
            $token = (string) Storage::disk('local')->get('zoho_crm_token.txt');

            $input = $request->validated();
            $url = config('zoho.api_base_url').config('zoho.api_leads_url');

            $recordData = new stdClass();
            $recordData->Company = str::random(); // required field
            $recordData->Last_Name = $input['full_name'];
            $recordData->Phone = $input['phone_number'];
            $recordData->Email = $input['email'];
            $recordData->Lead_Status = self::FIRST_STEP;
            $jsonData = json_encode($recordData, JSON_THROW_ON_ERROR);
            $data = "{\n    \"data\": [\n ".$jsonData."\n    ]\n}\n\n";

            $client = new Client();

            $response = $client->request('post', $url, [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $token,
                ],
                'body' => $data,
            ]);

            $decode = json_decode($response->getBody()->getContents());

            Session::put('Lead_ID', $decode->data[0]->details->id);
        } catch (Exception $e) {
            return redirect()->route('home');
        }

        if (isset($decode) && $decode->data[0]->code === 'SUCCESS') {
            Session::put('success', 'lead created in ZohoCRM successfully!');
        } else {
            Session::put('error', 'lead not create, please try again!');
        }
        return redirect()->route('show-lead');
    }

    public function show()
    {
        return view('edit-lead');
    }

    public function update(ZohoLeadsUpdateRequest $request): RedirectResponse
    {
        Session::forget('success');
        Session::forget('error');

        try {
            $token = (string) Storage::disk('local')->get('zoho_crm_token.txt');
            $leadId = Session::get('Lead_ID');
            $input = $request->validated();
            $url = config('zoho.api_base_url').config('zoho.api_leads_url');

            $recordData = new stdClass();
            $recordData->Kids_Name = $input['kid_name'];
            $recordData->Kids_Grade = $input['kid_grade'];
            $recordData->Lead_Status = self::SECOND_STEP;
            $jsonData = json_encode($recordData, JSON_THROW_ON_ERROR);
            $data = "{\n    \"data\": [\n ".$jsonData."\n    ]\n}\n\n";

            $client = new Client();

            $response = $client->request('put', $url.'/'.$leadId, [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $token,
                ],
                'body' => $data,
            ]);

            $decode = json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            return redirect()->route('home');
        }

        if (isset($decode) && $decode->data[0]->code === 'SUCCESS') {
            Session::put('success', 'lead created in ZohoCRM successfully!');
        } else {
            Session::put('error', 'lead not updated, please try again!');
        }
        session::forget('Lead_ID');

        return redirect()->route('leads')->with('success', 'lead updated in ZohoCRM successfully!');
    }

    public function destroy(): RedirectResponse
    {
        Session::forget('success');
        Session::forget('error');

        try {
            $token = (string) Storage::disk('local')->get('zoho_crm_token.txt');
            $leadId = Session::get('Lead_ID');
            $url = config('zoho.api_base_url').config('zoho.api_leads_url');
            $client = new Client();
            $response = $client->request('delete', $url.'/'.$leadId, [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $token,
                ]
            ]);

            $decode = json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            return redirect()->route('home');
        }

        if (isset($decode) && $decode->data[0]->code == 'SUCCESS') {
            Session::put('success', 'lead deleted in ZohoCRM successfully!');
        } else {
            Session::put('error', 'lead not deleted, please try again!');
        }
        session::forget('Lead_ID');

        return redirect()->route('leads')->with('success', 'lead deleted in ZohoCRM successfully!');
    }
}
