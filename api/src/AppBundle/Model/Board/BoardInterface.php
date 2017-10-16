<?php

namespace AppBundle\Model\Board;

interface BoardInterface {
    /**
     * For easy conver to simple structure
     * @return array
     */
    public function toArray(): array;

    /**
     * apply new field to data vector
     *
     * @param FieldInterface $field
     * @return mixed
     */
    public function setField(FieldInterface $field);

    /**
     * Try to predict next unit
     *
     * @return string
     */
    public function nextUnit(): string;

    /**
     * Check possibility to do any steps on board
     *
     * @return bool
     */
    public function isFull()  : bool;
}