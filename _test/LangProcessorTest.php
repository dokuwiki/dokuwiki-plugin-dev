<?php

namespace dokuwiki\plugin\dev\test;

use dokuwiki\plugin\dev\LangProcessor;
use DokuWikiTest;

/**
 * FIXME tests for the dev plugin
 *
 * @group plugin_dev
 * @group plugins
 */
class LangProcessorTest extends DokuWikiTest
{

    public function testPhpExtract()
    {
        $pl = new LangProcessor(new NullLogger());

        $file = __DIR__ . '/testdata/test.php';
        $result = $pl->phpExtract($file);

        $this->assertEquals([
            'string 1' => "$file:4",
            'string 2' => "$file:4",
            'string 3' => "$file:6",
        ], $result);
    }

    public function testJsExtract()
    {
        $pl = new LangProcessor(new NullLogger());

        $file = __DIR__ . '/testdata/test.js';
        $result = $pl->jsExtract($file);

        $this->assertEquals([
            'string1' => "$file:1",
            'string 2' => "$file:1",
            'string 3' => "$file:3",
            'string4' => "$file:5",
            'string 5' => "$file:9",
        ], $result);
    }

}

