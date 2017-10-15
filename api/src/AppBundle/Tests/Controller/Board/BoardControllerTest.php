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
        $client = $this->apiRequest(Request::METHOD_POST, $this->getMoveUrl(), $this->generateEmptyRequestBoard());
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

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
    public function testEmptyNextMoveRequest()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_POST, $this->getMoveUrl());

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        $error = $this->assertAndParseApiErrorResponse($client);
        $this->assertContains(AppBundleException::NO_CONTENT, $error->detail);
    }

}
