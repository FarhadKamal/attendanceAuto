<?php

namespace App\Services;

use GuzzleHttp\Client;

class ZktService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'http://192.168.1.153';
    }


    public function deleteAttendanceData($startDate, $endDate)
    {
        try {
            // Send DELETE request to ZKTeco API
            $response = $this->client->request('DELETE', $this->baseUrl . '/api/attendance', [
                'query' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ]);


            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            // Handle any errors that may occur during the request
            return ['error' => $e->getMessage()];
        }
    }
}
