<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Service;

use WoohooLabs\Dicone\Examples\Utils\PlantUtil;

class PlantService
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
