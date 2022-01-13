<?php

namespace Localizationteam\Localizer\Handler;

use Exception;
use Localizationteam\Localizer\Constants;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * AbstractHandler $COMMENT$
 *
 * @author      Peter Russ<peter.russ@4many.net>, Jo Hasenau<jh@cybercraft.de>
 */
abstract class AbstractHandler
{
    /**
     * @var string
     */
    protected $processId = '';
    /**
     * @var bool
     */
    private $run = false;
    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @param int $id
     * @throws Exception
     */
    abstract public function init(int $id = 1);

    abstract public function run();

    final public function __destruct()
    {
        $time = time();
        $this->finish($time);
        $this->releaseAcquiredItems($time);
    }

    /**
     * @param int $time
     */
    abstract public function finish(int $time);

    /**
     * @param int $time
     */
    protected function releaseAcquiredItems(int $time = 0)
    {
        if ($time == 0) {
            $time = time();
        }
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(Constants::TABLE_EXPORTDATA_MM)
            ->update(
                Constants::TABLE_EXPORTDATA_MM,
                [
                    'tstamp' => $time,
                    'processid' => '',
                ],
                [
                    'processid' => $this->processId,
                ],
                [
                    Connection::PARAM_INT,
                    Connection::PARAM_STR,
                ]
            );
    }

    /**
     * @return string
     */
    final public function getProcessId(): string
    {
        return $this->processId;
    }

    abstract protected function acquire(): bool;

    final protected function initProcessId()
    {
        $this->processId = md5(uniqid('', true) . (microtime(true) * 10000));
    }

    final protected function initRun()
    {
        $this->run = true;
    }

    /**
     * @param int $limit
     */
    protected function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    final protected function resetRun()
    {
        $this->run = false;
    }

    /**
     * @return bool
     */
    final protected function canRun(): bool
    {
        return (bool)$this->run;
    }
}
