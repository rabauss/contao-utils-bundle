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
            StaticUrlUtil::unparseUrl(['user' => 'operator', 'pass' => 'secret', 'host' => 'example.com', 'port' => '1234'], emptySchemeSuffix: true),
        );
        $this->assertEquals('example.com:1234', StaticUrlUtil::unparseUrl(['host' => 'example.com', 'port' => '1234'], emptySchemeSuffix: false));

        $this->assertEquals('?foo=bar', StaticUrlUtil::unparseUrl(['query' => 'foo=bar']));
        $this->assertEquals('?foo=bar', StaticUrlUtil::unparseUrl(['query' => 'foo=bar'], queryPrefix: true));
        $this->assertEquals('foo=bar', StaticUrlUtil::unparseUrl(['query' => 'foo=bar'], queryPrefix: false));

        $this->assertEquals('#foo=bar', StaticUrlUtil::unparseUrl(['fragment' => 'foo=bar']));
        $this->assertEquals('#foo=bar', StaticUrlUtil::unparseUrl(['fragment' => 'foo=bar'], fragmentPrefix: true));
        $this->assertEquals('foo=bar', StaticUrlUtil::unparseUrl(['fragment' => 'foo=bar'], fragmentPrefix: false));

        $this->assertEquals('foo=bar#baz', StaticUrlUtil::unparseUrl(['query' => 'foo=bar', 'fragment' => 'baz'], queryPrefix: false, fragmentPrefix: false));

        $url = 'wss://user:pass@example.com:1234?foo=bar#baz';
        $this->assertEquals($url, StaticUrlUtil::unparseUrl(\parse_url($url)));
    }
}