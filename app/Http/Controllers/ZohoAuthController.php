<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoAuthController extends ZohoController
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

        $tokenResult = $this->prepareData($tokenUrl);

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
