<?php

return [
    // One-time fee every organization pays at registration (BDT)
    'org_fee' => 20000,
    // bKash number shown on the organization registration form (change in .env)
    'bkash_number' => env('BKASH_NUMBER', '01700000000'),
    'bkash_type' => env('BKASH_TYPE', 'Personal'),
    'min_withdrawal' => 100,
    'reputation' => ['critical' => 50, 'high' => 30, 'medium' => 15, 'low' => 5, 'informative' => 1],
];
