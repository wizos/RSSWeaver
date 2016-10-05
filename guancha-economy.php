<?php
include "./lib/lib_phpQuery.php";
include "./lib/lib_spiderss.php";

// 制定配置
$config['start_url'] = 'http://m.guancha.cn/economy';
$config['file_title'] = 'guancha-economy'; 
$config['feed_title'] = '观察者财经';
$config['feed_description'] = '';
$config['feed_size'] = 10;
$config['cache_time'] = 6*3600;
$config['output_encoding'] = 'utf-8';

$config['list_scopen'] = '#Wapper > article div > section';
$config['list_href'] = 'a';
$config['list_href_prefix'] = 'http://m.guancha.cn';

$config['item_scopen'] = '#Wapper > article'; // 包含 title，pubDate，author
$config['item_title'] = 'h1';
$config['item_content'] = 'section:nth-child(3)';
$config['item_pubdate'] = 'time';

crawling($config);// 开始抓取


// 提取正文的规则编写
function extracting_article ( $art_html, $config ){
    phpQuery::newDocument( $art_html );
    pq( 'script' )->remove();
    pq( 'div.m_share' )->remove();
    $art_scopen = pq( $config['item_scopen'] );
    $item['title'] = $art_scopen->find( $config['item_title'] )->text();
    $item['content'] = $art_scopen->find( $config['item_content'] )->html();
    $item['pubdate'] = $art_scopen->find( $config['item_pubdate'] )->text();
    $item['pubdate'] = date('r',strtotime( $item['pubdate'] ));
    return $item;
}

?>