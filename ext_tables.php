<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    $extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('localizer');

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'localizer',
        '', // Submodule key
        'top',    // Position
        $extPath . 'Modules/Localizer/',
        [
            'routeTarget' => \Localizationteam\Localizer\Controller\LocalizerController::class . '::mainAction',
            'access'      => 'user,group',
            'name'        => 'localizer',
            'icon'        => 'EXT:localizer/Resources/Public/Icons/module-localizer.svg',
            'labels'      => 'LLL:EXT:localizer/Resources/Private/Language/locallang_localizer.xlf',
        ]
    );

    // Register the page tree core navigation component
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addNavigationComponent('localizer', 'typo3-pagetree', 'localizer');


    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'localizer', // Make module a submodule of 'Localizer'
        'localizerselector', // Submodule key
        '',    // Position
        $extPath . 'Modules/Localizer/Selector/',
        [
            'routeTarget' => \Localizationteam\Localizer\Controller\SelectorController::class . '::mainAction',
            'access'      => 'user,group',
            'name'        => 'localizer_localizerselector',
            'icon'        => 'EXT:localizer/Resources/Public/Icons/module-localizer-selector.svg',
            'labels'      => 'LLL:EXT:localizer/Resources/Private/Language/locallang_localizer_selector.xlf',
        ]
    );

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'localizer', // Make module a submodule of 'Localizer'
        'localizercart', // Submodule key
        '',    // Position
        $extPath . 'Modules/Localizer/Cart/',
        [
            'routeTarget' => \Localizationteam\Localizer\Controller\CartController::class . '::mainAction',
            'access'      => 'user,group',
            'name'        => 'localizer_localizercart',
            'icon'        => 'EXT:localizer/Resources/Public/Icons/module-localizer-cart.svg',
            'labels'      => 'LLL:EXT:localizer/Resources/Private/Language/locallang_localizer_cart.xlf',
        ]
    );

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'localizer', // Make module a submodule of 'Localizer'
        'localizersettings', // Submodule key
        '',    // Position
        $extPath . 'Modules/Localizer/Settings/',
        [
            'routeTarget'           => \Localizationteam\Localizer\Controller\SettingsController::class . '::mainAction',
            'access'                => 'user,group',
            'name'                  => 'localizer_localizersettings',
            'icon'                  => 'EXT:localizer/Resources/Public/Icons/module-localizer-settings.svg',
            'labels'                => 'LLL:EXT:localizer/Resources/Private/Language/locallang_localizer_settings.xlf',
            'navigationComponentId' => 'typo3-pagetree',

        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_localizer_settings,tx_localizer_cart,tx_localizer_settings_l10n_exportdata_mm');
}