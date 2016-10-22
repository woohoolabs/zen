<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Examples\Service\PlantServiceInterface;
use WoohooLabs\Zen\Examples\Utils\PlantUtil;
use WoohooLabs\Zen\Examples\View\PlantView;

class PlantController
{
    /**
     * @Inject
     * @var PlantUtil
     */
    private $util;

    /**
     * @Inject
     * @var PlantServiceInterface
     */
    private $service;

    /**
     * @Inject
     * @var PlantView
     */
    private $view;
}
