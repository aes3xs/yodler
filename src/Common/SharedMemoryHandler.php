<?php

/*
 * This file is part of the Yodler package.
 *
 * (c) aes3xs <aes3xs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aes3xs\Yodler\Common;

use Aes3xs\Yodler\Exception\RuntimeException;

/**
 * Implements shared memory feature to share data between deploy processes.
 *
 * Wraps shared memory functionality from PHP core.
 * Stores data in JSON format.
 */
class SharedMemoryHandler
{
    const MODE_READ = 'a';
    const MODE_CREATE = 'c';
    const MODE_READ_WRITE = 'w';
    const MODE_NEW = 'n';

    const PERMISSIONS = 0755;

    /**
     * @var int
     */
    protected $key;

    /**
     * Constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->key = $this->getIntegerHash($name);
    }

    /**
     * Generate integer hash from string.
     *
     * Integer key is required by PHP shared memory functions.
     *
     * http://stackoverflow.com/a/6315489
     *
     * @param string $str
     * @return int
     */
    protected function getIntegerHash($str)
    {
        $u = unpack('N2', sha1($str, true));
        return ($u[1] << 32) | $u[2];
    }

    /**
     * Return shared memory data.
     *
     * If shared memory has no data, return null.
     *
     * @return mixed|null
     */
    public function read()
    {
        $shmid = @shmop_open($this->key, self::MODE_READ, self::PERMISSIONS, 0);

        if (!$shmid) {
            return null;
        }

        $data = null;
        $size = shmop_size($shmid);

        if ($size) {
            $content = shmop_read($shmid, 0, $size);
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Json decode problem: ' . $content);
            }
        }

        shmop_close($shmid);

        return $data;
    }

    /**
     * Write data into shared memory.
     *
     * @param $data
     */
    public function dump($data)
    {
        $shmid = @shmop_open($this->key, self::MODE_READ, self::PERMISSIONS, 0);

        if ($shmid) {
            shmop_delete($shmid);
            shmop_close($shmid);
        }

        $content = json_encode($data);
        $size = mb_strlen($content, 'UTF-8');

        $shmid = @shmop_open($this->key, self::MODE_CREATE, self::PERMISSIONS, $size);

        if (!$shmid) {
            throw new RuntimeException('Shared memory write error: ' . error_get_last()['message']);
        }

        shmop_write($shmid, $content, 0);
        shmop_close($shmid);
    }
}