<?php

return [
    /*
     * Whatsmeow API Endpoint
     */
    'endpoint' => env('WHATSMEOW_ENDPOINT', 'http://localhost/api/v2'),

    /*
     * Your Whatsmeow API Token
     */
    'token' => env('WHATSMEOW_TOKEN', ''),

    /*
     * Notifiable's WhatsApp number
     * Fill with your user's whatsapp column
     */
    'whatsapp_number_field' => env('WHATSAPP_NUMBER_FIELD', 'personal_information'),

    /*
     * Notifiable's WhatsApp number
     * Only fill if whatsapp_number_field is JSON and you want to select a key from it
     */
    'whatsapp_number_json_field' => env('WHATSAPP_NUMBER_JSON_FIELD', 'whatsapp'),

    /*
     * If application is Local AND run in debug mode, send to this number instead
     */
    'debug_number' => env('DEBUG_WHATSAPP_NUMBER', null),

    /*
     * Debug mode
     * If true, will send debug output of the http request
     */
    'debug_mode' => env('WHATSMEOW_DEBUG_MODE', false),
];
