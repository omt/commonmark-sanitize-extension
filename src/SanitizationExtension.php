<?php

/*
 * Original code based on the CommonMark PHP parser (https://github.com/thephpleague/commonmark/)
 *  - (c) Colin O'Dell
 */

namespace OneMoreThing\CommonMark\Sanitize;

use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Renderer\RawHtmlRenderer;
use OneMoreThing\CommonMark\Sanitize\Nodes\CdataSection;
use OneMoreThing\CommonMark\Sanitize\Nodes\ClosingTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\Comment;
use OneMoreThing\CommonMark\Sanitize\Nodes\Declaration;
use OneMoreThing\CommonMark\Sanitize\Nodes\OpenTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\ProcessingInstruction;

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

    public function getInlineRenderers()
    {
        $htmlRenderer = new RawHtmlRenderer();
        return [
            CdataSection::class => $htmlRenderer,
            ClosingTag::class => $htmlRenderer,
            Comment::class => $htmlRenderer,
            Declaration::class => $htmlRenderer,
            OpenTag::class => $htmlRenderer,
            ProcessingInstruction::class => $htmlRenderer,
        ];
    }

    /** @inheritdoc */
    public function getName()
    {
        return 'Sanitize';
    }

}


