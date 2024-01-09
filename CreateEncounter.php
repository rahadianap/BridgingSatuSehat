<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class CreateEncounterController extends Controller
{
    public function getUrl()
    {
        $baseUrl = 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1';
        return $baseUrl;
    }
    
    public function createToken()
    {
        $response = Http::asForm()->post('https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials', [
            // 'client_id' => 'KQc4OiEXQ8XPP1UeMkxLnyZnAzooGLi6pyqR17ZPDG6hmlVt',
            // 'client_secret' => 'FHxvSs1IIGghiiuj641P9QCIAkQN0F4yVtD2Um89rqfMQpjm6GtZuQseAyVc7c51',
            'client_id' => 'uKCKCZuFkkudD1225L8RHFtlH5y6RHQYGDaRjxJJBnE14sk8',
            'client_secret' => 'LtQQVc7Cpp9iN1Rsz1cWz9YG60QV0VsaAALxOJFjHHjKfurflqhWHvdyq4bvc7XS'
        ]);
        $result = json_decode($response->getBody(), true);
        $value = $result['access_token'];
        return (string)$value;
    }

    public function sendData()
    {
        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Encounter', [
            "resourceType" => "Encounter",
            "identifier" => [
                [
                    // 10083042 adalah ID IHS Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/encounter/10083042", 
                    "value" => $request->reg_id // bebas. bisa menggunakan id pasien simrs, id ihs, atau nomor rm.
                    // "value" => "100000030009"
                ]
            ],
            "status" => "arrived", // Pasien melakukan kunjungan
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],
            "subject" => [
                // ID IHS Pasien berhubung tidak bisa mendaftar maka menggunakan data yang sudah ada
                "reference" => "Patient/100000030009", 
                "display" => $pasien->nama_pasien
                // "display" => "Bambang Santoso"
            ],
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender"
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        // ID IHS Dokter / Tenaga Medis N10000002 berhubung tidak bisa daftar maka menggunakan data yang sudah ada
                        "reference" => "Practitioner/N10000002",
                        "display" => $dokter->dokter_nama
                        // "display" => "Voigt"
                    ]
                ]
            ],
            "period" => [
                "start" => $dt
            ],
            "location" => [
                [
                    "location" => [
                        // ID Ruang / Location yang sudah didaftarkan
                        "reference" => "Location/" . $ruang->bridging_resource_id,
                        // "reference" => "Location/ef011065-38c9-46f8-9c35-d1fe68966a3e",
                        "display" => $ruang->bridging_resource_name
                    ]
                ]
            ],
            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => $dt
                    ]
                ]
            ],
            "serviceProvider" => [
                 // 10083042 adalah ID IHS Rumah Sakit yang didaftarkan di DTO.
                "reference" => "Organization/10083042"
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return json_encode($value);
    }
}