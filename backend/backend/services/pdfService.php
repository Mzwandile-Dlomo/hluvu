<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class PDFService {
    private $resourceFactory;

    public function __construct() {
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }

    public function extractText($pdfFile) {
        try {
            // Use TYPO3's file abstraction layer to handle the PDF
            $file = $this->resourceFactory->retrieveFileOrFolderObject($pdfFile);
            
            // Create parser instance using TYPO3's built-in PDF parser
            $parser = GeneralUtility::makeInstance(\Smalot\PdfParser\Parser::class);
            
            // Parse PDF content
            $pdf = $parser->parseFile($file->getForLocalProcessing(false));
            
            // Extract text
            return $pdf->getText();
        } catch (\Exception $error) {
            throw new \Exception('Failed to extract text from PDF: ' . $error->getMessage());
        }
    }
}