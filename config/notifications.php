<?php
// Notification Configuration
return [
    'whatsapp' => [
        'business_phone' => '5541996713782', // Replace with your WhatsApp Business number
        'enabled' => true
    ],
    
    'email' => [
        'admin_email' => 'gutiajs@gmail.com', // Replace with your business email
        'from_email' => 'noreply@samptech.com',
        'enabled' => true
    ],
    
    'smtp' => [
        'host' => 'smtp.gmail.com', // For Gmail SMTP
        'port' => 587,
        'username' => '', // Your email
        'password' => '', // Your app password
        'encryption' => 'tls'
    ]
];
?>