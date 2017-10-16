<?php

namespace AppBundle\TicTacToe\Strategy;


use AppBundle\TicTacToe\Strategy\Vendor\{
    ZeroSum,
    Random
};

/**
 * Class Strategy
 *      Factory for create strategies according configs
 *         Pay attention right set place: api/app/config/parameters.yml
 *
 *
 * @package AppBundle\TicTacToe\Factory
 */
class StrategyFactory
{
    private $vendorConfig;

    /**
     * Strategy constructor.
     *
     * @param array $vendorConfig
     * @throws StrategyException
     */
    public function __construct($vendorConfig)
    {
        if (empty($vendorConfig['vendor'])) {
            throw new StrategyException(StrategyException::INVALID_VENDOR);
        }

        $this->vendorConfig = $vendorConfig;
    }

    /**
     * Build strategy classes according configs
     *      this method has one minus it build Classes inside it is not good solution,
     *      but set ServiceLocator here and build according string more worse solution
     *
     * @return MoveInterface
     */
    public function getStrategy() : MoveInterface
    {
        $vendor      = null;
        $vendorName  = $this->vendorConfig['vendor'];
        switch($vendorName) {
            case 'random':
                $vendor = new Random($this->vendorConfig[$vendorName]);
                break;
            case 'ZeroSum':
                $vendor = new ZeroSum($this->vendorConfig[$vendorName]);
                break;
            default:
                new StrategyException(sprintf(StrategyException::NOT_FOUND, $this->vendorConfig->vendor));
                break;
        }
        return $vendor;
    }
}
