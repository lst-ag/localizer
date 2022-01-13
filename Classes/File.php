<?php

namespace Localizationteam\Localizer;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * File
 *
 * @author      Peter Russ<peter.russ@4many.net>, Jo Hasenau<jh@cybercraft.de>
 */
trait File
{
    /**
     * @param string $fileName
     * @param string $locale
     * @return string
     */
    protected function getLocalFilename(string $fileName, string $locale): string
    {
        $downloadPath = Environment::getPublicPath() . '/uploads/tx_l10nmgr/jobs/in/' . strtolower($locale);
        if (!@is_dir($downloadPath)) {
            GeneralUtility::mkdir_deep($downloadPath);
        }
        return $downloadPath . '/' . $fileName;
    }
}
