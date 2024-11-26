<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class AuthenticationService {
    private $backendUser;
    
    public function __construct() {
        $this->backendUser = GeneralUtility::makeInstance(BackendUserAuthentication::class);
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $loginData = [
                    'uname' => $email,
                    'uident' => $password,
                    'status' => 'login'
                ];

                $isAuthenticated = $this->backendUser->checkAuthentication($loginData);

                if ($isAuthenticated) {
                    // Successful login
                    header('Location: /typo3/login/hero.php');
                    exit;
                } else {
                    throw new \Exception('Invalid credentials');
                }

            } catch (\Exception $error) {
                // Handle login error
                $errorMessage = '<div class="error-message" style="color: red; margin-top: 10px;">';
                $errorMessage .= 'Invalid email or password. Please try again.';
                $errorMessage .= '</div>';
                echo $errorMessage;
            }
        }
    }
}

// Initialize and handle login
$authService = GeneralUtility::makeInstance(AuthenticationService::class);
$authService->handleLogin();