<?php

namespace Application\Success\MediaBundle\Generator;

class BasicGenerator
{

    protected $firstLevel;

    protected $secondLevel;

    /**
     * @param int $firstLevel
     * @param int $secondLevel
     */
    public function __construct($firstLevel = 100000, $secondLevel = 1000)
    {
        $this->firstLevel = $firstLevel;
        $this->secondLevel = $secondLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePath($id, $context)
    {
        $rep_first_level = (int) ($id / $this->firstLevel);
        $rep_second_level = (int) (($id - ($rep_first_level * $this->firstLevel)) / $this->secondLevel);

        return sprintf('%s/%04s/%02s', $context, $rep_first_level + 1, $rep_second_level + 1);
    }
}
