<?php

namespace AppBundle\Model\Board;

use AppBundle\AppBundleException;

class BoardException extends AppBundleException {
    const INVALID_BOARD = 'Given board data invalid';
    const BOARD_SIZE    = 'Invalid board size';
    const STEP_SEQUENCE = 'Looks like some body did more move then it should';
    const GAME_OVER     = 'Game over!';
}