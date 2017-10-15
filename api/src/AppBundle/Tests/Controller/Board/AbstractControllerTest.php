<?php
namespace AppBundle\Tests\Controller\Board;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Model\Board\FieldInterface;

abstract class AbstractControllerTest extends WebTestCase
{
    const BOARD_SIZE = 3;

    /**
     * Generate game board
     *
     * @param  null|''|'X'|'O' $symbol
     * @param  null|array      $valueList
     * @return array
     */
    private function generateBoard($symbol = null, $valueList = null)
    {
        $valueList = $valueList ?? [FieldInterface::PRIMARY_PLAYER_SYMBOL, FieldInterface::SECONDARY_PLAYER_SYMBOL];

        $boardRequest = [];
        for ($x = 0; $x < self::BOARD_SIZE; $x++) {
            for($y = 0; $y < self::BOARD_SIZE; $y++) {

                shuffle($valueList);
                $boardRequest[] = $this->generateRequestField($symbol ?? $valueList[0], $x, $y);
            }
        }

        return $boardRequest;
    }

    /**
     * Generate field js model
     *
     * @param null|int    $x
     * @param null|int    $y
     * @param null|string $symbol
     *
     * @return \stdClass
     */
    private function generateField($symbol, $x = null, $y = null)
    {
        $field    = new \stdClass();
        $field->x = is_null($x) ? rand(0, self::BOARD_SIZE) : $x;
        $field->y = is_null($y) ? rand(0, self::BOARD_SIZE) : $y;
        $field->value = $symbol;

        return $field;
    }

    /**
     * Generate request field
     *
     * @param int    $x
     * @param int    $y
     * @param string $symbol
     * @return array
     */
    protected function generateRequestField ($symbol, $x, $y)
    {
        return (array) $this->generateField($symbol, $x, $y);
    }

    /**
     * Generate game board and fill with given symbol
     *
     * @param  string $symbol
     * @return array
     */
    protected function generateRequestBoard($symbol)
    {
        return [
            'board' => $this->generateBoard($symbol)
        ];
    }

    /**
     * Generate game board and fill with given symbol
     *
     * @param bool $withEmptyValue
     *
     * @return array
     */
    protected function generateRandomBoard($withEmptyValue = false)
    {
        $valueList = [FieldInterface::PRIMARY_PLAYER_SYMBOL, FieldInterface::SECONDARY_PLAYER_SYMBOL];
        if ($withEmptyValue) {
            $valueList[] = '';
        }

        return [
            'board' => $this->generateBoard(null, $valueList)
        ];
    }

    /**
     * Create empty board
     *
     * @return array
     */
    protected function generateEmptyRequestBoard()
    {
        return [
            'board' => $this->generateBoard('')
        ];
    }

    /**
     * Common checker for API Errors in response
     *
     * @param  $client
     * @return \stdClass
     */
    protected function assertAndParseApiErrorResponse($client)
    {
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $errorList = json_decode($client->getResponse()->getContent());
        $this->assertNotEmpty($errorList);

        $errorResponse  = reset($errorList);

        $this->assertObjectHasAttribute('code', $errorResponse);
        $this->assertObjectHasAttribute('detail', $errorResponse);

        return $errorResponse;
    }

    protected function getClient()
    {
        $client = self::createClient();

        return $client;
    }

    protected function getMoveUrl()
    {
        return $this->getClient()->getContainer()->get('router')->generate('make_move');
    }
}
