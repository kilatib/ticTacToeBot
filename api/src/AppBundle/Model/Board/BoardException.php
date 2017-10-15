<?php

namespace AppBundle\Model\Board;

use AppBundle\AppBundleException;

class BoardException extends AppBundleException {
    const INVALID_BOARD = 'Given board data invalid';
    const BOARD_SIZE    = 'Invalid board size';
    const GAME_OVER     = 'Game over!';
    const GAME_DRAW     = 'Draw!';
}