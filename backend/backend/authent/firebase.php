<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class FirebaseService {
    private $firebaseConfig = [
      // removed for safety

    private $backendUser;

    public function __construct() {
        $this->backendUser = GeneralUtility::makeInstance(BackendUserAuthentication::class);
    }

    public function authenticate($email, $password) {
        try {
            // Use TYPO3's authentication instead of Firebase
            $loginData = [
                'uname' => $email,
                'uident' => $password,
                'status' => 'login'
            ];

            return $this->backendUser->checkAuthentication($loginData);
            
        } catch (\Exception $error) {
            throw new \Exception('Authentication failed: ' . $error->getMessage());
        }
    }
}