<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Attribute\Inject;
use WoohooLabs\Zen\Examples\Service\UserService;
use WoohooLabs\Zen\Examples\View\UserView;

class UserController extends AbstractController
{
    #[Inject]
    private UserService $service;

    #[Inject]
    private UserView $view;
}
