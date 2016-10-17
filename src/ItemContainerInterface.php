<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone;

interface ItemContainerInterface
{
    /**
     * @return bool
     */
    public function hasItem(string $id): bool;

    /**
     * @return mixed
     */
    public function getItem(string $id);
}
