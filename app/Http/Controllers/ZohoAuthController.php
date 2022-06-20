<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class ZohoAuthController extends Controller
{
    public function index()
    {
        return view('zoho');
    }

    public function authZoho()
    {
        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
                [
                    'client_id' => config('zoho.client_id'),
                    'redirect_uri' => config('zoho.redirect_uri'),
                    'scope' => config('zoho.oauth_scope'),
                    'response_type' => config('zoho.response_type'),
                ]);

        return redirect($redirectTo);
    }

    public function generateToken(Request $request): RedirectResponse
    {
        $input = $request->all();
        $client = new Client();

        try {
            $response = $client->request('POST', 'https://accounts.zoho.eu/oauth/v2/token?', [
                'form_params' => [
                    'code' => $input['code'],
                    'client_id' => config('zoho.client_id'),
                    'client_secret' => config('zoho.client_secret'),
                    'redirect_uri' => config('zoho.redirect_uri'),
                    'grant_type' => config('zoho.grant_type'),
                ]
            ]);

            $decode = json_decode($response->getBody()->getContents(),
                false, 512, JSON_THROW_ON_ERROR
            );
            $this->writeTokenToFile($decode->access_token);

        } catch (GuzzleException $e) {
            $e->getMessage();
        }

        return redirect()->route('home');
    }

    private function writeTokenToFile(string $token)
    {
        try {
            Storage::put('zoho_crm_token.txt', $token);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
