Diggin_Uri_HttpCrawlableHash
============================

convert ajax uri to crawlable

porting from https://github.com/mala/p5-URI-CrawlableHash

## Usage
<pre>
$uri = new Diggin\Uri\HttpCrawlableHash('http://twitter.com/#!/tomita/status/15754739119562752');
$uri->filterFragmentToQuery();
echo $uri->toString(); // http://twitter.com/?_escaped_fragment_=/tomita/status/15754739119562752
</pre>

## Requirements
Zend Framework 2
