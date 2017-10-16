<?php

namespace AppBundle\TicTacToe\Strategy\Vendor;

use AppBundle\TicTacToe\Strategy\ {
    MoveInterface,
    AbstractStrategy,
    StrategyException
};
class Minmax extends AbstractStrategy implements MoveInterface
{
    private $depth   = 1;

    private function getVectorScore($vector, $unit)
    {
        $unitScoreForVector = 0;
        foreach ($vector as $index => $value) {
            if (empty($value)) {
                $unitScoreForVector = 0;
            } elseif ($value === $unit) {
                $unitScoreForVector++;
            } else {
                $unitScoreForVector--;
            }
        }
        return $unitScoreForVector;
    }

    public function minmax($unit)
    {
        /**
         * How many step in future we try predict
         */
        $tmpBoardState = $this->boardState;
        $x = -1;
        $y = -1;
        $bestScore = -1;

        for ($depthStep=0; $depthStep < $this->depth; $depthStep++) {
           // score in rows
           foreach ($tmpBoardState as $rowIndex => $boardRow) {
                if (in_array('', $boardRow)) {
                    foreach ($boardRow as $colIndex => $value) {
                        if ('' === $value) {
                            $tmpRow = $boardRow;
                            $tmpRow[$colIndex] = $unit;
                            $score = $this->getVectorScore($tmpRow, $unit);

                            if ($score > $bestScore) {
                                $x = $rowIndex;
                                $y = $colIndex;
                                $bestScore = $score;
                            }
                        }
                    }
                }

                // check column
                $colVector = array_column($tmpBoardState, $rowIndex);
                if (in_array('', $colVector)) {
                    foreach ($colVector as $rIndex => $value ) {
                        if ('' === $value) {
                            $tmpRow = $boardRow;
                            $tmpRow[$rIndex] = $unit;
                            $score = $this->getVectorScore($tmpRow, $unit);

                            if ($score > $bestScore) {
                                $x = $rIndex;
                                $y = $rowIndex;
                                $bestScore = $score;
                            }
                        }
                    }
                }
           }

           // check diagonals
           // right -> left
           $diagonalVector = [];
           for ($i=0; $i < $this->colAmount; $i++) {
               $diagonalVector[] = $tmpBoardState[$i][$i];
           }
            if (in_array('', $diagonalVector)) {
                foreach ($diagonalVector as $index => $value ) {
                    if ('' === $value) {
                        $tmpRow = $diagonalVector;
                        $tmpRow[$index] = $unit;
                        $score = $this->getVectorScore($tmpRow, $unit);

                        if ($score > $bestScore) {
                            $x = $index;
                            $y = $index;
                            $bestScore = $score;
                        }
                    }
                }
            }

            // left -> right
            $diagonalVector = [];
            $j=0;
            for ($i= ($this->colAmount - 1); $i >=0; $i--) {
                $diagonalVector[] = $tmpBoardState[$i][$j];
                $j++;
            }

            if (in_array('', $diagonalVector)) {
                foreach ($diagonalVector as $index => $value ) {
                    if ('' === $value) {
                        $tmpRow = $diagonalVector;
                        $tmpRow[$index] = $unit;
                        $score = $this->getVectorScore($tmpRow, $unit);

                        if ($score > $bestScore) {
                            $x = $index;
                            $y = ($this->colAmount - 1 - $index);
                            $bestScore = $score;
                        }
                    }
                }
            }


            if ($bestScore > 0) {
                $tmpBoardState[$x][$y] = $unit;
            }
        }

        return [$x, $y, $bestScore];
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

//        $AiScoreAndCoordinate     = $this->minmax($this->secondaryPlayerSymbol);
//        $playerScoreAndCoordinate = $this->minmax($this->primaryPlayerSymbol);
//
//        // if player score is winner try protected
//        $result = [];
//        if ($playerScoreAndCoordinate[2] >= $this->winLength) {
//            $result = $playerScoreAndCoordinate;
//
//        // if move from AI has change to win
//        } elseif ($AiScoreAndCoordinate[2] > 0) {
//            $result = $AiScoreAndCoordinate;
//
//        // result will be in draw or game over
//        } else {
//            foreach ($boardState as $x => $row) {
//                $y = array_search('', $row);
//                if (false !== $y) {
//                    $result = [$x, $y, $playerUnit];
//                    break;
//                }
//            }
//        }
//        $result[2] = $playerUnit;
        return $result;
    }
}
