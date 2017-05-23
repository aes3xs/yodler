<?php

/*
 * This file is part of the Yodler package.
 *
 * (c) aes3xs <aes3xs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aes3xs\Yodler\Service;

/**
 * Helper service to manage symfony project.
 */
class Symfony
{
    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var string
     */
    protected $phpPath;

    /**
     * @var string
     */
    protected $env = 'prod';

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var bool
     */
    protected $interaction = false;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Constructor.
     *
     * @param Shell $shell
     */
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @param $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param $interaction
     */
    public function setInteraction($interaction)
    {
        $this->interaction = $interaction;
    }

    /**
     * @param $name
     * @param null $value
     */
    public function setOption($name, $value = null)
    {
        $this->options[$name] = $value;
    }

    public function runCommand($console, $command, $arguments = [], $options = [])
    {
        $php = $this->getPhpPath();

        $predefinedOptions = ['env' => $this->env];
        if (!$this->debug) {
            $predefinedOptions['no-debug'] = null;
        }
        if (!$this->interaction) {
            $predefinedOptions['no-interaction'] = null;
        }

        $options = $predefinedOptions + $this->options + $options;

        $argumentLine = implode(' ', $arguments);
        $optionLine = '';
        foreach ($options as $i => $value) {
            if (is_numeric($i)) {
                $name = $value;
                $value = null;
            } else {
                $name = $i;
            }
            $name = false === strpos($name, '--') ? " --$name" : " $name"; // Add preceding --
            $value = $value ? '=' . $value : '';
            $optionLine .= $name . $value;
        }

        return $this->shell->exec("$php $console $command $argumentLine $optionLine");
    }

    /**
     * @return string
     */
    protected function getPhpPath()
    {
        if (null === $this->phpPath) {
            $phpPath = $this->shell->which('php');
            if (!$phpPath) {
                throw new \RuntimeException('PHP not found');
            }
            $this->phpPath = $phpPath;
        }

        return $this->phpPath;
    }
}
