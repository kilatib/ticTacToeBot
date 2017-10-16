<?php
namespace AppBundle\TicTacToe\Strategy;

use AppBundle\AppBundleException;

class StrategyException extends AppBundleException
{
    const WINNER = 'Winner %s';
    const WINNER_COMBINATION_PRESENT = 'Winner combination present';
    const EMPTY = 'Empty board state';
    const FORBIDDEN = 'FORBIDDEN';
}
