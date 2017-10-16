<?php

namespace AppBundle\TicTacToe\Strategy\Vendor;

use AppBundle\TicTacToe\Strategy\ {
    MoveInterface,
    AbstractStrategy,
    StrategyException
};
class ZeroSum extends AbstractStrategy implements MoveInterface
{
    private $depth    = 1;

    /**
     * Get opposite symbol
     *
     * @param  string $unit
     * @return string
     */
    private function getOppositeSymbol (string $unit) : string
    {
        return ($this->primaryPlayerSymbol === $unit)
            ? $this->secondaryPlayerSymbol
            : $this->primaryPlayerSymbol;
    }

    protected function validate($unit)
    {
        if (empty($this->boardState)) {
            throw new StrategyException(StrategyException::EMPTY, 400);
        }
    }

    /**
     *
     *
     * @param array  $boardState
     * @param string $unit
     * @param int    $depth
     * @param array  $point
     * @param int    $coefficient  // represent one of the strategy behaviour -1 - min; 1 - max
     * @return array
     */
    public function minmax($boardState, $unit, $depth = 0, $point = [], $coefficient = -1)
    {
        $X =  $point[0] ?? -1;
        $Y =  $point[1] ?? -1;

        $isWinCombinationPresent = $this->isWinnerCombinationPresent($boardState);
        if (
            $isWinCombinationPresent
            || $depth >= $this->depth
        ) {
            $score = $coefficient * ( ($X == $Y) ? 1 : 0); // draw, but be on main diagonal big opportunity
            if ($isWinCombinationPresent) {
                $winnerUnit = $this->getWinnerSymbolIfPresent($boardState);
                $score = $coefficient * (( $winnerUnit === $unit ) ? -$this->winLength : $this->winLength);
            }
        } else {
            $score = $coefficient > 0? -1000 : 1000;
            foreach ($boardState as $x => $rowSet) {
                foreach ($rowSet as $y => $value) {
                    if (!in_array($value, [$this->primaryPlayerSymbol, $this->secondaryPlayerSymbol])) {
                        $tmpBoardState = $boardState;
                        $tmpBoardState[$x][$y] = $this->getOppositeSymbol($unit);

                        $scorePoint = $this->minmax(
                            $tmpBoardState,                    // board state after step
                            $this->getOppositeSymbol($unit),   // opponent symbol representation
                            $depth+1,                   // depth of recursion
                            [$x, $y],                          // new position
                            -1 * $coefficient        // -1 - min; 1 - max // flip
                        );
                        $tmpScore = $scorePoint[2];  // 3 parameter

                        if ($coefficient < 0) {
                            // min strategy
                            if ($tmpScore < $score) {
                                $score = $tmpScore;
                                $X     = $scorePoint[0];
                                $Y     = $scorePoint[1];
                            }
                        } else {
                            // max strategy
                            if ($tmpScore > $score) {
                                $score = $tmpScore;
                                $X     = $scorePoint[0];
                                $Y     = $scorePoint[1];
                            }
                        }
                    }

                }
            }
        }

        return [ $X, $Y, $score];
    }

    public function __construct($params)
    {
        parent::__construct($params);
        $this->depth = (int)($params['depth'] ?? $this->depth);
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

        if ($this->isPrimarySymbol($playerUnit)) {
            $attackPointAI = $this->minmax($boardState, $playerUnit, 0, [], 1);

            // try to prevent winning in one step
            $preventPointAI  =  $this->minmax($boardState, $playerUnit, $this->depth - 1, [], 1);

            $pointAI = $attackPointAI;
        } else {
            $preventPointAI = $this->minmax($boardState, $playerUnit);

            // check possibility to be winner in one step
            $attackPointAI = $this->minmax($boardState, $this->getOppositeSymbol($playerUnit), $this->depth - 1);

            $pointAI = $preventPointAI;
        }

        /**
         * Classic zero sum tactic means that we use only have one way
         *   First player always attack (max strategy)
         *   Second player always try be alive (min strategy)
         *
         *   This tactic create a situation when AI loose win or was loss when we it have choice
         *      It is out of rules but I have try to mix both variant check if you are in one step to win or loss
         *
         *   if you in one step to be winner why not ?
         */
        if (
            abs($preventPointAI[2]) === abs($attackPointAI[2])
            || abs($preventPointAI[2]) < abs($attackPointAI[2])
        ) {
            $pointAI = $attackPointAI;
        }

        return [$pointAI[0], $pointAI[1], $playerUnit];
    }
}
