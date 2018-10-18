<?php

// 
// The TZ environment variable *has* to be set because its inherited by sub
// processes (like at). It also affects the formatting of timestamps. Either 
// set it here, or if safe_mode is enabled, in the apache config.
// 
if (!getenv("TZ")) {
        putenv("TZ=Europe/Stockholm");
}
if (!getenv("TZ")) {
        die("The environment variable TZ is not set!");
}
if (!ini_get("date.timezone")) {
        ini_set("date.timezone", getenv("TZ"));
}

//
// The application configuration (please modify to suite your preferences).
//
return [
        // 
        // The data directory where i.e. job queues are stored.
        // 
        'data'    => [
                'path' => realpath(__DIR__ . "/../data"),
                'mode' => 0750
        ],
        // 
        // The contact information.
        // 
        'contact' => [
                'name' => 'System Manager',
                'mail' => 'batchelor@localhost',
                'page' => 'http://batchelor.example.com/contact.html'
        ],
//        // 
//        // The cache options (see docs/README-CACHE).
//        // 
//        'cache'   => [
//                'system'   => 'detect', // Generic system cache.
//                'queue'    => 'detect', // Jobs queue cache.
//                'schedule' => 'detect', // Scheduled task cache.
//                'mapper'   => 'detect'  // The hostid to queue map cache.
//        ],
//        // 
//        // The logger options (relative pathes are created inside the data directory).
//        // 
//        'logger'  => [
//                'request' => [
//                        'type'    => 'request',
//                        'options' => [
//                                'path'  => 'logs',
//                                'ident' => 'request'
//                        ]
//                ],
//                'system'  => [
//                        'type'    => 'file',
//                        'options' => [
//                                'filename' => 'logs/system.log'
//                        ]
//                ],
//                'auth'    => [
//                        'type'    => 'syslog',
//                        'options' => [
//                                'ident'    => 'batchelor',
//                                'facility' => LOG_AUTH
//                        ]
//                ]
//        ],
//        // 
//        // The job queue options (see docs/README-QUEUE).
//        // 
//        'queue'   => [
//                'local' => [
//                        'type'   => 'system',
//                        'weight' => 50
//                ],
//                'host1' => [
//                        'type'   => 'remote',
//                        'url'    => 'http://rosalie.bmc.uu.se/batchelor2',
//                        'weight' => 25
//                ],
//                'host2' => [
//                        'type'   => 'remote',
//                        'url'    => 'http://rosalie.bmc.uu.se/batchelor2',
//                        'weight' => 40
//                ],
//                'host3' => [
//                        'type'   => 'remote',
//                        'url'    => 'http://rosalie.bmc.uu.se/batchelor2',
//                        'weight' => 30
//                ]
//        ]
        'queue'   => true   // Use local queue
];
