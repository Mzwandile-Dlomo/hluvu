<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;

class SearchController {
    public function search() {
        try {
            // Get POST data
            $postData = json_decode(file_get_contents('php://input'), true);
            $service = $postData['service'] ?? '';
            $sector = $postData['sector'] ?? '';
            $province = $postData['province'] ?? '';

            // Get search results
            $results = $this->searchCompanies($service);
            
            // Filter results if sector is specified
            if ($sector) {
                $filteredResults = array_filter($results, function($company) use ($sector) {
                    return stripos($company['description'], $sector) !== false;
                });
            } else {
                $filteredResults = $results;
            }

            // Log results
            GeneralUtility::devLog('Search Results:', 'SearchController', 0, $filteredResults);

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode(['results' => $filteredResults]);
        } catch (Exception $e) {
            GeneralUtility::devLog('Search error:', 'SearchController', 3, ['error' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    private function searchCompanies($service) {
        // Implementation needed - replace with actual data fetching logic
        // This is a placeholder that should be replaced with your data service implementation
        return [];
    }
}