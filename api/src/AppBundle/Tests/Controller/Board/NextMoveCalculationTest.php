<?php

namespace AppBundle\Tests\Controller\Board;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

use AppBundle\Model\Board\{
    FieldInterface,
    BoardException
};

use AppBundle\TicTacToe\Strategy\StrategyException;

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
        $boardRequest = $this->generateEmptyRequestBoard();
        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);

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
        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);

        $respondField = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('x',     $respondField);
        $this->assertObjectHasAttribute('y',     $respondField);
        $this->assertObjectHasAttribute('value', $respondField);

        $this->assertContains(FieldInterface::SECONDARY_PLAYER_SYMBOL, $respondField->value);
    }


    /**
     *
     * X X X
     * X X X
     * X X X
     */
    public function testFullBoard()
    {
        $randomBoard = $this->generateRandomBoard(false);

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $randomBoard);
        $error  = $this->assertAndParseApiErrorResponse($client, Response::HTTP_MISDIRECTED_REQUEST);

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
        $boardRequest = $this->generateRequestBoard(FieldInterface::PRIMARY_PLAYER_SYMBOL);

        unset($boardRequest['board'][2]);

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);

        $error = $this->assertAndParseApiErrorResponse($client);
        $this->assertContains(BoardException::BOARD_SIZE, $error->detail);
    }

    /**
     * Test cheats
     *
     * X X X
     * - - -
     * - - -
     * <----->
     * X X 0
     * X - -
     * - - -
     */
    public function testStepSequence()
    {
        $boardRequest = $this->generateEmptyRequestBoard();

        //
        // If range of symbols pure
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);

        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_MISDIRECTED_REQUEST);
        $this->assertContains(BoardException::STEP_SEQUENCE, $error->detail);

        //
        // If range of symbol normal avoid winner combination
        $boardRequest['board'][2]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_MISDIRECTED_REQUEST);
        $this->assertContains(BoardException::STEP_SEQUENCE, $error->detail);
    }


    /**
     * Winner combination is present.
     *   No need to move
     *
     * X X X
     * 0 0 -
     * - - -
     * <- - ->
     * X X O
     * O X -
     * O - X
     * <--->
     * X O O
     * X O -
     * X X -
     */
    public function testWinnerCombinationPresent()
    {
        //
        // X X X
        // 0 0 -
        // - - -
        //
        // First line vector
        $boardRequest = $this->generateEmptyRequestBoard();
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // second line vector
        $boardRequest['board'][3]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_UNAUTHORIZED);
        $this->assertContains(StrategyException::WINNER_COMBINATION_PRESENT, $error->detail);

        /**
         * X O O
         * X O -
         * X X -
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][8]['value'] = '';

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_UNAUTHORIZED);
        $this->assertContains(StrategyException::WINNER_COMBINATION_PRESENT, $error->detail);

        /**
         * X X O
         * O X -
         * O - X
        */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_UNAUTHORIZED);
        $this->assertContains(StrategyException::WINNER_COMBINATION_PRESENT, $error->detail);

        /**
         * X X O
         * O O -
         * O - X
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_UNAUTHORIZED);
        $this->assertContains(StrategyException::WINNER_COMBINATION_PRESENT, $error->detail);
    }

    /**
     * Set unreal combination
     *
     * X X X
     * 0 0 O
     * - - -
     * <- - ->
     * X - O
     * X - O
     * X - O
     * <--->
     * O X X
     * O O O
     * X - O
     */
    public function testUnrealCombination ()
    {
        /**
         * X X X
         * O O O
         * - - -
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        // vector: 3
        $boardRequest['board'][6]['value'] = '';
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = '';

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_MISDIRECTED_REQUEST);
        $this->assertContains(BoardException::INVALID_BOARD, $error->detail);

        /**
         * X - O
         * X - O
         * X - O
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = '';
        $boardRequest['board'][2]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = '';
        $boardRequest['board'][5]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_MISDIRECTED_REQUEST);
        $this->assertContains(BoardException::INVALID_BOARD, $error->detail);

        /**
         * X X X
         * - - -
         * O O O
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = '';
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][8]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);
        $error = $this->assertAndParseApiErrorResponse($client, Response::HTTP_MISDIRECTED_REQUEST);
        $this->assertContains(BoardException::INVALID_BOARD, $error->detail);
    }
}
