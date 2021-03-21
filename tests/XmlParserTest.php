<?php

namespace MediaStoreNet\XmlParser\Test;

use MediaStoreNet\XmlParser\XmlParser;
use PHPUnit\Framework\TestCase;

class XmlParserTest extends TestCase
{
    public $xmlParser;

    public function setUp(): void
    {
        parent::setUp();
        $this->xmlParser = new XmlParser(__DIR__, '');
    }

    public function testBaseFilesExists(): void
    {
        self::assertFileExists(
            dirname(__DIR__) . '/src/XmlParser.php',
            'expected the file XmlParser.php should be exist'
        );
    }

    public function testInstanceOfXmlParser(): void
    {
        self::assertInstanceOf(XmlParser::class, $this->xmlParser, 'expected a Instance of XmlParser');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->xmlParser);
    }
}
