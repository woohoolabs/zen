<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Attribute\Inject;
use WoohooLabs\Zen\Examples\Service\PlantServiceInterface;
use WoohooLabs\Zen\Examples\Utils\PlantUtil;
use WoohooLabs\Zen\Examples\View\PlantView;

class PlantController extends AbstractController
{
    #[Inject]
    private PlantUtil $util;

    #[Inject]
    private PlantServiceInterface $service;

    #[Inject]
    private PlantView $view;
}
