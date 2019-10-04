<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Service;

use WoohooLabs\Zen\Examples\Utils\PlantUtil;

class PlantService implements PlantServiceInterface
{
    private PlantUtil $util;
    /** @var string */
    private $plantType;

    public function __construct(PlantUtil $util, string $plantType)
    {
        $this->util = $util;
        $this->plantType = $plantType;
    }
}
