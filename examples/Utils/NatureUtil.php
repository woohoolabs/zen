<?php

namespace WoohooLabs\Zen\Examples\Utils;

class NatureUtil
{
    /**
     * @var AnimalUtil
     */
    private $animalUtil;

    /**
     * @var PlantUtil
     */
    private $plantUtil;

    /**
     * @var bool
     */
    private $humansEnabled;

    /**
     * NatureUtil constructor.
     *
     * @param AnimalUtil $animalUtil
     * @param PlantUtil $plantUtil
     * @param bool $humansEnabled
     */
    public function __construct(AnimalUtil $animalUtil, PlantUtil $plantUtil, bool $humansEnabled)
    {
        $this->animalUtil = $animalUtil;
        $this->plantUtil = $plantUtil;
        $this->humansEnabled = $humansEnabled;
    }

}