<?php
require_once 'Zend/Uri/Http.php';

/**
 *
 * @see http://code.google.com/intl/ja/web/ajaxcrawling/docs/specification.html
 * @see http://subtech.g.hatena.ne.jp/mala/20101018/1287419036
 */
class Diggin_Uri_HttpCrawlableHash extends Zend_Uri_Http
{
    const CONVERT_QUERY = 'query';
    const CONVERT_HASH  = 'hash';
    const ESCAPED_FRAGMENT = '_escaped_fragment_';

    public function __construct($scheme, $schemeSpecific = '')
    {
        parent::__construct($scheme, $schemeSpecific);
        
        if (isset(self::$_config['convert_always'])) {
            if (self::CONVERT_QUERY === self::$_config['convert_always']) {
                $this->filterFragmentToQuery();
            } else if (self::CONVERT_HASH === self::$_config['convert_always']) {
                $this->filterQueryToFragment();
            }
        }
    }

    public function hasCrawlableHash()
    {
        return !(strpos($this->getFragment(), '!'));
    }

    public function hasEscapedFragment()
    {
        if ($query = $this->getQueryAsArray()) {
            return isset($query[self::ESCAPED_FRAGMENT]) ? true : false;
        }

        return false;
    }

    public function getFragmentToQuery()
    {
        $clone = clone $this;
        $clone->filterFragmentToQuery();

        return $clone;
    }

    public function getQueryToFragment()
    {
        $clone = clone $this;
        $clone->filterQueryToFragment();

        return $clone;
    }

    public function filterFragmentToQuery()
    {
        if ($this->hasCrawlableHash()) {

            $fragment = $this->getFragment();
            $fragment = preg_replace('/^!/', '', $fragment);
        
            $query = self::ESCAPED_FRAGMENT.'='.preg_replace_callback('/(?:[\x00-\x20][\x23][\x25-\x26][\x2B][\x7F-\xFF])/', 
                                       create_function('$b', 'return rawurlencode($b[0]);'), $fragment); 

            if ($this->getQuery()) {
                $old = $this->getQuery();
                $escapedFragment = self::ESCAPED_FRAGMENT;
                $old = preg_replace("/$escapedFragment=[^\&]*$/", '', $old);
                $delimiter = ($old) ? '&' : '';
                //$this->setQuery($old.$delimiter.$query);
                $this->setQuery($old.$delimiter.str_replace("&", "%26", $query));
            } else {
                //$this->setQuery($query); //?

                $this->setQuery(str_replace("&", "%26", $query));
            }

            $this->setFragment('');
        }
    }

    public function filterQueryToFragment()
    {
        if ($this->hasEscapedFragment()) {
            $query = $this->getQueryAsArray();

            $escapedFragment = $query[self::ESCAPED_FRAGMENT];
            unset($query[self::ESCAPED_FRAGMENT]);
            $this->setQuery($query);
            
            $this->setFragment('!'. rawurldecode($escapedFragment));
        }
    }
}

