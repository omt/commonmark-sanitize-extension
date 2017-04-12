<?php

/*
 * Original code based on the CommonMark PHP parser (https://github.com/thephpleague/commonmark/)
 *  - (c) Colin O'Dell
 */

namespace OneMoreThing\CommonMark\Sanitize;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\HtmlBlock;
use League\CommonMark\Inline\Element\HtmlInline;
use League\CommonMark\Node\NodeWalker;
use OneMoreThing\CommonMark\Sanitize\Nodes\ClosingTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\OpenTag;

class SanitizationProcessor
{
    /** @var HtmlParser */
    private $htmlParser;

    private $whitelist = ['b', 'i', 'strong', 'em', 'ins', 'del', 'sup', 'sub', 'u'];

    public function processDocument(Document $document)
    {
        // First convert all html nodes to custom nodes.
        $this->convertHtmlNodes($document);

        $this->sanitizeDocument($document);
    }

    public function convertHtmlNodes(Document $document)
    {
        $walker = new NodeWalker($document);
        while ($step = $walker->next()) {
            if (!($step->getNode() instanceof HtmlInline) && !($step->getNode() instanceof HtmlBlock)) {
                continue;
            }

            $node = $step->getNode();

            if ($node instanceof HtmlBlock) {
                if ($node->next() !== null) {
                    $walker->resumeAt($node->next());
                } else {
                    $walker->resumeAt($node->parent(), false);
                }
                
                $node->detach();
                continue;
            }

            $replacement = $this->getHtmlParser()->parseHtml($node);
            $node->replaceWith($replacement);
        }
    }

    public function sanitizeDocument(Document $document)
    {
        $walker = new NodeWalker($document);
        while ($step = $walker->next()) {
            if (!($step->getNode() instanceof HtmlInline)) {
                continue;
            }

            $node = $step->getNode();
            if (!($node instanceof OpenTag) && !($node instanceof ClosingTag)) {
                // Remove all non-tag nodes
                $node->detach();
                continue;
            }

            if (!in_array(strtolower($node->getTagName()), $this->whitelist)) {
                // Remove all non-whitelisted nodes
                $node->detach();
                continue;
            }
        }
    }

    private function getHtmlParser()
    {
        if ($this->htmlParser === null) {
            $this->htmlParser = new HtmlParser();
        }

        return $this->htmlParser;
    }
}
