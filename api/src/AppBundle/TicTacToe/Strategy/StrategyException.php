<?php
namespace AppBundle\TicTacToe\Strategy;

use AppBundle\AppBundleException;

/**
 * Class StrategyException
 *      Exception Layer to determinate strategies errors from any vendors
 *
 * @package AppBundle\TicTacToe\Factory
 */
class StrategyException extends AppBundleException
{
    const WINNER = 'Winner %s';
    const WINNER_COMBINATION_PRESENT = 'Winner combination present';
    const EMPTY = 'Empty board state';
    const FORBIDDEN = 'FORBIDDEN';
    const INVALID_VENDOR = 'Invalid Vendor config';
    const NOT_FOUND = 'Vendor %s not found';
}
