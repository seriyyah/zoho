<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZohoLeadsCreateRequest;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ZohoLeadsController extends Controller
{
    public function index()
    {
        return view('leads');
    }

    public function store(ZohoLeadsCreateRequest $request)
    {
        Session::forget('success');
        Session::forget('error');

        try {
            $token = (string) Storage::disk('local')->get('zoho_crm_token.txt');
        } catch (Exception $e) {
            return redirect()->route('home');
        }

        if (isset($token)) {
            $input = $request->validated();
            $url = config('zoho.api_base_url').config('zoho.api_leads_url');
            $data = [
                'data' => [
                    'first_name' => $input['full_name'],
                    'phone' => $input['phone_number'],
                    'email' => $input['email'],
                    'lead_status' => 'Step 1',
                ],
            ];
            $headers = [
                'headers' => [
                    'Authorization: Zoho-oauthtoken ' . $token,
                    'Content-Type: application/json',
                ],
            ];
            $client = new Client();

            $response = $client->request('post', $url, [$headers, $data]);
        }

        if (isset($response) && $response->getStatusCode() === 200) {
            Session::put('success', 'lead created in ZohoCRM successfully!');
        } else {
            Session::put('error', 'lead not create, please try again!');
        }
        return redirect()->route('edit-lead');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('edit-leads');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}