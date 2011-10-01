<?php
namespace DigginTests\Uri;

use Zend\Uri\UriFactory,
    Diggin\Uri\HttpCrawlableHash;

class HttpCrawlableHashTest extends \PHPUnit_Framework_TestCase
{
    public function testFiltering()
    {
       $uri = new HttpCrawlableHash('http://example.com/?_escaped_fragment_=key1=value1%26key2=value2');
       $uri->filterQueryToFragment();
       $this->assertEquals($uri->toString(),
                           "http://example.com/#!key1=value1&key2=value2");

       $uri = new HttpCrawlableHash('http://example.com/?user=a&q=b&_escaped_fragment_=key1=value1%26key2=value2');
       $uri->filterQueryToFragment();
       $this->assertEquals($uri->toString(),
                           "http://example.com/?user=a&q=b#!key1=value1&key2=value2");

       // Mapping from #! to _escaped_fragment_ format
       $uri2 = new HttpCrawlableHash('http://example.com/#!key1=value1&key2=value2');
       $uri2->filterFragmentToQuery();
       $this->assertEquals($uri2->toString(),
                           "http://example.com/?_escaped_fragment_=key1=value1%26key2=value2");

       $uri2 = new HttpCrawlableHash('http://example.com/path?old_query#!key1=value1&key2=value2');
       $uri2->filterFragmentToQuery();
       $this->assertEquals($uri2->toString(),
                           "http://example.com/path?old_query&_escaped_fragment_=key1=value1%26key2=value2");

       $uri2 = new HttpCrawlableHash('http://example.com/path?_escaped_fragment_=hoge#!key1=value1&key2=value2');
       $uri2->filterFragmentToQuery();
       $this->assertEquals($uri2->toString(),
                           "http://example.com/path?_escaped_fragment_=key1=value1%26key2=value2");
    }

    public function testClonedAndFiltering()
    {
       $uri = new HttpCrawlableHash($origin_uri = 'http://example.com/?_escaped_fragment_=key1=value1%26key2=value2');
       $newuri = $uri->getQueryToFragment();

       $this->assertNotEquals(spl_object_hash($newuri), spl_object_hash($uri));

       $this->assertEquals($uri->toString(),
                           $origin_uri);

       $newuri->filterQueryToFragment();
       $this->assertEquals($newuri->toString(),
                           "http://example.com/#!key1=value1&key2=value2");
    
       //
       $uri2 = new HttpCrawlableHash($origin_uri2 = 'http://example.com/#!key1=value1&key2=value2');
       $newuri2 = $uri2->getFragmentToQuery();

       $this->assertNotEquals(spl_object_hash($newuri2), spl_object_hash($uri2));

       $this->assertEquals($uri2->toString(),
                           $origin_uri2);

       $uri2->filterFragmentToQuery();
       $this->assertEquals($uri2->toString(),
                           "http://example.com/?_escaped_fragment_=key1=value1%26key2=value2");
    }
}
