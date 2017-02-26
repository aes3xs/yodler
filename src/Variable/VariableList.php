<?php

/*
 * This file is part of the Yodler package.
 *
 * (c) aes3xs <aes3xs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aes3xs\Yodler\Variable;

use Aes3xs\Yodler\Exception\VariableAlreadyExistsException;
use Aes3xs\Yodler\Exception\VariableNotFoundException;

/**
 * Default implementation for variable list.
 */
class VariableList implements VariableListInterface
{
    /**
     * @var array
     */
    protected $variables = [];

    /**
     * Constructor.
     *
     * @param array $variables
     */
    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->variables;
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $value)
    {
        if ($this->has($name)) {
            throw new VariableAlreadyExistsException($name);
        }

        $this->variables[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new VariableNotFoundException($name);
        }

        return $this->variables[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return array_key_exists($name, $this->variables);
    }
}
