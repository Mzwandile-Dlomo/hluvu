<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class SafetyController {
    private $geminiApiKey;

    public function __construct() {
        // Get API key from TYPO3 extension configuration
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->geminiApiKey = $extensionConfiguration->get('safety_extension', 'geminiApiKey');
    }

    public function analyzeIncident() {
        try {
            // Get POST data
            $postData = json_decode(file_get_contents('php://input'), true);
            $keywords = $postData['keywords'];
            $timestamp = $postData['timestamp'];

            // Construct prompt
            $prompt = "As a safety incident analyzer, create a detailed report based on the following keywords detected: {$keywords}. 
            Time of incident: {$timestamp}
            
            Please structure the report as follows:
            1. Initial Assessment (urgency level)
            2. Detected Keywords Analysis
            3. Emotional State Analysis
            4. Potential Situation Assessment
            5. Risk Level
            6. Immediate Recommendations
            7. Follow-up Actions
            
            Format this as a clear, professional incident report.";

            // Call Gemini API (implementation needed based on your API setup)
            $response = $this->callGeminiApi($prompt);

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'analysis' => $response,
                'timestamp' => $timestamp,
                'detectedKeywords' => $keywords
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function chat() {
        try {
            // Get POST data
            $postData = json_decode(file_get_contents('php://input'), true);
            $message = $postData['message'];
            $isPostIncident = $postData['isPostIncident'];

            $prompt = $isPostIncident ? 
                "As a supportive AI assistant helping someone who has reported workplace abuse, 
                respond to their message: \"{$message}\"
                
                Context: The user has reported workplace abuse involving their boss with aggressive behavior.
                
                Guidelines:
                - Show empathy and understanding
                - Provide specific, actionable advice
                - Prioritize their safety and well-being
                - Reference relevant support services or legal options
                - Be clear and direct in recommendations
                
                Format the response in a clear, structured manner using markdown-style formatting with ** for emphasis." :
                "As an AI assistant, respond to: \"{$message}\"
                Format the response in a clear manner using markdown-style formatting with ** for emphasis.";

            // Call Gemini API
            $response = $this->callGeminiApi($prompt);

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'response' => $response,
                'isFormatted' => true
            ]);
        } catch (Exception $e) {
            error_log('Error in chat route: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to process message',
                'details' => $e->getMessage()
            ]);
        }
        exit;
    }

    private function callGeminiApi($prompt) {
        // Implementation needed for Gemini API call
        // This is a placeholder - you'll need to implement the actual API call
        // using PHP's curl or another HTTP client
        
        // Example structure:
        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->geminiApiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'contents' => [['text' => $prompt]]
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true)['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }
}