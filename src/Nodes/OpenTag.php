<?php

namespace OneMoreThing\CommonMark\Sanitize\Nodes;

use League\CommonMark\Inline\Element\HtmlInline;

class OpenTag extends HtmlInline
{

    private $tagName;

    public function __construct($tagName)
    {
        parent::__construct('');
        $this->tagName = $tagName;
    }

    public function getTagName()
    {
        return $this->tagName;
    }

    public function getContent()
    {
        $output = '<';
        $output .= $this->getTagName();
        $output .= '>';

        return $output;
    }
}
