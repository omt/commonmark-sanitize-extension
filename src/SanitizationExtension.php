<?php

/*
 * Original code based on the CommonMark PHP parser (https://github.com/thephpleague/commonmark/)
 *  - (c) Colin O'Dell
 */

namespace OneMoreThing\CommonMark\Sanitize;

use League\CommonMark\Extension\Extension;

class SanitizationExtension extends Extension
{
    /** @var SanitizationProcessor */
    private $processor;

    public function __construct()
    {
        $this->processor = new SanitizationProcessor();
    }

    public function getSanitizationProcessor()
    {
        return $this->processor;
    }

    /** @inheritdoc */
    public function getName()
    {
        return 'Sanitize';
    }

}


