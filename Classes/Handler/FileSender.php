<?php

namespace Localizationteam\Localizer\Handler;

use Exception;
use Localizationteam\Localizer\Constants;
use Localizationteam\Localizer\Data;
use Localizationteam\Localizer\Language;
use Localizationteam\Localizer\Runner\SendFile;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FileSender takes care to send file(s) to Localizer
 *
 * @author      Peter Russ<peter.russ@4many.net>, Jo Hasenau<jh@cybercraft.de>
 * @package     TYPO3
 * @subpackage  localizer
 *
 */
class FileSender extends AbstractHandler
{
    use Data, Language;

    /**
     * @var string
     */
    protected $uploadPath = '';

    /**
     * @param $id
     * @throws Exception
     */
    public function init($id = 1)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(Constants::TABLE_EXPORTDATA_MM);
        $this->setAcquireWhere(
            $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    'status',
                    $queryBuilder->createNamedParameter(Constants::HANDLER_FILESENDER_START, PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'action',
                    $queryBuilder->createNamedParameter(Constants::ACTION_SEND_FILE, PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'last_error',
                    $queryBuilder->createNamedParameter('', PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'processid',
                    $queryBuilder->createNamedParameter('', PDO::PARAM_STR)
                )
            )
        );
        $this->setLimit(Constants::HANDLER_FILESENDER_MAX_FILES);
        parent::init($id);
        if ($this->canRun()) {
            $this->initData();
            $this->load();
        }
    }

    /**
     *
     * @throws Exception
     */
    public function run()
    {
        if ($this->canRun() === true) {
            foreach ($this->data as $row) {
                $file = $this->getFileAndPath($row['filename']);
                if ($file === false) {
                    $this->addErrorResult(
                        $row['uid'],
                        Constants::STATUS_CART_ERROR,
                        $row['status'],
                        'File ' . $row['filename'] . ' not found'
                    );
                } else {
                    $localizerSettings = $this->getLocalizerSettings($row['uid_local']);
                    if ($localizerSettings === false) {
                        $this->addErrorResult(
                            $row['uid'],
                            Constants::STATUS_CART_ERROR,
                            $row['status'],
                            'LOCALIZER settings (' . $row['uid_local'] . ') not found'
                        );
                    } else {
                        $additionalConfiguration = [
                            'localFile' => $file,
                            'file' => $row['filename'],
                        ];
                        $deadline = $this->addDeadline($row);
                        if (!empty($deadline)) {
                            $additionalConfiguration['deadline'] = $deadline;
                        }
                        $metadata = $this->addMetaData($row);
                        if (!empty($metadata)) {
                            $additionalConfiguration['metadata'] = $metadata;
                        }
                        $translateAll = $this->translateAll($row);
                        if ($translateAll === false) {
                            $targetLocalesUids = $this->getAllTargetLanguageUids(
                                $row['uid'],
                                Constants::TABLE_EXPORTDATA_MM
                            );
                            $additionalConfiguration['targetLocales'] =
                                $this->getStaticLanguagesCollateLocale($targetLocalesUids, true);
                        }
                        $configuration = array_merge(
                            (array)$localizerSettings,
                            $additionalConfiguration
                        );
                        if ((int)$row['action'] === Constants::ACTION_SEND_FILE) {
                            /** @var SendFile $runner */
                            $runner = GeneralUtility::makeInstance(SendFile::class);
                            $runner->init($configuration);
                            $runner->run();
                            $response = $runner->getResponse();
                            //fixme:: improve error handling
                            if ($response === '') {
                                $this->addSuccessResult(
                                    $row['uid'],
                                    Constants::STATUS_CART_FILE_SENT,
                                    Constants::ACTION_REQUEST_STATUS
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $fileName
     * @return bool|string
     */
    protected function getFileAndPath($fileName)
    {
        $file = $this->getUploadPath() . $fileName;
        return file_exists($file) ? $file : false;
    }

    /**
     * @return string
     */
    protected function getUploadPath()
    {
        if ($this->uploadPath === '') {
            $this->uploadPath = PATH_site . 'uploads/tx_l10nmgr/jobs/out/';
        }
        return $this->uploadPath;
    }

    /**
     * @param array $row
     * @return int
     */
    protected function addDeadline(&$row)
    {
        $deadline = '';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(Constants::TABLE_EXPORTDATA_MM);
        $queryBuilder->getRestrictions();
        $carts = $queryBuilder
            ->selectLiteral('COALESCE (
                NULLIF(' . Constants::TABLE_EXPORTDATA_MM . '.deadline, 0), ' .
                Constants::TABLE_LOCALIZER_CART . '.deadline
            ) deadline')
            ->from(Constants::TABLE_EXPORTDATA_MM)
            ->leftJoin(
                Constants::TABLE_EXPORTDATA_MM,
                Constants::TABLE_LOCALIZER_CART,
                Constants::TABLE_LOCALIZER_CART,
                $queryBuilder->expr()->eq(
                    Constants::TABLE_LOCALIZER_CART . '.uid_foreign',
                    $queryBuilder->quoteIdentifier(Constants::TABLE_EXPORTDATA_MM . '.uid_foreign')
                )
            )->where(
                $queryBuilder->expr()->eq(
                    Constants::TABLE_EXPORTDATA_MM . ' .uid',
                    $queryBuilder->createNamedParameter((int)$row['uid'], PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchColumn(0);

        if (!empty($carts['deadline'])) {
            $deadline = (int)$carts['deadline'];
        }
        return $deadline;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function addMetaData(&$row)
    {
        $metaData = [];
        $hooks = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['localizer']['addMetaData'];
        if (is_array($hooks)) {
            foreach ($hooks as $hookObj) {
                $metaData = GeneralUtility::callUserFunction($hookObj, $row, $this);
            }
        }
        return $metaData;
    }

    /**
     * @param int $time
     * @return void
     */
    public function finish($time)
    {
        $this->dataFinish($time);
    }
}