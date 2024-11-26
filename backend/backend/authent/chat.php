<?php
namespace TYPO3\CMS\Backend\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Http\JsonResponse;

class ChatController {
    protected $mediaRecorder;
    protected $audioChunks = [];
    protected $incidentReportGenerated = false;
    protected $resourceFactory;

    public function __construct() {
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }

    public function createMessage($content, $isUser = false) {
        $message = '<div class="message ' . ($isUser ? 'user-message' : 'bot-message') . '">';
        
        if (is_string($content) && (strpos($content, '<div') !== false || strpos($content, '**') !== false)) {
            // Handle markdown-style formatting
            $formattedContent = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
            $formattedContent = nl2br($formattedContent);
            $message .= $formattedContent;
        } else {
            $message .= htmlspecialchars($content);
        }
        
        $message .= '</div>';
        return $message;
    }

    public function createFilePreview($file) {
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileSize = round($file['size'] / 1024) . ' kB';
        
        return '
            <div class="file-preview">
                <div class="file-icon">
                    <i data-lucide="' . ($fileExt === 'pdf' ? 'file-text' : 'image') . '" size="24"></i>
                </div>
                <div class="file-info">
                    <span class="file-name">' . htmlspecialchars($file['name']) . '</span>
                    <span class="file-details">' . strtoupper($fileExt) . ' • ' . $fileSize . '</span>
                </div>
            </div>
        ';
    }

    public function handleUpload() {
        if (!isset($_FILES['file'])) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        $file = $_FILES['file'];
        $context = $_POST['context'] ?? 'analyze';

        try {
            // Create upload folder if it doesn't exist
            $uploadDir = 'fileadmin/user_upload/chat_files/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . $file['name'];
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception('Failed to move uploaded file');
            }

            // Process file with AI service
            $aiService = GeneralUtility::makeInstance(\AIService::class);
            $analysis = $aiService->analyzeContent(file_get_contents($filePath), $context);

            return new JsonResponse([
                'success' => true,
                'analysis' => $analysis
            ]);

        } catch (\Exception $error) {
            return new JsonResponse([
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function handleEmergency() {
        try {
            $position = [
                'latitude' => -33.9179,
                'longitude' => 18.4233
            ];
            
            $emergencyMessage = sprintf(
                '🚨 EMERGENCY ALERT 🚨\n\nMatshepo is not safe and needs immediate assistance.\n\n' .
                'Location: https://www.google.com/maps?q=%f,%f\n\n' .
                '⚠️ IMPORTANT: Please stay calm and immediately contact emergency services:\n\n' .
                '🚔 Police: 10111\n🚑 Ambulance: 10177\n🆘 Emergency from cell: 112',
                $position['latitude'],
                $position['longitude']
            );

            // Send emergency alert via TYPO3 notification service
            $notificationService = GeneralUtility::makeInstance(\NotificationService::class);
            $notificationService->sendEmergencyAlert($emergencyMessage, $position);

            return new JsonResponse([
                'success' => true,
                'message' => 'Emergency alert sent successfully'
            ]);

        } catch (\Exception $error) {
            return new JsonResponse([
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function formatIncidentReport($analysis) {
        return '
            <div class="incident-report">
                <div class="report-header">
                    <i data-lucide="file-text" size="20"></i>
                    <span>Incident Report</span>
                </div>
                <div class="report-content">' .
                    nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analysis)) .
                '</div>
            </div>
        ';
    }
}

// Initialize controller
$chatController = GeneralUtility::makeInstance(ChatController::class);