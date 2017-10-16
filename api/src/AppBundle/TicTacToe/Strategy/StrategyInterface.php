<?php

namespace AppBundle\TicTacToe\Strategy;

/**
 * Interface StrategyInterface
 *
 *   This interface is pure but his goal just declaim communication with external world
 *
 * @package AppBundle\TicTacToe\Strategy
 */
interface StrategyInterface
{
    /**
     * Check board state for continue game
     *
     * @param array $boardState
     * @return bool
     */
    public function isWinnerCombinationPresent(array $boardState) : bool;

    /**
     * Return symbol of game winner
     *
     * @param array $boardState
     *
     * @return string
     */
    public function getWinnerSymbolIfPresent($boardState) : string;
}
