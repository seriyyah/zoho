<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoAuthController extends Controller
{
    public function view()
    {
        return view('zoho');
    }
    
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
        \Session::forget('auth_error');
        
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

        if (isset($tokenResult->error)) {
            \Session::put('auth_error', 'Invalid client secret');
        } else {
            \Session::put('auth_success', 'Auth is success');
        }

        $this->writeTokenToFile($tokenResult->access_token);
        
        return redirect()->route('/');
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
