<?php

namespace OneMoreThing\CommonMark\Sanitize;

use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Html;
use League\CommonMark\Util\RegexHelper;
use OneMoreThing\CommonMark\Sanitize\Nodes\CdataSection;
use OneMoreThing\CommonMark\Sanitize\Nodes\ClosingTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\Comment;
use OneMoreThing\CommonMark\Sanitize\Nodes\Declaration;
use OneMoreThing\CommonMark\Sanitize\Nodes\OpenTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\ProcessingInstruction;

class HtmlParser
{
    /**
     * @param Html $html
     * @return Html
     */
    public function parseHtml(Html $html)
    {
        $content = $html->getContent();

        if (strpos($content, '<?') === 0) {
            return new ProcessingInstruction($content);
        }

        if (strpos($content, '<!--') === 0) {
            return new Comment($content);
        }

        if (strpos($content, '<![CDATA[') === 0) {
            return new CdataSection($content);
        }

        if (strpos($content, '<!') === 0) {
            return new Declaration($content);
        }

        $regexHelper = RegexHelper::getInstance();
        $tagNameRegex = $regexHelper->getPartialRegex(RegexHelper::TAGNAME);
        $attributeRegex = $regexHelper->getPartialRegex(RegexHelper::ATTRIBUTE);

        if (strpos($content, '</') === 0) {
            $regex = '/^<\/(' . $tagNameRegex . ')\s*[>]$/';
            preg_match($regex, $content, $matches);

            return new ClosingTag($matches[1]);
        }

        $regex = '/^<(' . $tagNameRegex . ')(' . $attributeRegex . '*' . ')\s*\/?>$/';
        preg_match($regex, $content, $matches);

        return new OpenTag($matches[1]);
    }
}
