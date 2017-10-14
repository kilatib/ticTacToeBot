<?php

namespace AppBundle\Model\Board;

use JMS\Serializer\Annotation as Serializer;

class Field implements FieldInterface {
    /**
     * @Serializer\Type("integer")
     * @Serializer\Groups({"api"})
     */
    protected $x;

    /**
     * @Serializer\Type("integer")
     * @Serializer\Groups({"api"})
     */
    protected $y;

    /**
     * @Serializer\Type("string")
     * @Serializer\Groups({"api"})
     */
    protected $value;

    private $availableSymbols = ['X', 'O'];

    public function setX ($x)
    {
        $this->x = $x;

        return $this->x;
    }

    public function setY ($y)
    {
        $this->y = $y;

        return $this->y;
    }

    public function getX(): int
    {
        return (int)$this->x;
    }

    public function getY(): int
    {
        return (int)$this->y;
    }

    public function getValue(): string
    {
        return (string)$this->value;
    }

    public function populate($x, $y, $value): FieldInterface
    {
        $this->setX($x);
        $this->setY($y);
        $this->setValue($value);

        return $this;
    }

    public function setValue ($value)
    {
       if (!empty($value) && !in_array($value, $this->availableSymbols)) {
           throw new FieldException(FieldException::INVALID_SYMBOL);
       }

       $this->value = $value;
       return $this->value ;
    }

    public function isEmpty() : bool
    {
        return empty($this->value);
    }

    public function isValid() : bool
    {
        return !isset($this->x) && !isset($this->y);
    }
}