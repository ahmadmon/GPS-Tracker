<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Device Communication Protocol Ports
    |--------------------------------------------------------------------------
    |
    | This configuration file defines the port numbers assigned to different
    | GPS tracking protocols. Each protocol requires a specific port to
    | establish communication with the server.
    |
    | ⚠️ Important:
    | - Ensure that the assigned ports are open and not blocked by a firewall.
    | - You may need to configure your server's firewall settings to allow
    |   inbound connections on these ports.
    |
    */

    'protocols' => [

        /*
        |--------------------------------------------------------------------------
        | GT06 Protocol
        |--------------------------------------------------------------------------
        |
        | GT06 is a popular GPS tracking protocol used by various devices.
        | It communicates using TCP and typically operates on port 5024.
        |
        | Example Usage:
        |  - Ensure that port 5024 is open and listening.
        |  - Configure your GPS device to send data to your server's IP on port 5024.
        |
        */
        'GT06' => 5024,

        /*
        |--------------------------------------------------------------------------
        | H02 Protocol
        |--------------------------------------------------------------------------
        |
        | H02 is a GPS tracking protocol used in a variety of tracking devices.
        | It uses TCP-based communication and typically runs on port 5023.
        |
        | Example Usage:
        |  - Ensure that port 5023 is open and listening.
        |  - Configure your GPS device to send data to your server's IP on port 5023.
        |
        */
        'H02' => 5023,

        /*
        |--------------------------------------------------------------------------
        | TK103 Protocol
        |--------------------------------------------------------------------------
        |
        | The TK103 protocol is commonly used for vehicle tracking devices.
        | It also uses TCP-based communication and usually runs on port 5001.
        |
        */
        'TK103' => 5001,

        /*
        |--------------------------------------------------------------------------
        | GPS103 Protocol
        |--------------------------------------------------------------------------
        |
        | GPS103 is another variation of the TK103 protocol, often used in
        | personal tracking devices and fleet management.
        |
        */
        'GPS103' => 5002,

    ]
];

