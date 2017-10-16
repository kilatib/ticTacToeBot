<?php

namespace AppBundle\Model\Board;

use PHPUnit\Runner\Exception;

use Symfony\Component\HttpFoundation\Response;


class Board implements BoardInterface {
    /**
     * @Serializer\Type("Field[]")
     * @Serializer\Groups({"api"})
     *
     * @var FieldInterface[]
     */
    protected $fieldList    = [];
    private $unitAmountList = [];

    /**
     * Add field to point vector
     *
     * @param FieldInterface $field
     * @return array
     */
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

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function applyFieldModel (FieldInterface $field)
    {
        foreach ($this->fieldList as $key => $fieldModel) {
            if (
                $fieldModel->getX() == $field->getX()
                && $fieldModel->getY() == $field->getY()
            ) {
                $this->fieldList[$key] = $field;
                break;
            }
        }

        return $this;
    }

    /**
     * Create to capability: transferring data to \AppBundle\TicTacToe\Strategy\MoveInterface
     *
     * @return array
     */
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

    /**
     * Tic Tac Toe mean game field is square
     *
     * @return bool
     */
    public function isBoardSquare(): bool
    {
        $fieldAmount = count($this->fieldList);
        $sqrt = sqrt($fieldAmount);

        return $fieldAmount  > 0           // matrix contain data
               && $sqrt === floor($sqrt)   // matrix square
        ;
    }

    /**
     * If client done not well we probably
     * have possibility have tow sequence step from it
     *  Added to Unit test
     *
     * @return bool
     */
    public function isStepSequenceBroken(): bool
    {
        $flag = false;
        if (!empty($this->unitAmountList)) {
            $minAmount = min(array_values($this->unitAmountList));
            $maxAmount = max(array_values($this->unitAmountList));

            $flag = ( count($this->unitAmountList) < 2 && $maxAmount > 1)
                    || ($maxAmount - $minAmount) > 1;
        }

        return $flag;
    }

    /**
     * I believe user can choice symbols for play which he loves
     *
     * @return string
     */
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

    /**
     * Test model
     *
     * @throws BoardException
     */
    public function validate()
    {
        // check board size
        if (false === $this->isBoardSquare()) {
            throw new BoardException(BoardException::BOARD_SIZE, Response::HTTP_BAD_REQUEST);
        }

        if ($this->isFull()) {
            throw new BoardException(BoardException::GAME_OVER, Response::HTTP_MISDIRECTED_REQUEST);
        }

        if ($this->isStepSequenceBroken()) {
            throw new BoardException(BoardException::STEP_SEQUENCE, Response::HTTP_MISDIRECTED_REQUEST);
        }

    }
}