<?php
namespace MauticPlugin\MauticXmlToEmailBundle\Parser;

class Parser
{
    protected $event;
    protected $content;

    public function __construct($content, $event)
    {
        $this->setContent($content);
        $this->event = $event;

        $this->parseContent();
    }

    public function parseContent()
    {
        $content = $this->getContent();

        $feedMatches = array();
        preg_match_all('|[\<\{]feed\s+url\=\"(.+)\"[^\>\}]*[\>\}](.*)[\<\{]\/feed[\>\}]|msU', $content, $feedMatches, PREG_SET_ORDER);
        foreach($feedMatches as $feedMatch) {
        	$feedHtmlContent = $feedMatch[2];
        	try {

        		// get feed content (cached)
        		$cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();
        		$cacheKey = preg_replace('|[^a-zA-Z0-9\.\-\_]+|','.','URL:'.$feedMatch[1]);
        		$feedContent = $cache->get($cacheKey,function(\Symfony\Contracts\Cache\ItemInterface $item) use ($feedMatch) {
        			// cache for 5 minutes
        			$item->expiresAfter(300);
        			$content = file_get_contents($feedMatch[1]);
        			if (!$content)
        				throw new \Exception('Error when requesting url');
        			
        			return $content;
        		});

        		// load feed XML
				$feedXml = new \SimpleXMLElement($feedContent);

				// find/replace global feed properties
				foreach($feedXml as $key => $value) {
					$feedHtmlContent = str_replace('{feed.'.$key.'}', trim((string) $value), $feedHtmlContent);
				}

				// find feed items	        	
				$itemMatches = array();
				preg_match_all('|[\<\{]feeditem\s+loop\=\"(.+)\"[^\>\}]*[\>\}](.*)[\<\{]\/feeditem[\>\}]|msU', $feedHtmlContent, $itemMatches, PREG_SET_ORDER);
				foreach($itemMatches as $itemMatch) {
					$feedItems = array();
					$loopName = $itemMatch[1];
					
					// find loop element
					$loopElement = null;
					if ($loopName !== 'root' && $loopName !== '') {
						foreach($feedXml as $name => $element) {
							// find tag with loopName
							if ($name === $loopName) {
								$loopElement = $element;
								break;
							}
						}
					} else {
						$loopElement = $feedXml;
					}
					
					// check loop element
					if ($loopElement === null)
						throw new \Exception('Cannot find loop element "'.$loopName.'"');
					
					// loop through children
					foreach($loopElement->children() as $child) {
						$feedItemContent = $itemMatch[2];
						
						// find/replace feeditem properties
						foreach($child->children() as $key => $value) {
							$feedItemContent = str_replace('{feeditem.'.$key.'}', trim((string) $value), $feedItemContent);
						}
						$feedItems[] = $feedItemContent;
					}

					// add feed items
					$feedHtmlContent = str_replace($itemMatch[0],implode("\n",$feedItems),$feedHtmlContent);
				}
        	
        	} catch (\Exception $e) {
        		$feedHtmlContent = 'Error in XML feed '.$feedMatch[1].': '.$e->getMessage();
        	}
        	
        	// replace unmatched tags
        	$feedHtmlContent = preg_replace('|[\<\{]feed(item)?\.[^\>\}]*[\>\}]|','Token $0 not found',$feedHtmlContent);
        	
        	// replace feed match
        	$content = str_replace($feedMatch[0],$feedHtmlContent,$content);
        }

        $this->setContent($content);
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getEvent()
    {
        return $this->event;
    }
}
