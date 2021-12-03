<?php

return array(
    'author'            => 'CSCI-331',
    'author_url'        => 'https://example.com',
    'name'              => 'ArtHub',
    'description'       => 'ArtHub-Addon description...',
    'version'           => '1.0.0',
    'namespace'         => 'User\Addons\ArtHub',
    'settings_exist'    => true,
    'models' => array(
        'Message' => 'Model\Message',
        'MessageConvesation' => 'Model\MessageConvesation',
    ),
    'models.dependencies' => array(
        'Message' => array(
            'ee:Member'
        ),
        'MessageConvesation' => array(
            'ee:Member'
        )
    ),
);

// END OF
