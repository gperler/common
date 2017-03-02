<?php

declare(strict_types = 1);

namespace CommonTest\Functional;

use Civis\Common\File;

class ClassReaderTest extends \PHPUnit_Framework_TestCase
{


    public function testDirectoryDetails()
    {
        $file = new File(__DIR__);

        $this->assertTrue($file->exists());
        $this->assertTrue($file->isDir());
        $this->assertFalse($file->isFile());

        $this->assertSame("Functional", $file->getFileName());
        $this->assertSame(__DIR__, $file->getAbsoluteFileName());
        $this->assertSame("Functional", $file->getExtension());
        $dirName = str_replace(DIRECTORY_SEPARATOR . "Functional", "", __DIR__);
        $this->assertSame($dirName, $file->getDirName());

    }

    public function testFile()
    {
        $file = new File(__FILE__);

        $this->assertTrue($file->exists());
        $this->assertFalse($file->isDir());
        $this->assertTrue($file->isFile());

        $this->assertSame("FileTest.php", $file->getFileName());
        $this->assertSame(__FILE__, $file->getAbsoluteFileName());
        $this->assertSame("php", $file->getExtension());
        $this->assertSame(__DIR__, $file->getDirName());
        $this->assertSame("php", $file->getExtension());

        $this->assertTrue($file->hasExtension("php"));
        $this->assertFalse($file->hasExtension("java"));
    }

    public function testPutContents()
    {
        $testDir = __DIR__ . DIRECTORY_SEPARATOR . "doesNotExist";
        $fileName = $testDir . DIRECTORY_SEPARATOR . "otherDir" . DIRECTORY_SEPARATOR . "test.xml";

        $file = new File($fileName);
        $file->putContents("<xml/>");
        $this->assertTrue(file_exists($fileName));

        $dir = new File($testDir);
        $dir->deleteRecursively();
        $this->assertFalse(file_exists($testDir));
    }

    public function testFindFile()
    {
        $dir = new File(__DIR__);
        $fileList = $dir->findFileList("xml");
        $this->assertSame(2, sizeof($fileList));

        $this->assertSame("test.xml", $fileList[0]->getFileName());
        $this->assertSame("invalid.xml", $fileList[1]->getFileName());

        $json = $dir->findFirstOccurenceOfFile("test.json");

        $this->assertNotNull($json);
        $this->assertSame("test.json", $json->getFileName());
    }

    public function testLoadXML()
    {
        $dir = new File(__DIR__);
        $fileList = $dir->findFileList("xml");
        $this->assertSame(2, sizeof($fileList));

        $validXML = $fileList[0];
        $this->assertSame("test.xml", $validXML->getFileName());
        $domDocument = $validXML->loadAsXML();
        $this->assertNotNull($domDocument);

        try {
            $invalidFile = $fileList[1];
            $invalidFile->loadAsXML();
            $this->assertTrue(false);
        } catch (\Exception $e) {

        }

    }

    public function testLoadJSON()
    {
        $dir = new File(__DIR__);
        $json = $dir->findFirstOccurenceOfFile("test.json");
        $this->assertNotNull($json);

        $array = $json->loadAsJSONArray();
        $this->assertTrue(is_array($array));

        $this->assertSame(true, $array["hello"]);

        $json = $dir->findFirstOccurenceOfFile("invalid.json");
        $this->assertNotNull($json);

        try {
            $array = $json->loadAsJSONArray();
            $this->assertTrue(false);
        } catch (\Exception $e) {

        }

    }

}