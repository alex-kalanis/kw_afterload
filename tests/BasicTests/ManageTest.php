<?php

namespace BasicTests;

use CommonTestClass;
use kalanis\kw_afterload\AfterloadException;
use kalanis\kw_afterload\Manage\Files;


class ManageTest extends CommonTestClass
{
    public function tearDown(): void
    {
        $files = new FilesMock();
        foreach ($files->getFiles() as $item) {
            $files->removeInput($item->getName());
        }
        parent::tearDown();
    }

    /**
     * @throws AfterloadException
     */
    public function testManageSimple(): void
    {
        $files = new FilesMock();
        $this->assertEmpty($files->getFiles(), 'Something left from previous run');

        $files->addInput('pre', 'foo-bar-baz');
        $this->assertEquals(1, count($files->getFiles()), 'No too many files');

        $files->enable('pre');
        $files->enable('pre'); // do nothing
        $this->assertEquals('foo-bar-baz', $files->getInput('pre'));

        $files->updateInput('pre', 'abc-def-ghi');
        $files->disable('pre');
        $files->disable('pre'); // do nothing
        $this->assertEquals('abc-def-ghi', $files->getInput('pre'));

        $files->removeInput('pre');
        $this->assertEmpty($files->getFiles(), 'No too many files left');
    }

    /**
     * @throws AfterloadException
     */
    public function testManageMulti(): void
    {
        $files1 = new FilesMock();
        $this->assertEmpty($files1->getFiles(), 'Something left from previous run');

        $files1->addInput('pre', 'foo-bar-baz');
        $files1->addInput('duo', 'foo-baz-baz');
        $this->assertEquals(2, count($files1->getFiles()), 'No too many files');
        $this->assertEquals('foo-baz-baz', $files1->getInput('duo'));

        $files1->enable('pre');
        $files1->enable('pre'); // do nothing
        $this->assertEquals('foo-bar-baz', $files1->getInput('pre'));

        $files2 = new FilesMock();
        $files2->updateInput('pre', 'abc-def-ghi');
        $files2->disable('pre');
        $files2->disable('pre'); // do nothing
        $this->assertEquals('abc-def-ghi', $files2->getInput('pre'));

        $files2->removeInput('duo');
        $files2->removeInput('pre');
        $this->assertEmpty($files2->getFiles(), 'No too many files left');
    }

    /**
     * @throws AfterloadException
     */
    public function testFailRead(): void
    {
        $files = new FilesMock();
        $this->assertEmpty($files->getFiles(), 'Something left from previous run');
        $files->addInput('erteddf', 'poiuztr');
        $in = $files->getFiles();
        $in = reset($in);
        chmod($in->getFullName(), 0222);
        $this->expectException(AfterloadException::class);
        $files->getInput('erteddf'); // fail
    }

    /**
     * @throws AfterloadException
     */
    public function testDoubleAdd(): void
    {
        $files = new FilesMock();
        $this->assertEmpty($files->getFiles(), 'Something left from previous run');
        $files->addInput('bnuuu', 'poiuztr');
        $in = $files->getFiles();
        $in = reset($in);
        chmod($in->getFullName(), 0444);
        $this->expectException(AfterloadException::class);
        $files->addInput('bnuuu', 'lkjhgfd'); // fail
    }

    /**
     * @throws AfterloadException
     */
    public function testDoubleUpdate(): void
    {
        $files = new FilesMock();
        $this->assertEmpty($files->getFiles(), 'Something left from previous run');
        $files->addInput('kldsg', 'poiuztr');
        $in = $files->getFiles();
        $in = reset($in);
        chmod($in->getFullName(), 0444);
        $this->expectException(AfterloadException::class);
        $files->updateInput('kldsg', 'lkjhgfd', false); // fail
    }

    /**
     * @throws AfterloadException
     */
    public function testSearch(): void
    {
        $files = new FilesMock();
        $this->assertEmpty($files->getFiles(), 'Something left from previous run');
        $this->expectException(AfterloadException::class);
        $files->getInput('unknown'); // fail
    }
}


class FilesMock extends Files
{
    protected function generatePaths(): void
    {
        parent::generatePaths();
        $this->enabledPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'test_conf_enabled']));
        $this->disabledPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'test_conf_disabled']));
    }
}