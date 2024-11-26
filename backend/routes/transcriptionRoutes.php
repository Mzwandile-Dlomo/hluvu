<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class TranscriptionController {
    private $openaiApiKey;

    public function __construct() {
        // Get API key from TYPO3 extension configuration
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->openaiApiKey = $extensionConfiguration->get('transcription_extension', 'openaiApiKey');
    }

    public function transcribe() {
        try {
            // Handle file upload
            $uploadedFile = $_FILES['audio'];
            $uploadDir = 'uploads/';
            $targetPath = $uploadDir . basename($uploadedFile['name']);
            
            if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                // Simulate processing delay
                sleep(2);

                // Demo response
                $demoResponse = [
                    'transcription' => "Boss was very aggressive today, I'm scared and crying. Please help.",
                    'report' => "**Incident Report**\n" .
                               "**1. Initial Assessment**\n" .
                               "- Urgency Level: High\n" .
                               "- Risk Assessment: Immediate attention required\n\n" .
                               "**2. Detected Keywords Analysis**\n" .
                               "- **Boss:** Indicates a hierarchical relationship and potential power imbalance\n" .
                               "- **Aggressive:** Implies hostile or threatening behavior\n" .
                               "- **Scared:** Suggests fear or anxiety\n" .
                               "- **Crying:** Indicates emotional distress\n\n" .
                               "**3. Emotional State Analysis**\n" .
                               "The presence of keywords \"scared\" and \"crying\" suggests significant emotional distress. " .
                               "The combination of these keywords with \"aggressive\" behavior indicates a potentially unsafe situation.\n\n" .
                               "**4. Recommendations**\n" .
                               "- Document all incidents\n" .
                               "- Contact HR department if available\n" .
                               "- Consider legal counsel\n" .
                               "- Establish safety plan\n" .
                               "- Seek support from trusted colleagues"
                ];

                // Return JSON response
                header('Content-Type: application/json');
                echo json_encode($demoResponse);
            } else {
                throw new Exception('Failed to upload file');
            }
        } catch (Exception $e) {
            GeneralUtility::devLog('Transcription error:', 'TranscriptionController', 3, ['error' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}