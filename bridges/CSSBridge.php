<?php

class CSSBridge extends BridgeAbstract {

  const NAME = 'CSS Bridge';
  const URI = 'https://*';
  const CACHE_TIMEOUT = 3600;
  const DESCRIPTION = 'Feed from any web site by css styles';
  const MAINTAINER = 'kolarcz';

  const PARAMETERS = array(
    array(
      'url' => array(
        'name' => 'url of page',
        'exampleValue' => 'https://www.example.com',
        'required' => true
      ),
      'item' => array(
        'name' => 'selector for individual items',
        'exampleValue' => 'body .items > .item',
        'required' => true
      ),
      'title' => array(
        'name' => 'selector for item title',
        'exampleValue' => '.title',
        'required' => true
      ),
      'content' => array(
        'name' => 'selector for item content',
        'exampleValue' => '.content'
      ),
      'author' => array(
        'name' => 'selector for item author',
        'exampleValue' => '.author'
      ),
      'timestamp' => array(
        'name' => 'selector for item timestamp',
        'exampleValue' => '.timestamp',
      ),
      'uri' => array(
        'name' => 'selector for item url',
        'exampleValue' => 'a'
      ),
      'image' => array(
        'name' => 'selector for item image',
        'exampleValue' => 'img'
      )
    )
  );

  public function collectData() {
    $html = getSimpleHTMLDOM($this->getInput('url'))
      or returnServerError('Could not request Web page.');

    $itemSelector = $this->getInput('item');
    foreach ($html->find($itemSelector) as $itemElement) {
      $item = array();
      $item['title'] = $itemElement->find($this->getInput('title'), 0)->plaintext;
      $item['content'] = '';

      $contentSelector = $this->getInput('content');
      if ($contentSelector) {
        $content = $itemElement->find($contentSelector, 0);
        if ($content) {
          $item['content'] = $content->innerhtml;
        }
      }

      $imageSelector = $this->getInput('image');
      if ($imageSelector) {
        $img = $itemElement->find($imageSelector, 0);
        if ($img) {
          $item['content'] = '<img src="' . $img->getAttribute('src') . '" /><br />' . $item['content'];
        }
      }

      $authorSelector = $this->getInput('author');
      if ($authorSelector) {
        $author = $itemElement->find($authorSelector, 0);
        if ($author) {
          $item['author'] = $author->plaintext;
        }
      }

      $timeSelector = $this->getInput('timestamp');
      if ($timeSelector) {
        $time = $itemElement->find($timeSelector, 0);
        if ($time) {
          $item['timestamp'] = strtotime($time->plaintext);
        }
      }

      $urlSelector = $this->getInput('uri');
      if ($urlSelector) {
        $url = $itemElement->find($urlSelector, 0);
        if ($url) {
          $item['uri'] = $url->getAttribute('href');
        }
      }

      $this->items[] = $item;
    }
  }

}
