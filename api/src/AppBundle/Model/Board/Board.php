<?php

namespace AppBundle\Model\Board;

class Board implements BoardInterface {
    /**
     * @Serializer\Type("Field[]")
     * @Serializer\Groups({"api"})
     *
     * @var FieldInterface[]
     */
    protected $fieldList = [];

    private $maxY = -1;
    private $maxX = -1;

    private $unitAmountList = [];

    public function setField(FieldInterface $field) : array
    {
        if ($field->isValid()) {
            $this->fieldList[] = $field;

            $this->maxY = ($field->getY() < $this->maxY ) ?? $field->getY();
            $this->maxX = ($field->getX() < $this->maxX ) ?? $field->getX();

            if (!$field->isEmpty()) {
                if (isset($this->unitAmountList[$field->getValue()])) {
                    $this->unitAmountList[$field->getValue()]++;
                } else {
                    $this->unitAmountList[$field->getValue()] = 1;
                }
            }
        }

        return $this->fieldList;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->fieldList as $field) {
            /** @var FieldInterface $field */
            if (isset($result[$field->getX()])) {
                $result[$field->getX()][$field->getY()] = $field->getValue();
            } else {
                $result[$field->getX()] = [$field->getY() => $field->getValue()];
            }
        }
        return $result;
    }

    public function isValid(): bool
    {
        return $this->maxX === $this->maxY;
    }

    public function nextUnit(): string
    {
        $minAmount = min($this->unitAmountList);
        return (string)array_reverse($this->unitAmountList)[$minAmount];
    }
}