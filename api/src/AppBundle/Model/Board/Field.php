<?php

namespace AppBundle\Model\Board;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Finder\Iterator\FilterIterator;

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

    private $availableSymbols = [FieldInterface::PRIMARY_PLAYER_SYMBOL, FieldInterface::SECONDARY_PLAYER_SYMBOL];

    public function setX (int $x)
    {
        $this->x = $x ?? '0';
    }

    public function setY (int $y)
    {
        $this->y = $y ?? '0';
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

    public function setValue ($value)
    {
        if (!empty($value) && !in_array($value, $this->availableSymbols)) {
            throw new FieldException(FieldException::INVALID_SYMBOL, 400);
        }

        $this->value = $value;
        return $this->value ;
    }

    public function populate($x, $y, $value): FieldInterface
    {
        $this->setX($x);
        $this->setY($y);
        $this->setValue($value);

        return $this;
    }

    public function isEmpty() : bool
    {
        return empty($this->value);
    }

    public function isValid() : bool
    {
        // not sure field can validate self
        // PHP 7.0 has bug when some how 0 interpolate as null
        return true;
    }

    public function toArray()
    {
        return [$this->getX(), $this->getY(), $this->getValue()];
    }
}