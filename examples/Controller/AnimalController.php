<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Controller;

use WoohooLabs\Dicone\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Dicone\Examples\Utils\AnimalUtil;

class AnimalController
{
    /**
     * @Inject
     * @var AnimalUtil
     */
    private $util;

    /**
     * @Inject
     * @var AnimalServiceInterface
     */
    private $service;
}
