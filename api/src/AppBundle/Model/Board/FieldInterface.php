<?php

namespace AppBundle\Model\Board;

interface FieldInterface {
    const PRIMARY_PLAYER_SYMBOL = 'X';
    const SECONDARY_PLAYER_SYMBOL = 'O';

    /**
     * Getters bellow decelerate isolation layer for another entities
     */
    public function getX ()     : int;
    public function getY ()     : int;
    public function getValue () : string;


    public function isEmpty()   : bool;
    public function isValid()   : bool;

    /**
     * Obligate possibility convert to simple structure
     *
     * @param int    $x
     * @param int    $y
     * @param string $value
     *
     * @return FieldInterface
     */
    public function populate($x, $y, $value) : FieldInterface;
}