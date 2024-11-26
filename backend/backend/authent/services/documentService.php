<?php
namespace TYPO3\CMS\Backend\Service;

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DocumentService {
    protected $resourceFactory;
    protected $storageRepository;
    protected $connection;

    public function __construct() {
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $this->storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_documents');
    }

    public function uploadDocument($file, $userId, $category, $content) {
        try {
            // Upload file to TYPO3 storage
            $storage = $this->storageRepository->getDefaultStorage();
            $targetFolder = $storage->getFolder('documents/' . $userId);
            $fileName = time() . '_' . $file->getClientOriginalName();
            $newFile = $targetFolder->addUploadedFile($file, $fileName);
            $fileUrl = $newFile->getPublicUrl();

            // Store document metadata in database
            $data = [
                'user_id' => $userId,
                'file_name' => $file->getClientOriginalName(),
                'file_url' => $fileUrl,
                'file_type' => $file->getMimeType(),
                'upload_date' => time(),
                'category' => $category,
                'content' => $content
            ];

            $this->connection->insert('tx_documents', $data);
            return $this->connection->lastInsertId();

        } catch (\Exception $error) {
            throw new \TYPO3\CMS\Core\Error\Exception(
                'Failed to upload document: ' . $error->getMessage()
            );
        }
    }

    public function getUserDocuments($userId, $category = null) {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('tx_documents')
                ->where(
                    $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId))
                )
                ->orderBy('upload_date', 'DESC');

            if ($category !== null) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq('category', $queryBuilder->createNamedParameter($category))
                );
            }

            $result = $queryBuilder->executeQuery();
            return $result->fetchAllAssociative();

        } catch (\Exception $error) {
            throw new \TYPO3\CMS\Core\Error\Exception(
                'Failed to fetch documents: ' . $error->getMessage()
            );
        }
    }
}