<?php

namespace AppBundle\Model\Request;

use AppBundle\Model\Board\{
    BoardInterface,
    Board  as BoardModel
};

/**
 * Class Board
 *
 * Simple way to get boardModel from response by form
 *    Create isolation layer between response and entities
 *
 * @package AppBundle\Model\Request
 */
class Board
{
    /** @var BoardModel */
    private $board;

    /**
     * Set parsed collection data
     *      convert collection to board entity s
     *
     * @param $collection
     */
    public function setBoard($collection)
    {
        $this->board = new BoardModel();
        foreach ($collection as $fieldModel) {
            $this->board->setField($fieldModel);
        }
    }

    /**
     * Return parsed request as data layer
     *
     * @return BoardModel
     */
    public function getBoard()
    {
        return $this->board ;
    }

    /**
     * Service method for symfony forms
     *      check is request set
     *
     * @return bool
     */
    public function hasBoard()
    {
        return isset($this->board);
    }

    /**
     * Service method for symfony forms
     *      check is request set
     *
     * @return bool
     */
    public function isBoard()
    {
        return ($this->board instanceof BoardInterface);
    }
}