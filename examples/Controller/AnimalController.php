<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Examples\Domain\AnimalRepositoryInterface;
use WoohooLabs\Zen\Examples\Service\AnimalServiceInterface;

class AnimalController extends AbstractController
{
    #[Inject]
    private AnimalRepositoryInterface $repository;

    #[Inject]
    private AnimalServiceInterface $service;
}
