<?php
/**
 *  Move here common steps for all vendor's strategies
 */
namespace AppBundle\TicTacToe\Strategy;

use AppBundle\Model\Board\FieldInterface;

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

        if (empty($this->primaryPlayerSymbol)) {
            $this->primaryPlayerSymbol = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        }

        if (empty($this->secondaryPlayerSymbol)) {
            $this->secondaryPlayerSymbol = FieldInterface::SECONDARY_PLAYER_SYMBOL;
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
     * Detect is winner combination present in given row set
     *    If winner combination present as result provide winner symbol
     *
     * @param array $matrix
     * @return bool|string
     */
    protected function isWinnerCombinationInRowSet($matrix)
    {
        $flag = false;

        // in rows
        foreach ($matrix as $row) {
            $tmpRow = array_diff($row, ['', '-']);                        // remove all not filled elements
            if (!empty($tmpRow)) {
                $uniqueList     = array_unique($tmpRow);
                $duplicateCount = count($tmpRow) - count($uniqueList) + 1 ; // one symbol always still exist
                if ($duplicateCount >= $this->winLength) {
                    $flag = reset($uniqueList);
                    break;
                }
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

        return $this->isWinnerCombinationInRowSet([$diagonalVector]);
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
        $winnerIn = $this->getWinnerSymbolIfPresent($boardState);

        return !empty($winnerIn);
    }

    /**
     * Try to find winner combinations
     *
     * @param array $boardState
     *
     * @return bool
     */
    public function getWinnerSymbolIfPresent($boardState)
    {
        // check rows
        $winnerIn = $this->isWinnerCombinationInRowSet($boardState);

        // check columns
        $columnList = [];
        foreach ($boardState as $key => $row) {
            $columnList[] = array_column($boardState, $key);
        }
        $winnerIn = empty($winnerIn) ? $this->isWinnerCombinationInRowSet($columnList) : $winnerIn;

        // left -> right diagonal vectors
        $winnerIn = empty($winnerIn) ? $this->isWinnerCombinationInDiagonal($boardState) : $winnerIn;

        // right -> left diagonal vectors
        // Flip matrix for always move from left to right
        $reverseArray = $boardState;
        foreach ($reverseArray as $key => $row) {
            $reverseArray[$key] = array_reverse($row);
        }
        $winnerIn = empty($winnerIn) ? $this->isWinnerCombinationInDiagonal($reverseArray) : $winnerIn;

        return $winnerIn;
    }
}
