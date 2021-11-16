<?php 

return [
    'name'        => 'XML To Email',
    'description' => 'Enables XML feed in mail',
    'version'     => '1.1',
    'author'      => 'Fastware B.V.',
    'services' => [
        'events' => [
            'mautic.plugin.xmltoemail.subscriber' => [
                'class'     => \MauticPlugin\MauticXmlToEmailBundle\EventListener\EmailSubscriber::class,
            ],
        ],
    ],
];