<?php

namespace AppBundle\TicTacToe\Strategy\Vendor;

use AppBundle\TicTacToe\Strategy\ {
    MoveInterface,
    AbstractStrategy,
    StrategyException
};

class Random extends AbstractStrategy implements MoveInterface
{
    private $min;
    private $max;

    public function __construct($params)
    {
        parent::__construct($params);

        $this->min = (int) $params['min'];
        $this->max = (int) $params['max'];
    }

    /**
     * Makes a move using the $boardState
     * $boardState contains 2 dimensional array of the game field
     * X represents one team, O - the other team, empty string means field is not yet taken.
     * example
     * [
     *    ['X', 'O', '']
     *    ['X', 'O', 'O']
     *    ...
     *    ['', '', '']
     * ]
     * Returns an array, containing x and y coordinates for next move, and the unit that now occupies it.
     * Example: [2, 0, 'O'] - upper right corner - O player
     *
     * @param array $boardState Current board state
     * @param string $playerUnit Player unit representation
     *
     * @return array
     * @throws StrategyException
     */
    public function makeMove($boardState, $playerUnit = 'X') : array
    {
        $this->setBoardState($boardState);
        $this->validate($playerUnit);
        if ($this->isWinnerCombinationPresent($boardState)) {
            throw new StrategyException(StrategyException::WINNER_COMBINATION_PRESENT, 401);
        }

        $x = rand($this->min, $this->max);
        $y = rand($this->min, $this->max);

        $maxCall     = pow($this->max, 2);
        $currentCall = $this->min;
        while (!empty($boardState[$x][$y]) || $currentCall >= $maxCall) {

            $x = rand($this->min, $this->max);
            $y = rand($this->min, $this->max);

            $currentCall++;
        }

        return [$x, $y, $playerUnit];
    }
}
