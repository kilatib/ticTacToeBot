<?php
/**
 *  Move here common steps for all vendor's strategies
 */
namespace AppBundle\TicTacToe\Strategy;

abstract class AbstractStrategy
{
    /**
     * Board state
     * @var array
     */
    protected $boardState = [];

    /** @var int $colAmount // items in row  */
    protected $colAmount = -1;
    /** @var int $rowAmount // row in matrix */
    protected $rowAmount = -1;

    protected $winLength;

    /**
     *  Logically primary symbol potentially not always be `X`
     *    but if user play with AI then it mean X.
     *    because AI do always odd move
     */
    protected $primaryPlayerSymbol;
    protected $secondaryPlayerSymbol;

    /**
     * Parse board
     *
     * @param array $boardState
     */
    protected function setBoardState(array $boardState)
    {
        $this->boardState = $boardState;

        $this->determinateBoardSize($boardState);
        $this->determinatePlayerSymbols($boardState);
    }

    /**
     * Detect usage symbols
     *      save next step symbol
     *
     * @param array $boardState
     */
    private function determinatePlayerSymbols(array $boardState)
    {
        $this->boardState = $boardState;

        $primaryStepAmount   = 0;
        $secondaryStepAmount = 0;

        /**
         * This solution justified by 2 thing: speed, and no reason to use two level circles
         */
        array_walk_recursive($boardState, function($symbol) use(&$primaryStepAmount, &$secondaryStepAmount){
            if(!empty($symbol)) {
                if (null == $this->primaryPlayerSymbol) {
                    $this->primaryPlayerSymbol = $symbol;
                } elseif (
                    $symbol !== $this->primaryPlayerSymbol
                    && $symbol !== $this->secondaryPlayerSymbol
                ) {
                    $this->secondaryPlayerSymbol = $symbol;
                }

                switch($symbol){
                    case $this->primaryPlayerSymbol:
                        $primaryStepAmount++;
                        break;
                    case $this->secondaryPlayerSymbol:
                        $secondaryStepAmount++;
                        break;
                }
            }
        });

        // set next step symbol
        // need for check reason of next symbol
        if ($primaryStepAmount < $secondaryStepAmount) {
            list($this->primaryPlayerSymbol, $this->secondaryPlayerSymbol)
                = [$this->secondaryPlayerSymbol, $this->primaryPlayerSymbol];
        }
    }

    /**
     * Determinate last step player symbols
     *
     * @param string $unit
     * @return bool
     */
    protected function isPrimarySymbol ($unit) : bool
    {
        return $unit === $this->primaryPlayerSymbol;
    }

    /**
     * Determinate next step player symbols
     *
     * @param string $unit
     * @return bool
     */
    protected function isSecondarySymbol ($unit) : bool
    {
        return $unit === $this->secondaryPlayerSymbol;
    }

    /**
     * Limit size
     *
     * @param array $boardState
     */
    protected function determinateBoardSize(array $boardState)
    {
        $this->colAmount = count(reset($boardState));
        $this->rowAmount = count($boardState);
    }

    protected function validate($unit)
    {
        if (empty($this->boardState)) {
            throw new StrategyException(StrategyException::EMPTY, 400);
        }

        if (
            !empty($this->primaryPlayerSymbol)
            && !empty($this->secondaryPlayerSymbol)
            && !$this->isSecondarySymbol($unit)
        ) {
            throw new StrategyException(StrategyException::FORBIDDEN, 401);
        }
    }

    /**
     * Detect is winner combination present in given row
     *
     * @param array $matrix
     * @return bool
     */
    protected function isWinnerCombinationInRowSet($matrix)
    {
        $flag = false;

        // in rows
        foreach ($matrix as $row) {
            $duplicateCount = count($row) - count(array_unique($row)) + 1 ; // one symbol always still exist

            if ($duplicateCount >= $this->winLength) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }

    /**
     * Detect is winner combination present in given row
     *
     * @param array $matrix
     * @return bool
     */
    protected function isWinnerCombinationInDiagonal($matrix)
    {
        $diagonalVector = [];
        for($i=0; $i < count($matrix); $i++ ) {
            $diagonalVector[] = $matrix[$i][$i];
        }

        $duplicateCount = count($diagonalVector) - count(array_unique($diagonalVector)) + 1; // one symbol always still exist

        return $duplicateCount >= $this->winLength;
    }

    public function __construct($params)
    {
        $this->winLength = (int)($params['winLength'] ?? $this->winLength);
    }

    /**
     * Try to find winner combinations
     *
     * @param array $boardState
     *
     * @return bool
     */
    public function isWinnerCombinationPresent($boardState)
    {
        // check rows
        $winnerIn = $this->isWinnerCombinationInRowSet($boardState);

        // check columns
        $columnList = [];
        foreach ($boardState as $key => $row) {
            $columnList[] = array_column($this->boardState, $key);
        }
        $winnerIn = $winnerIn || $this->isWinnerCombinationInRowSet($columnList);

        // left -> right diagonal vectors
        $winnerIn = $winnerIn || $this->isWinnerCombinationInDiagonal($boardState);

        // right -> left diagonal vectors
        // Flip matrix for always move from left to right
        $reverseArray = $this->boardState;
        foreach ($reverseArray as $key => $row) {
            $reverseArray[$key] = array_reverse($row);
        }
        $winnerIn = $winnerIn || $this->isWinnerCombinationInDiagonal($reverseArray);

        return $winnerIn;
    }
}
