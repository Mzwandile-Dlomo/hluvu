<?php
namespace Vendor\Extension\Domain\Model;

class Document extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
    /**
     * @var string
     */
    protected $userId;         // ID of logged in user
    
    /**
     * @var string  
     */
    protected $fileName;       // Original file name
    
    /**
     * @var string
     */
    protected $fileUrl;        // Storage URL
    
    /**
     * @var string
     */
    protected $fileType;       // PDF/Image etc
    
    /**
     * @var \DateTime
     */
    protected $uploadDate;     // Upload timestamp
    
    /**
     * @var string
     */
    protected $category;       // Business/Market/Pitch
    
    /**
     * @var string
     */
    protected $content;        // Extracted text content
}