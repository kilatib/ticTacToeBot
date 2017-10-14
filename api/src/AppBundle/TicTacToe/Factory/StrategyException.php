<?php

namespace AppBundle\TicTacToe\Factory;

use PHPUnit\Runner\Exception;

use AppBundle\AppBundleException;

class StrategyException extends AppBundleException
{
    const INVALID_VENDOR = 'Invalid Vendor config';
    const NOT_FOUND = 'Vendor %s not found';
}