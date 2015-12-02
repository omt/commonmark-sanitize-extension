<?php

namespace OneMoreThing\CommonMark\Sanitize\Tests\Unit;

use League\CommonMark\Inline\Element\Html;
use OneMoreThing\CommonMark\Sanitize\HtmlParser;
use OneMoreThing\CommonMark\Sanitize\Nodes\CdataSection;
use OneMoreThing\CommonMark\Sanitize\Nodes\ClosingTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\Comment;
use OneMoreThing\CommonMark\Sanitize\Nodes\Declaration;
use OneMoreThing\CommonMark\Sanitize\Nodes\OpenTag;
use OneMoreThing\CommonMark\Sanitize\Nodes\ProcessingInstruction;

class HtmlParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var HtmlParser */
    private $parser;

    protected function setUp()
    {
        $this->parser = new HtmlParser();
    }

    public function testParseOpenTag()
    {
        $input = new Html('<h2>');
        $actual = $this->parser->parseHtml($input);

        $this->assertInstanceOf(OpenTag::class, $actual);
        $this->assertEquals('h2', $actual->getTagName());
        $this->assertEquals('<h2>', $actual->getContent());
    }

    public function testParseClosingTag()
    {
        $input = new Html('</h2>');
        $actual = $this->parser->parseHtml($input);

        $this->assertInstanceOf(ClosingTag::class, $actual);
        $this->assertEquals('h2', $actual->getTagName());
        $this->assertEquals('</h2>', $actual->getContent());
    }

    public function testParseComment()
    {
        $input = new Html('<!-- foo--->');
        $actual = $this->parser->parseHtml($input);

        $this->assertInstanceOf(Comment::class, $actual);
        $this->assertEquals('<!-- foo--->', $actual->getContent());
    }

    public function testParseProcessingInstruction()
    {
        $input = new Html('<?php echo $a; ?>');
        $actual = $this->parser->parseHtml($input);

        $this->assertInstanceOf(ProcessingInstruction::class, $actual);
        $this->assertEquals('<?php echo $a; ?>', $actual->getContent());
    }

    public function testParseDeclaration()
    {
        $input = new Html('<!ELEMENT br EMPTY>');
        $actual = $this->parser->parseHtml($input);

        $this->assertInstanceOf(Declaration::class, $actual);
        $this->assertEquals('<!ELEMENT br EMPTY>', $actual->getContent());
    }

    public function testParseCdataSection()
    {
        $input = new Html('<![CDATA[>&<]]>');
        $actual = $this->parser->parseHtml($input);

        $this->assertInstanceOf(CdataSection::class, $actual);
        $this->assertEquals('<![CDATA[>&<]]>', $actual->getContent());
    }
}
