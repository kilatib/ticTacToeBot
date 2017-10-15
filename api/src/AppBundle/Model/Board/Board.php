<?php

namespace AppBundle\Model\Board;

use PHPUnit\Runner\Exception;

class Board implements BoardInterface {
    /**
     * @Serializer\Type("Field[]")
     * @Serializer\Groups({"api"})
     *
     * @var FieldInterface[]
     */
    protected $fieldList    = [];
    private $unitAmountList = [];

    public function setField(FieldInterface $field) : array
    {
        if ($field->isValid()) {
            $this->fieldList[] = $field;

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

    /**
     * If board full then game was finished
     *
     * @return bool
     */
    public function isFull () : bool
    {
        $totalSymbolsAmount = array_sum($this->unitAmountList);
        return $totalSymbolsAmount >= count($this->fieldList);
    }

    public function isBoardSquare(): bool
    {
        $fieldAmount = count($this->fieldList);
        $sqrt = sqrt($fieldAmount);

        return $fieldAmount  > 0           // matrix contain data
               && $sqrt === floor($sqrt)   // matrix square
        ;
    }

    public function nextUnit(): string
    {
        $symbol = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        if (!empty($this->unitAmountList)) {
            if (count($this->unitAmountList) > 1) {
                $minAmount = min(array_values($this->unitAmountList));
                $symbol    = (string)array_flip($this->unitAmountList)[$minAmount];
            } else {
                $symbol = isset($this->unitAmountList[FieldInterface::PRIMARY_PLAYER_SYMBOL])
                    ? FieldInterface::SECONDARY_PLAYER_SYMBOL
                    : FieldInterface::PRIMARY_PLAYER_SYMBOL;
            }
        }

        return $symbol;
    }

    public function validate()
    {
        // check board size
        if (false === $this->isBoardSquare()) {
            throw new BoardException(BoardException::BOARD_SIZE);
        }

        if ($this->isFull()) {
            throw new BoardException(BoardException::GAME_OVER);
        }


    }
}