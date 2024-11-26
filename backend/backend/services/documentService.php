<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DocumentService {
    private $connection;

    public function __construct() {
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_documents');
    }

    public function storeDocument($userId, $file, $context, $content) {
        try {
            $data = [
                'user_id' => $userId,
                'file_name' => $file['name'],
                'file_type' => $file['type'], 
                'context' => $context,
                'content' => $content,
                'uploaded_at' => time()
            ];

            $this->connection->insert('tx_documents', $data);
            return $this->connection->lastInsertId();
        } catch (\Exception $error) {
            throw new \Exception('Failed to store document: ' . $error->getMessage());
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
                ->orderBy('uploaded_at', 'DESC');

            if ($category !== null) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq('category', $queryBuilder->createNamedParameter($category))
                );
            }

            $result = $queryBuilder->executeQuery();
            return $result->fetchAllAssociative();
        } catch (\Exception $error) {
            throw new \Exception('Failed to fetch documents: ' . $error->getMessage());
        }
    }
}