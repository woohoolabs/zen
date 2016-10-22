<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Examples\Service\UserService;
use WoohooLabs\Zen\Examples\View\UserView;

class UserController extends AbstractController
{
    /**
     * @Inject
     * @var UserService
     */
    private $service;

    /**
     * @Inject
     * @var UserView
     */
    private $view;
}
