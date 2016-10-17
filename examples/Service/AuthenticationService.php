<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Service;

use WoohooLabs\Dicone\Examples\Utils\UserUtil;

class AuthenticationService
{
    /**
     * @var UserUtil
     */
    private $util;

    public function __construct(UserUtil $util)
    {
        $this->util = $util;
    }
}
