<?php

/*
 * This file is part of the Yodler package.
 *
 * (c) aes3xs <aes3xs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aes3xs\Yodler\Scenario;

/**
 * Interface to action storage.
 */
interface ActionListInterface
{
    /**
     * Return all actions in array.
     *
     * @return ActionInterface[]
     */
    public function all();

    /**
     * Add variable to a list.
     *
     * @param ActionInterface $action
     */
    public function add(ActionInterface $action);
}