<?php

/*
 * This file is part of the Tasker package.
 *
 * (c) aes3xs <aes3xs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aes3xs\Tasker\Connection;

use Aes3xs\Tasker\Exception\SshExtensionCommandException;
use Ssh\Session;

/**
 * PhpSecLib connection implementation.
 */
class SshExtensionConnection implements ConnectionInterface
{
    const TIMEOUT = 1200;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function exec($command)
    {
        return $this->session->getExec()->run($command);
    }

    /**
     * {@inheritdoc}
     */
    public function send($local, $remote)
    {
        if ($this->session->getSftp()->send($local, $remote) === false) {
            throw new SshExtensionCommandException(sprintf('Error put: from local "%s" to remote "%s"', $local, $remote));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function recv($remote, $local)
    {
        if ($this->session->getSftp()->receive($remote, $local) === false) {
            throw new SshExtensionCommandException(sprintf('Error recv: from remote "%s" to local "%s"', $remote, $local));
        }
    }
}
