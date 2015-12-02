<?php

namespace OneMoreThing\CommonMark\Sanitize\Tests\Unit;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\HtmlBlock;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Inline\Element\Html;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use OneMoreThing\CommonMark\Sanitize\Nodes\CdataSection;
use OneMoreThing\CommonMark\Sanitize\Nodes\ClosingTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\Comment;
use OneMoreThing\CommonMark\Sanitize\Nodes\Declaration;
use OneMoreThing\CommonMark\Sanitize\Nodes\OpenTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\ProcessingInstruction;
use OneMoreThing\CommonMark\Sanitize\SanitizationProcessor;

class SanitizationProcessorTest extends \PHPUnit_Framework_TestCase
{
    /** @var SanitizationProcessor */
    private $processor;

    protected function setUp()
    {
        $this->processor = new SanitizationProcessor();
    }

    public function testNodeConversion()
    {
        $paragraph = new Paragraph();
        $paragraph->appendChild(new Html('<h2>'));
        $paragraph->appendChild(new Text('Overview'));
        $paragraph->appendChild(new Html('</h2>'));
        $paragraph->appendChild(new Text(' '));
        $paragraph->appendChild(new Html('<!-- foo--->'));
        $paragraph->appendChild(new Text(' '));
        $paragraph->appendChild(new Html('<?php echo $a; ?>'));
        $paragraph->appendChild(new Text(' '));
        $paragraph->appendChild(new Html('<!ELEMENT br EMPTY>'));
        $paragraph->appendChild(new Text(' '));
        $paragraph->appendChild(new Html('<![CDATA[>&<]]>'));

        $document = new Document();
        $document->appendChild($paragraph);

        $this->processor->convertHtmlNodes($document);

        $walker = new NodeWalker($document);
        /** @var NodeWalkerEvent[] $steps */
        $steps = [];
        while ($step = $walker->next()) {
            $steps[] = $step;
        }

        $expected = [
            Document::class,
            Paragraph::class,
            OpenTag::class,
            Text::class,
            ClosingTag::class,
            Text::class,
            Comment::class,
            Text::class,
            ProcessingInstruction::class,
            Text::class,
            Declaration::class,
            Text::class,
            CdataSection::class,
            Paragraph::class,
            Document::class
        ];

        foreach ($expected as $i => $type) {
            $this->assertInstanceOf($type, $steps[$i]->getNode());
        }
    }

    public function testRemovesHtmlBlocks()
    {
        $document = new Document();
        $document->appendChild(new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT));
        $document->appendChild(new Paragraph());
        $document->appendChild(new HtmlBlock(HtmlBlock::TYPE_1_CODE_CONTAINER));

        $this->processor->processDocument($document);

        $walker = new NodeWalker($document);
        /** @var NodeWalkerEvent[] $steps */
        $steps = [];
        while ($step = $walker->next()) {
            $steps[] = $step;
        }

        $expected = [
            Document::class,
            Paragraph::class,
            Paragraph::class,
            Document::class
        ];

        foreach ($expected as $i => $type) {
            $this->assertInstanceOf($type, $steps[$i]->getNode());
        }
    }

}
