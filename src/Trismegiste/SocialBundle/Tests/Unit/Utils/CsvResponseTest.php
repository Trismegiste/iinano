<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils;

use Trismegiste\SocialBundle\Utils\CsvResponse;

/**
 * CsvResponseTest tests CsvResponseTest
 */
class CsvResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleArray()
    {
        $content = [['name' => 'aaaa', 'age' => 12], ['name' => 'bbbb', 'age' => 6]];
        $sut = new CsvResponse(new \ArrayIterator($content), ['Name' => '[name]', 'Age' => '[age]']);

        $rows = explode(PHP_EOL, $sut->getContent());
        $this->assertEquals('Name,Age', $rows[0]);
        $this->assertEquals('"aaaa",12', $rows[1]);
        $this->assertEquals('"bbbb",6', $rows[2]);
    }

    protected function createItem($name, $age)
    {
        $obj = new \stdClass();
        $obj->name = $name;
        $obj->age = $age;

        return $obj;
    }

    public function testSimpleObject()
    {
        $content = [
            $this->createItem('aaaa', 12),
            $this->createItem('bbbb', 6)
        ];
        $sut = new CsvResponse(new \ArrayIterator($content), ['Name' => 'name', 'Age' => 'age']);

        $rows = explode(PHP_EOL, $sut->getContent());
        $this->assertEquals('Name,Age', $rows[0]);
        $this->assertEquals('"aaaa",12', $rows[1]);
        $this->assertEquals('"bbbb",6', $rows[2]);
    }

    public function testWithFormatter()
    {
        $content = [
            $this->createItem('aaaa', new \DateTime('2003-07-07')),
            $this->createItem('bbbb', new \DateTime('2009-03-03')),
            $this->createItem(null, new \DateTime('2000-01-01'))
        ];
        $sut = new CsvResponse(new \ArrayIterator($content), [
            'Name' => 'name',
            'Year' => [
                'path' => 'age',
                'render' => function($val) {
                    return $val->format('Y');
                }
            ]
        ]);

        $rows = explode(PHP_EOL, $sut->getContent());
        $this->assertEquals('Name,Year', $rows[0]);
        $this->assertEquals('"aaaa","2003"', $rows[1]);
        $this->assertEquals('"bbbb","2009"', $rows[2]);
        $this->assertEquals(',"2000"', $rows[3]);
    }

}
