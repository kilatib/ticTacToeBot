<?php

namespace AppBundle\Tests\Controller\Board;

use AppBundle\Model\Board\{
    FieldInterface
};

use AppBundle\AppBundleException;

class BoardControllerTest extends AbstractControllerTest
{
    /**
     * Test start game rules
     */
    public function testGetSymbolsAction()
    {
        $client    = $this->getClient();
        $symbolUrl = $client->getContainer()->get('router')->generate('symbols');

        $client->request('GET', $symbolUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent());

        // only 2 symbols allowed
        $this->assertArrayNotHasKey(2, $content);

        // check that server set allowed symbols
        $this->assertTrue(in_array(FieldInterface::PRIMARY_PLAYER_SYMBOL, $content));
        $this->assertTrue(in_array(FieldInterface::SECONDARY_PLAYER_SYMBOL, $content));
    }

    /**
     *
     *
     * X X X
     * X X X
     * X X X
     */
    public function testPostMakeMoveAction()
    {
        $client = $this->getClient();
        $boardSymfonyRequest = $this->generateEmptyRequestBoard();

        $client->request('POST', $this->getMoveUrl(), ['test' => '']);

        print_r($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //
        // test correct response
        $response = $client->getResponse()->getContent();
        $respondField = json_decode($response);

        $this->assertObjectHasAttribute('x',     $respondField);
        $this->assertObjectHasAttribute('y',     $respondField);
        $this->assertObjectHasAttribute('value', $respondField);
    }

    /**
     *  No data
     */
    public function testEmptySymfonyRequest()
    {
        $client = $this->getClient();
        $client->request('POST', $this->getMoveUrl());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $error = $this->assertAndParseApiErrorResponse($client);
        $this->assertContains(AppBundleException::NO_CONTENT, $error->detail);
    }

}
