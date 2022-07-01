<?php

namespace App\Http\Controllers;

class StatusController
{
    public function index()
    {
        return response()->json([
            'monitors (last 24h)' => [
                'status'         => 'All systems operational',
                'services'       => [
                    'account'      => [
                        'status' => 'Up',
                        'uptime' => '100.00%',
                    ],
                    'game_account' => [
                        'status' => 'Up',
                        'uptime' => '100.00%',
                    ],
                ],
                'Overall Status' => 'Up',
                'Overall Uptime' => '100.00%',
            ],
        ]);
    }
}
