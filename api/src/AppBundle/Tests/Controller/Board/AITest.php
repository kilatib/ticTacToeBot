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

class AITest extends AbstractControllerTest
{

    private function requestAndAssertField ($boardRequest, $x, $y, $value)
    {
        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $boardRequest);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        //
        // test correct response
        $response = $client->getResponse()->getContent();
        $respondField = json_decode($response);

        $this->assertObjectHasAttribute('x',     $respondField);
        $this->assertObjectHasAttribute('y',     $respondField);
        $this->assertObjectHasAttribute('value', $respondField);

        $this->assertEquals($x, $respondField->x);
        $this->assertEquals($y, $respondField->y);
        $this->assertEquals($value, $respondField->value);
    }

    /**
     * Create Board 3 x 3 and send it
     *
     * O  O   X
     * '' X  ''
     * '' '' ''
     */
    public function testPreventWin ()
    {
        /**
         * O - X
         * - X -
         * - - -
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = '';
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = '';
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = '';

        $this->requestAndAssertField($boardRequest, 2, 0, FieldInterface::SECONDARY_PLAYER_SYMBOL);

        /**
         * X 0 -
         * - - -
         * X - -
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = '';

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = '';
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = '';

        $this->requestAndAssertField($boardRequest, 1, 0, FieldInterface::SECONDARY_PLAYER_SYMBOL);

        /**
         * X 0 -
         * - - -
         * - - X
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = '';

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = '';
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = '';
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        $this->requestAndAssertField($boardRequest, 1, 1, FieldInterface::SECONDARY_PLAYER_SYMBOL);

        /**
         * - 0 X
         * - X -
         * - - -
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = '';
        $boardRequest['board'][1]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = '';
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = '';

        $this->requestAndAssertField($boardRequest, 2, 0, FieldInterface::SECONDARY_PLAYER_SYMBOL);
    }

    /**
     * This test represent the situation when minSum of step be enough
     *  to prevent player win but in that time enough to be winner.
     *  Network with one perception should resolve this task
     *  But what about alpha-beta clipping or minmax strategy
     *
     * X O X
     * X O X
     * O - -
     */
    public function testHardChoice()
    {
        /**
         * X O X
         * X O X
         * O - -
         */
        $boardRequest = $this->generateEmptyRequestBoard();
        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][4]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 3
        $boardRequest['board'][6]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = '';

        $this->requestAndAssertField($boardRequest, 2, 1, FieldInterface::SECONDARY_PLAYER_SYMBOL);
    }
}
