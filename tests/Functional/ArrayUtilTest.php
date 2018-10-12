<?php

declare(strict_types=1);

namespace CommonTest\Functional;

use Civis\Common\ArrayUtil;

class ArrayUtilTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayUtil()
    {
        $this->assertNull(ArrayUtil::getFromArray(null, null));
        $this->assertNull(ArrayUtil::getFromArray(null, "test"));
        $this->assertNull(ArrayUtil::getFromArray([], "test"));
        $this->assertNull(ArrayUtil::getFromArray(["test"], "test"));
        $this->assertSame("test", ArrayUtil::getFromArray(["test"], 0));
        $this->assertSame("test", ArrayUtil::getFromArray([
            "key" => "test"
        ], "key"));
    }


    public function testPath()
    {
        $testArray = [
            "test" => [
                7,
                [
                    "path2" => "result"
                ]
            ]
        ];

        $result = ArrayUtil::getPathFromArray($testArray, "test.1.path2");

        $this->assertSame("result", $result);

    }
}