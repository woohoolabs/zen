<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Service;

use WoohooLabs\Dicone\Examples\Utils\AnimalUtil;

class AnimalService implements AnimalServiceInterface
{
    /**
     * @var AnimalUtil
     */
    private $util;

    public function __construct(AnimalUtil $util)
    {
        $this->util = $util;
    }
}
