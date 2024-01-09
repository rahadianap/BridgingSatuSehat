<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use DB;

class CreateOrganizationController extends Controller
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

    public function createOrganization(Request $request)
    {

        $body = [
            "resourceType" => "Organization",
            "active" => true,
            "identifier" => [
                [
                    "use" => "official",
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/organization/10083042",
                    "value" => $request->unit_id
                ]
            ],
            "type" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/organization-type",
                            "code" => "dept",
                            "display" => "Hospital Department"
                        ]
                    ]
                ]
            ],
            "name" => $request->unit_nama,
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
                [
                    "use" => "work",
                    "type" => "both",
                    "line" => [
                        "alamat"
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
                ]
            ],
            "partOf" => [
                "reference" => "Organization/10083042",
                "display" => "client_nama"
            ]
        ];
        
        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Organization', $body);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}