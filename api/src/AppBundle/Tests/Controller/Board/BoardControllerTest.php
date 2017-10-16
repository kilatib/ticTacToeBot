<?php

namespace AppBundle\Tests\Controller\Board;

use AppBundle\Model\Board\{
    FieldInterface
};

use AppBundle\AppBundleException;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

/***
 * Class BoardControllerTest
 *
 * @package AppBundle\Tests\Controller\Board
 */
class BoardControllerTest extends AbstractControllerTest
{
    /**
     * Test start game rules
     */
    public function testGetSymbolsAction()
    {
        $client    = $this->getClient();
        $symbolUrl = $client->getContainer()->get('router')->generate('symbols');

        $client->request(Request::METHOD_GET, $symbolUrl);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent());

        // only 2 symbols allowed
        $this->assertArrayNotHasKey(2, $content);

        // check that server set allowed symbols
        $this->assertTrue(in_array(FieldInterface::PRIMARY_PLAYER_SYMBOL, $content));
        $this->assertTrue(in_array(FieldInterface::SECONDARY_PLAYER_SYMBOL, $content));
    }

    /**
     *
     * '' '' ''
     * '' '' ''
     * '' '' ''
     */
    public function testPostMakeMoveAction()
    {
        $this->requestAndAssertField( $this->generateEmptyRequestBoard());
    }

    /**
     *  No data
     */
    public function testEmptyNextMoveRequest()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_POST, $this->getMoveUrl());

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        $error = $this->assertAndParseApiErrorResponse($client);
        $this->assertContains(AppBundleException::NO_CONTENT, $error->detail);
    }

    /**
     *  Test communication if AI winner
     */
    public function testGameWinnerDetection()
    {
        $client         = $this->getClient();
        $gameWinnerLink = $client->getContainer()->get('router')->generate('game_winner');

        /**
         * O X X
         * - O -
         * - X -
         */
        $boardRequest = $this->generateEmptyRequestBoard();

        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][2]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = '';
        $boardRequest['board'][7]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][8]['value'] = FieldInterface::SECONDARY_PLAYER_SYMBOL;

        $client = $this->apiRequest(Request::METHOD_POST, $gameWinnerLink, $boardRequest);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $response = $client->getResponse()->getContent();
        $responseJson = json_decode($response);

        $this->assertObjectHasAttribute('winner', $responseJson);
        $this->assertEquals(FieldInterface::SECONDARY_PLAYER_SYMBOL, $responseJson->winner);

        // test empty winner

        $boardRequest = $this->generateEmptyRequestBoard();

        // vector: 1
        $boardRequest['board'][0]['value'] = FieldInterface::PRIMARY_PLAYER_SYMBOL;
        $boardRequest['board'][1]['value'] = '';
        $boardRequest['board'][2]['value'] = '';

        // vector: 2
        $boardRequest['board'][3]['value'] = '';
        $boardRequest['board'][4]['value'] = '';
        $boardRequest['board'][5]['value'] = '';

        // vector: 3
        $boardRequest['board'][6]['value'] = '';
        $boardRequest['board'][7]['value'] = '';
        $boardRequest['board'][8]['value'] = '';

        $client = $this->apiRequest(Request::METHOD_POST, $gameWinnerLink, $boardRequest);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $response = $client->getResponse()->getContent();
        $responseJson = json_decode($response);

        $this->assertObjectHasAttribute('winner', $responseJson);
        $this->assertEmpty($responseJson->winner, 'Winner not empty strange');
    }
}
