<?php

namespace BasicTests;

use CommonTestClass;
use kalanis\kw_afterload\Afterload;
use kalanis\kw_afterload\AfterloadException;


class BasicTest extends CommonTestClass
{
    public function testRunningLoad(): void
    {
        Afterload1::run();
        $this->assertTrue(defined('SITE_NAME'));
        $this->assertTrue(defined('SITE_PREFIX'));
        $this->assertEquals('kwebcms', SITE_NAME);
        $this->assertEquals('kwc', SITE_PREFIX);
    }

    public function testDyingLoad(): void
    {
        $exMessages = [];
        try {
            Afterload2::run();
        } catch (AfterloadException $ex) {
            // more than one!
            do {
                $exMessages[] = $ex->getMessage();
            } while($ex = $ex->getPrev()); // NOT previous
        }

        // check constants
        $this->assertTrue(defined('X_SITE_NAME'));
        $this->assertTrue(defined('X_SITE_PREFIX'));
        $this->assertTrue(defined('MODULE_FOR_INPUT'));
        $this->assertFalse(defined('MODULE_FOR_OUTPUT'));
        $this->assertEquals('kwebcms', X_SITE_NAME);
        $this->assertEquals('kwc', X_SITE_PREFIX);
        $this->assertEquals('markdown', MODULE_FOR_INPUT);

        // now check messages - they comes in reversed order!
        $this->assertEquals('something else has died', reset($exMessages));
        $this->assertEquals('something has died', next($exMessages));
        $this->assertEmpty(next($exMessages));
    }

    public function testNoLoad(): void
    {
        Afterload3::run();
        $this->assertFalse(defined('MODULE_FOR_OUTPUT'));
    }
}


/**
 * Class Afterload1
 * @package BasicTests
 * Contains simple running code
 */
class Afterload1 extends Afterload
{
    protected function path(): array
    {
        return [__DIR__, '..', 'test_data_1'];
    }
}


/**
 * Class Afterload2
 * @package BasicTests
 * Contains error-throwing configs
 */
class Afterload2 extends Afterload
{
    protected function path(): array
    {
        return [__DIR__, '..', 'test_data_2'];
    }
}


/**
 * Class Afterload3
 * @package BasicTests
 * Contains path which does not exists
 */
class Afterload3 extends Afterload
{
    protected function path(): array
    {
        return [__DIR__, '..', 'test_data_3'];
    }
}