<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class MapController {
    public function getApiKey() {
        // Get API key from TYPO3 extension configuration
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $apiKey = $extensionConfiguration->get('maps_extension', 'googleMapsApiKey');
        
        // Log the API key request
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Maps']['requestApiKey'] = $apiKey;
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['key' => $apiKey]);
        exit;
    }
}