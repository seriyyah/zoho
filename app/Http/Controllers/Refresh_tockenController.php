<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class Refresh_tockenController extends Controller
{
    public function generate_refresh_tocken(){
        $client_id ='1000.P8RO7QKRTMURDW8Z4B9COXOAHEP69I';
        $client_secret = '91f61cac8bfa95ae375289e405dd9cbd1b3bcee79c';
        $code = '1000.2c4cb06f637e06316abb922d16987a49.1b4961dd42feff578dca2cfb3ece1ca1';
        $base_acc_url = 'https://accounts.zoho.com';
        $service_url = 'https://creator.zoho.com';

        $refresh_token = '1000.3dbaba43a90ec019809f21a0ad668466.97992c604d87bd93d9768f45e00a5c62';

        $token_url = $base_acc_url . '/oauth/v2/token?grant_type=authorization_code&client_id='. $client_id . '&client_secret='. $client_secret . '&redirect_uri=http://127.0.0.1:8000/&code=' . $code;



        function generate_access_token($url){

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, 1);
          $result = curl_exec($ch);
          curl_close($ch);
          return json_decode($result)->access_token;
          print_r($result);
        }


        $access_token_url = $base_acc_url .  '/oauth/v2/token?refresh_token='.$refresh_token.'&client_id='.$client_id.'&client_secret='.$client_secret .'&grant_type=refresh_token';
        generate_access_token($access_token_url);

        $access_token = generate_access_token($access_token_url);
        var_dump($access_token);




        function create_record($access_token){

            $deal = $_POST['Deal_Name'];
            $account = $_POST['Account_Name'];
            $amount = $_POST['Amount'];


          $service_url = 'https://www.zohoapis.com/crm/v2/Deals';

          //Authorization: Zoho-oauthtoken access_token
          $recordArray=[];
          $recordArray['Deal_Name']=$deal ;
          $recordArray['Account_Name']=$account;
          $recordArray['Amount']=$amount;
          // $recordArray['Closing_Date']='09.17.2021';
          $recordArray['Stage']='Qualification';

          $jsonData= json_encode($recordArray);
          // $jsonData = '{

          //             "Lead_Name":"test prod",
          //             "Company":"new",

          //       }';
          $data="{\n    \"data\": [\n ".$jsonData."\n    ]\n}\n\n";
                    $header = array(
                      'Authorization: Zoho-oauthtoken ' . $access_token,
                      'Content-Type: application/json'
                    );

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $service_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
          $result = curl_exec($ch);
          curl_close($ch);
          var_dump($result);
        }
        create_record($access_token);
    }



}

