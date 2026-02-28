<?php

$helpCenters = HelpModel::getActiveHelpCenters();

if (empty($helpCenters)) {
    $helpCenters = [
        [
            'helpCenterId' => 1,
            'name' => 'Addiction Counseling',
            'organization' => '',
            'type' => 'appointment',
            'category' => 'counseling',
            'phoneNumber' => '',
            'email' => '',
            'website' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'zipCode' => '',
            'availability' => 'Available Now',
            'description' => 'Professional one-on-one counseling sessions with certified addiction specialists.',
            'specialties' => 'Addiction recovery, trauma-informed care',
        ],
        [
            'helpCenterId' => 2,
            'name' => 'Peer Support Groups',
            'organization' => '',
            'type' => 'chat',
            'category' => 'community',
            'phoneNumber' => '',
            'email' => '',
            'website' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'zipCode' => '',
            'availability' => 'Daily 6-8 PM',
            'description' => 'Join supportive group discussions with others on similar recovery journeys.',
            'specialties' => 'Group support, accountability',
        ],
        [
            'helpCenterId' => 3,
            'name' => 'Family Counseling',
            'organization' => '',
            'type' => 'appointment',
            'category' => 'counseling',
            'phoneNumber' => '',
            'email' => '',
            'website' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'zipCode' => '',
            'availability' => 'Weekends',
            'description' => 'Family therapy sessions to help rebuild and strengthen relationships.',
            'specialties' => 'Family systems, communication skills',
        ],
    ];
}

$pageTitle = 'Help Center';
$pageStyle = ['user/dashboard', 'user/helpCenter'];

