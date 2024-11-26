<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// Add JavaScript functionality using TYPO3's PageRenderer
$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
$pageRenderer->addJsInlineCode(
    'loginPanelSwitch',
    '
    document.addEventListener("DOMContentLoaded", function() {
        const signUpBtn = document.getElementById("signUp");
        const signInBtn = document.getElementById("signIn"); 
        const container = document.getElementById("container");

        signUpBtn.addEventListener("click", function() {
            container.classList.add("right-panel-active");
        });

        signInBtn.addEventListener("click", function() {
            container.classList.remove("right-panel-active");
        });
    });
    '
);