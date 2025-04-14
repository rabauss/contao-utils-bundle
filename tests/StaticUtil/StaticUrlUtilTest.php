<?php

namespace StaticUtil;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\UtilsBundle\StaticUtil\StaticUrlUtil;

class StaticUrlUtilTest extends ContaoTestCase
{
    public function testUnparseUrl()
    {
        $this->assertEmpty(StaticUrlUtil::unparseUrl([]));
        $this->assertEmpty(StaticUrlUtil::unparseUrl(['port' => '1234']));

        $this->assertEquals('//example.com:1234', StaticUrlUtil::unparseUrl(['host' => 'example.com', 'port' => '1234']));
        $this->assertEquals(
            '//operator:secret@example.com:1234',
            StaticUrlUtil::unparseUrl(['user' => 'operator', 'pass' => 'secret', 'host' => 'example.com', 'port' => '1234'],
                ['suffixEmptyScheme' => true]),
        );
        $this->assertEquals('example.com:1234', StaticUrlUtil::unparseUrl(['host' => 'example.com', 'port' => '1234'],
            ['suffixEmptyScheme' => false]));

        $this->assertEquals('?foo=bar', StaticUrlUtil::unparseUrl(['query' => 'foo=bar']));
        $this->assertEquals('?foo=bar', StaticUrlUtil::unparseUrl(['query' => 'foo=bar'], ['prefixQuery' => true]));
        $this->assertEquals('foo=bar', StaticUrlUtil::unparseUrl(['query' => 'foo=bar'], ['prefixQuery' => false]));

        $this->assertEquals('#foo=bar', StaticUrlUtil::unparseUrl(['fragment' => 'foo=bar']));
        $this->assertEquals('#foo=bar', StaticUrlUtil::unparseUrl(['fragment' => 'foo=bar'], ['prefixFragment' => true]));
        $this->assertEquals('foo=bar', StaticUrlUtil::unparseUrl(['fragment' => 'foo=bar'], ['prefixFragment' => false]));

        $this->assertEquals('foo=bar#baz', StaticUrlUtil::unparseUrl(['query' => 'foo=bar', 'fragment' => 'baz'],
            ['prefixQuery' => false, 'prefixFragment' => false]));

        $url = 'wss://user:pass@example.com:1234?foo=bar#baz';
        $this->assertEquals($url, StaticUrlUtil::unparseUrl(\parse_url($url)));
    }
}