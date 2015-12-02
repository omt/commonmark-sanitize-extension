<?php

namespace OneMoreThing\CommonMark\Sanitize\Nodes;

use League\CommonMark\Inline\Element\Html;

class ClosingTag extends Html
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
        return '</' . $this->getTagName() . '>';
    }
}
