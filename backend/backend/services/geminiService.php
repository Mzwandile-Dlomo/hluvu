<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocationService {
    private $locations;

    public function __construct() {
        $this->locations = [
            [
                'street' => 'V&A Waterfront',
                'description' => 'Popular waterfront with shops and restaurants.',
                'riskTimes' => 'Best visited during daylight hours',
                'position' => [
                    'lat' => -33.9033,
                    'lng' => 18.4197
                ]
            ],
            [
                'street' => 'Long Street', 
                'description' => 'Historic street with cafes and boutiques.',
                'riskTimes' => 'Busiest during business hours',
                'position' => [
                    'lat' => -33.9201,
                    'lng' => 18.4183
                ]
            ],
            [
                'street' => 'Camps Bay Strip',
                'description' => 'Scenic beachfront with dining options.',
                'riskTimes' => 'Most active from morning to sunset',
                'position' => [
                    'lat' => -33.9507,
                    'lng' => 18.3783
                ]
            ]
        ];
    }

    public function analyzeDangerZones($startLocation, $endLocation) {
        // Simply return the hardcoded locations without any API calls
        return $this->locations;
    }
}