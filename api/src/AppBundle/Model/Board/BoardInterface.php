<?php

namespace AppBundle\Model\Board;

use AppBundle\Model\Board\Filed;

interface BoardInterface {
    public function toArray(): array;
    public function setField(FieldInterface $field);
    public function nextUnit(): string;
    public function isValid() : bool;
}