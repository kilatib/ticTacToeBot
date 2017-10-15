<?php

namespace AppBundle\Tests\Controller\Board;

use AppBundle\Model\Board\{
    FieldInterface,
    BoardException
};

class NextMoveCalculationTest extends AbstractControllerTest
{

    /**
     * Create Board 3 x 3 and send it
     *
     * ''  X ''
     * '' '' ''
     * '' '' ''
     * --------
     * ''  O ''
     * '' '' ''
     * '' '' ''
     */
    public function tesNextMoveAction()
    {
        $client = $this->getClient();
        $boardRequest = $this->generateEmptyRequestBoard();

        $client->request('GET', $this->getMoveUrl(), $boardRequest);

        //
        // test correct response
        $respondField = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('x',     $respondField);
        $this->assertObjectHasAttribute('y',     $respondField);
        $this->assertObjectHasAttribute('value', $respondField);


        // test right symbol choice
        // If board was empty then AI make a first step
        $this->assertContains(FieldInterface::PRIMARY_PLAYER_SYMBOL, $respondField->value);

        // make one more request
        // but add first symbol AI should take another
        $boardRequest[1]->value = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $respondField = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('x',     $respondField);
        $this->assertObjectHasAttribute('y',     $respondField);
        $this->assertObjectHasAttribute('value', $respondField);

        $this->assertContains(FieldInterface::SECONDARY_PLAYER_SYMBOL, $respondField->value);
    }


    /**
     *
     *
     * X X X
     * X X X
     * X X X
     */
    public function testFullBoard()
    {
        $client      = $this->getClient();
        $randomBoard = $this->generateRandomBoard(false);

        $client->request('POST', $this->getMoveUrl(), $randomBoard);

        $error = $this->assertAndParseApiErrorResponse($client);
        $this->assertContains(BoardException::GAME_OVER, $error->detail);
    }

    /**
     * Create Board 3 x 3 and send it
     *
     * X X
     * X X X
     * X X X
     */
    public function testMixedBorderSize()
    {
        $client       = $this->getClient();
        $boardRequest = $this->generateRequestBoard(FieldInterface::PRIMARY_PLAYER_SYMBOL);

        unset($boardRequest['board'][2]);

        $client->request('POST', $this->getMoveUrl(), $boardRequest);

        $error = $this->assertAndParseApiErrorResponse($client);
        $this->assertContains(BoardException::BOARD_SIZE, $error->detail);
    }
}
