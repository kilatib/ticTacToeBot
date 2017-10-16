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

    /**
     * Possible symbols in field
     * @var array
     */
    private $availableSymbols = [FieldInterface::PRIMARY_PLAYER_SYMBOL, FieldInterface::SECONDARY_PLAYER_SYMBOL];

    /** setters and getters below will be called automatically Symfony form */
    /**
     * @param int $x
     */
    public function setX (int $x)
    {
        $this->x = $x ?? '0';
    }

    /**
     * @param int $y
     */
    public function setY (int $y)
    {
        $this->y = $y ?? '0';
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return (int)$this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return (int)$this->y;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return (string)$this->value;
    }

    /**
     * @param string $value
     * @return mixed
     * @throws FieldException
     */
    public function setValue ($value)
    {
        if (!empty($value) && !in_array($value, $this->availableSymbols)) {
            throw new FieldException(FieldException::INVALID_SYMBOL, 400);
        }

        $this->value = $value;
        return $this->value ;
    }

    /**
     * Create normal field from simple structure
     *      ...$data
     *
     * @param int    $x
     * @param int    $y
     * @param string $value
     * @return FieldInterface
     */
    public function populate($x, $y, $value): FieldInterface
    {
        $this->setX($x);
        $this->setY($y);
        $this->setValue($value);

        return $this;
    }

    /**
     * May show us is field free for step
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->value);
    }

    /**
     * Validate field
     *
     * @return bool
     */
    public function isValid() : bool
    {
        // not sure field can validate self
        // PHP 7.0 has bug when some how 0 interpolate as null
        return true;
    }

    /**
     * Convert to simple structure
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->getX(), $this->getY(), $this->getValue()];
    }
}