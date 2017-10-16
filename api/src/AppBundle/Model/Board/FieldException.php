<?php

namespace AppBundle\Model\Board;
use AppBundle\AppBundleException;

/**
 * Class FieldException
 *
 * Help to specify errors
 *
 * @package AppBundle\Model\Board
 */
class FieldException extends AppBundleException {
    const INVALID_SYMBOL = 'INVALID SYMBOL';
}