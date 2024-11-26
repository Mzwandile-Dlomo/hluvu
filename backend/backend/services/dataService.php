<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataService {
    private $dataFilePath;

    public function __construct() {
        $this->dataFilePath = GeneralUtility::getFileAbsFileName('EXT:your_extension/Resources/Private/Data/data.txt');
    }

    public function readDataFile() {
        try {
            $data = file_get_contents($this->dataFilePath);
            if ($data === false) {
                throw new \Exception('Could not read data file');
            }
            return $this->parseData($data);
        } catch (\Exception $error) {
            throw new \Exception('Failed to read data file: ' . $error->getMessage());
        }
    }

    private function parseData($data) {
        $sections = [];
        $currentTitle = '';
        $currentList = [];

        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'Title :') === 0 || strpos($line, 'tITLE =') === 0) {
                if ($currentTitle) {
                    $sections[$currentTitle] = $currentList;
                }
                $parts = (strpos($line, '=') !== false) ? explode('=', $line, 2) : explode(':', $line, 2);
                $currentTitle = trim($parts[1] ?? '');
                $currentList = [];
            } elseif ($line && strpos($line, 'TRACXN') === false && strpos($line, 'INCUBATORLIST') === false) {
                $colonIndex = strpos($line, ':');
                if ($colonIndex !== false) {
                    $name = trim(substr($line, 0, $colonIndex));
                    $description = trim(substr($line, $colonIndex + 1));
                } else {
                    $name = $line;
                    $description = $line;
                }
                
                if ($name) {
                    $currentList[] = [
                        'name' => $name,
                        'description' => $description ?: $name
                    ];
                }
            }
        }

        if ($currentTitle && !empty($currentList)) {
            $sections[$currentTitle] = $currentList;
        }

        return $sections;
    }

    public function searchCompanies($service) {
        $data = $this->readDataFile();
        $searchKey = ($service === 'incubation') ? 'INCUBATION' : 'Investment';
        return $data[$searchKey] ?? [];
    }
}