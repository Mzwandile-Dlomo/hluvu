<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class AIService {
    private $geminiApiKey;
    
    public function __construct() {
        // Get API key from TYPO3 extension configuration
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->geminiApiKey = $extensionConfiguration->get('ai_extension', 'geminiApiKey');
    }

    public function analyzeContent($text, $context) {
        try {
            $prompt = $this->buildPrompt($context, $text);
            
            // Configure cURL request to Gemini API
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->geminiApiKey
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ])
            ]);

            $response = curl_exec($curl);
            $responseData = json_decode($response, true);
            
            if (curl_errno($curl)) {
                throw new \Exception(curl_error($curl));
            }
            curl_close($curl);

            return $this->formatResponse($context, $responseData['candidates'][0]['content']['parts'][0]['text']);
        } catch (\Exception $error) {
            throw new \Exception('AI analysis failed: ' . $error->getMessage());
        }
    }

    private function formatResponse($context, $text) {
        $intro = '<div class="ai-response">
            <div class="response-header">
                <div class="bot-avatar">
                    <i data-lucide="bot" size="24"></i>
                </div>
                <span class="bot-name">Venture-Bot</span>
            </div>
            <div class="response-content">';

        // Format the greeting and assessment
        $formattedText = preg_replace('/(EXCELLENT|GOOD|FAIR)/', '<span class="assessment-$1">$1</span>', $text);

        // Format section headers with icons
        $sectionIcons = [
            'Market Need' => 'target',
            'Innovative Solution' => 'lightbulb', 
            'Business Model' => 'briefcase',
            'Implementation Plan' => 'git-branch',
            'Recommendations' => 'check-circle'
        ];

        foreach ($sectionIcons as $section => $icon) {
            $formattedText = str_replace(
                $section,
                '<h3 class="section-header">
                    <i data-lucide="' . $icon . '" size="20"></i>
                    ' . $section . '
                </h3>',
                $formattedText
            );
        }

        // Format bullet points
        $formattedText = preg_replace('/•(.*)/', '<div class="bullet-point"><i data-lucide="check" size="16"></i>$1</div>', $formattedText);

        return $intro . $formattedText . '</div></div>';
    }

    private function buildPrompt($keywords, $timestamp, $location) {
        return "Generate a detailed incident report based on the following:
        Time: {$timestamp}
        Location: {$location}
        Keywords Detected: {$keywords}

        Format the report with these sections:
        **1. Initial Assessment**
        - Urgency Level
        - Risk Assessment

        **2. Detected Keywords Analysis**
        - Analyze each keyword and its implications

        **3. Emotional State Analysis**
        - Analyze emotional indicators
        - Assess psychological state

        **4. Recommendations**
        - Immediate actions needed
        - Support resources
        - Safety measures";
    }
}