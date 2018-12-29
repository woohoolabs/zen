<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Service;

use WoohooLabs\Zen\Examples\Utils\AnimalUtil;

class AnimalService2 extends AnimalService
{
    public function __construct(AnimalUtil $util)
    {
        parent::__construct($util);
    }
}
