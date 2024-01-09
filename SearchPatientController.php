<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class SearchPatientController extends Controller
{
    public function getUrl()
    {
        $baseUrl = 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1';
        return $baseUrl;
    }

    public function createToken()
    {
        $response = Http::asForm()->post('https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials', [
            'client_id' => 'KQc4OiEXQ8XPP1UeMkxLnyZnAzooGLi6pyqR17ZPDG6hmlVt',
            'client_secret' => 'FHxvSs1IIGghiiuj641P9QCIAkQN0F4yVtD2Um89rqfMQpjm6GtZuQseAyVc7c51',
        ]);
        $result = json_decode($response->getBody(), true);
        $value = $result['access_token'];
        return (string)$value;
    }

    public function searchNIK($nik)
    {   
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Patient?identifier=https://fhir.kemkes.go.id/id/nik|'.$nik);
        return json_decode($url->getBody(), true);
    }

    public function searchName(Request $request)
    {   
        $nama = $request->get('name');
        $gender = $request->get('gender');
        $year = $request->get('birthdate');

        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Practitioner?',
        [
            'name' => $nama, 
            'gender' => $gender, 
            'birthdate' => $year
        ]);
        return json_decode($url->getBody(), true);
    }

    public function searchID($id)
    {   
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Patient/'.$id);
        return json_decode($url->getBody(), true);
    }
}