<?php

namespace AppBundle\TicTacToe\Factory;

use AppBundle\TicTacToe\Strategy\{
    MoveInterface,
    Vendor\Simple
};

class Strategy
{
    private $vendorConfig;

    public function __construct($vendorConfig)
    {
        if (empty($vendorConfig['vendor'])) {
            throw new StrategyException(StrategyException::INVALID_VENDOR);
        }

        $this->vendorConfig = $vendorConfig;
    }

    public function getStrategy() : MoveInterface
    {
        $vendor      = null;
        $vendorName  = $this->vendorConfig['vendor'];
        switch($vendorName) {
            case 'simple':
                $vendor = new Simple($this->vendorConfig[$vendorName]);
                break;
            default:
                new StrategyException(sprintf(StrategyException::NOT_FOUND, $this->vendorConfig->vendor));
                break;
        }
        return $vendor;
    }
}
