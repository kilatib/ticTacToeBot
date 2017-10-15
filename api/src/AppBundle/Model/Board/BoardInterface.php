<?php

namespace AppBundle\Model\Board;

interface BoardInterface {
    public function toArray(): array;
    public function setField(FieldInterface $field);
    public function nextUnit(): string;
    public function isFull()  : bool;
}