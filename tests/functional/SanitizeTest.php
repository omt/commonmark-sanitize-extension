<?php

namespace OneMoreThing\CommonMark\Sanitize\Tests\Functional;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use OneMoreThing\CommonMark\Sanitize\SanitizationExtension;

class SanitizeTests extends \PHPUnit_Framework_TestCase
{
    /** @var SanitizationExtension */
    private $extension;

    /** @var DocParser */
    private $parser;

    /** @var HtmlRenderer */
    private $renderer;

    protected function setUp()
    {
        $environment = Environment::createCommonMarkEnvironment();

        $this->extension = new SanitizationExtension();
        $environment->addExtension($this->extension);

        $this->parser = new DocParser($environment);
        $this->renderer = new HtmlRenderer($environment);
    }

    public function testSimpleStrip()
    {
        $input = '<h2>Overview</h2>' ;
        $expected = '<p>Overview</p>';

        $this->assertConvertsTo($expected, $input);
    }

    private function assertConvertsTo($expected, $input)
    {
        $document = $this->parser->parse($input);

        $this->extension->getSanitizationProcessor()->processDocument($document);

        $html = $this->renderer->renderBlock($document);
        $html = trim($html);

        $this->assertEquals($expected, $html);
    }
}
