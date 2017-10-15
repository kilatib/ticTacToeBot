<?php

namespace AppBundle\Model\Request;

use AppBundle\Model\Board\{
    BoardInterface,
    Board  as BoardModel
};

class Board
{
    /** @var BoardModel */
    private $board;

    public function setBoard($collection)
    {
        $this->board = new BoardModel();
        foreach ($collection as $fieldModel) {
            $this->board->setField($fieldModel);
        }
    }

    public function getBoard()
    {
        return $this->board ;
    }


    public function hasBoard()
    {
        return isset($this->board);
    }

    public function isBoard()
    {
        return ($this->board instanceof BoardInterface);
    }
}