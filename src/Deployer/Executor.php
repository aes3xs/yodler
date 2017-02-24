<?php

/*
 * This file is part of the Yodler package.
 *
 * (c) aes3xs <aes3xs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aes3xs\Yodler\Deployer;

use Aes3xs\Yodler\Action\ActionListInterface;
use Aes3xs\Yodler\Heap\HeapInterface;
use Psr\Log\LoggerInterface;

/**
 * Executor implementation.
 */
class Executor implements ExecutorInterface
{
    /**
     * @var HeapInterface
     */
    protected $heap;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param HeapInterface $heap
     * @param ReportInterface $report
     * @param LoggerInterface $logger
     */
    public function __construct(HeapInterface $heap, ReportInterface $report, LoggerInterface $logger)
    {
        $this->heap = $heap;
        $this->report = $report;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ActionListInterface $actions)
    {
        foreach ($actions->all() as $action) {
            try {
                $this->report->reportActionRunning($action);
                $this->logger->info('➤ ' . $action->getName());
                if ($action->skip($this->heap)) {
                    $this->report->reportActionSkipped($action);
                    $this->logger->info('⇣ ' . $action->getName());
                    continue;
                }
                $output = $action->execute($this->heap);
                $this->report->reportActionSucceed($action, $output);
                $this->logger->info('✔ ' . $action->getName());
                if ($output) {
                    $this->logger->info('• ' . $action->getName() . ': ' . (string) $output);
                }
            } catch (\Exception $e) {
                $this->report->reportActionError($action, $e);
                $this->logger->error('✘ ' . $action->getName(), [$e]);
                throw $e;
            }
        }
    }
}