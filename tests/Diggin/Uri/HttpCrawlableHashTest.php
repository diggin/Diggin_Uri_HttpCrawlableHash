<?php

require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Uri/HttpCrawlableHash.php';

class Diggin_Uri_HttpCrawlableHashTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Zend_Uri::setConfig(array('allow_unwise' => true));
    }

    protected function tearDown()
    {
        Zend_Uri::setConfig(array('convert_always' => false));
    }

    public function testBasic()
    {

       $uri = Zend_Uri::factory('http://example.com/?_escaped_fragment_=key1=value1%26key2=value2', 'Diggin_Uri_HttpCrawlableHash');
       $this->assertEquals((string) $uri->getQueryToFragment(),
                           "http://example.com/#!key1=value1&key2=value2");

       $uri = Zend_Uri::factory('http://example.com/?user=a&q=b&_escaped_fragment_=key1=value1%26key2=value2', 'Diggin_Uri_HttpCrawlableHash');
       $this->assertEquals((string) $uri->getQueryToFragment(),
                           "http://example.com/?user=a&q=b#!key1=value1&key2=value2");


       // Mapping from #! to _escaped_fragment_ format
       $uri2 = Zend_Uri::factory('http://example.com/#!key1=value1&key2=value2', 'Diggin_Uri_HttpCrawlableHash');
       $this->assertEquals((string) $uri2->getFragmentToQuery(),
                           "http://example.com/?_escaped_fragment_=key1=value1%26key2=value2");

       $uri2 = Zend_Uri::factory('http://example.com/path?old_query#!key1=value1&key2=value2', 'Diggin_Uri_HttpCrawlableHash');
       $this->assertEquals((string) $uri2->getFragmentToQuery(),
                           "http://example.com/path?old_query&_escaped_fragment_=key1=value1%26key2=value2");

       $uri2 = Zend_Uri::factory('http://example.com/path?_escaped_fragment_=hoge#!key1=value1&key2=value2', 'Diggin_Uri_HttpCrawlableHash');
       $this->assertEquals((string) $uri2->getFragmentToQuery(),
                           "http://example.com/path?_escaped_fragment_=key1=value1%26key2=value2");
    }

    public function testConvertAlways()
    {
        Zend_Uri::setConfig(array('convert_always' => 'query'));
        $uri = Zend_Uri::factory('http://example.com/#!key1=value1&key2=value2', 'Diggin_Uri_HttpCrawlableHash');
        $this->assertEquals((string)$uri, 'http://example.com/?_escaped_fragment_=key1=value1%26key2=value2');

        Zend_Uri::setConfig(array('convert_always' => false));
        $uri = Zend_Uri::factory($u = 'http://example.com/#!key1=value1&key2=value2', 'Diggin_Uri_HttpCrawlableHash');
        $this->assertEquals((string)$uri, $u);


        Zend_Uri::setConfig(array('convert_always' => 'hash'));
        $uri = Zend_Uri::factory('http://example.com/?_escaped_fragment_=key1=value1%26key2=value2', 'Diggin_Uri_HttpCrawlableHash');
        $this->assertEquals((string)$uri, 'http://example.com/#!key1=value1&key2=value2');

        Zend_Uri::setConfig(array('convert_always' => false));
        $uri = Zend_Uri::factory($u = 'http://example.com/?_escaped_fragment_=key1=value1%26key2=value2', 'Diggin_Uri_HttpCrawlableHash');
        $this->assertEquals((string)$uri, $u);
    }
}



/** memo
Zend_Uri::setConfig(array('allow_unwise' => true));

$url = 'http://twitter.com/#!/bulkneets\x00';
//$url = "http://twitter.com/#!/bulkneets\x00";

$uri = Zend_Uri::factory($url, 'Diggin_Uri_HttpCrawlableHash');

var_dump($uri->hasCrawlableHash());
var_dump((string)$uri->getFragmentToQuery());

Zend_Uri::setConfig(array('convert_always' => 'query'));
var_dump((string)Zend_Uri::factory($url, 'Diggin_Uri_HttpCrawlableHash'));


var_dump((string)Zend_Uri::factory("http://example.com/#!key1=value1&key2=value2", 'Diggin_Uri_HttpCrawlableHash'));


//var_dump(preg_replace_callback('/(?:[\x00-\x20])/', create_function('$b', 'return rawurlencode($b[0]);'), "aa\x00")); 
//var_dump(preg_replace_callback('/(?:[^\x00-\x20])/', create_function('$b', 'return rawurlencode($b[0]);'), "aa\x00"));
*/
