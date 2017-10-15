<?php

namespace AppBundle\Model\Board;

interface FieldInterface {
    const PRIMARY_PLAYER_SYMBOL = 'X';
    const SECONDARY_PLAYER_SYMBOL = 'O';

    public function getX ()     : int;
    public function getY ()     : int;
    public function getValue () : string;

    public function isEmpty()   : bool;
    public function isValid()   : bool;
    public function populate($x, $y, $value) : FieldInterface;
}