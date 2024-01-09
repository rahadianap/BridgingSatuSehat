<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

use DB;

class CreateLocationController extends Controller
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

    public function createLocation(Request $request)
    {
        $body = [
            "resourceType" => "Location",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/1000001",
                    "value" => $request->ruang_id
                ]
            ],
            "status" => "active",
            "name" => $request->ruang_nama,
            "description" => "deskripsi",
            "mode" => "instance",
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => "no_telepon",
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" => "email",
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    "lokasi".', '."alamat"
                ],
                "city" => "kota",
                "postalCode" => "kodepos",
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" => "kode_propinsi"
                            ],
                            [
                                "url" => "city",
                                "valueCode" => "kode_kota"
                            ],
                            [
                                "url" => "district",
                                "valueCode" => "kode_kecamatan"
                            ],
                            [
                                "url" => "village",
                                "valueCode" => "kode_kelurahan"
                            ]
                        ]
                    ]
                ]
            ],
            "physicalType" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "ro",
                        "display" => "Room"
                    ]
                ]
            ],
            "position" => [
                "longitude" => -6.23115426275766,
                "latitude" => 106.83239885393944,
                "altitude" => 0
            ],
            "managingOrganization" => [
                "reference" => "Organization/10083042",
            ]
        ];
        
        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Location', $body);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Organization/'.$id);
        return json_decode($url->getBody(), true);
    }
}