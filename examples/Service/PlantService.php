<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Service;

use WoohooLabs\Zen\Examples\Utils\PlantUtil;

class PlantService implements PlantServiceInterface
{
    /**
     * @var PlantUtil
     */
    private $util;

    public function __construct(PlantUtil $util)
    {
        $this->util = $util;
    }
}
