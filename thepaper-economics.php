<?php
include "./lib/lib-phpQuery.php";
include "./lib/lib-spiderss.php";
//include "gethtml.php";
//date_default_timezone_set('PRC');//设置时区为中华人民共和国，东八区
//date_default_timezone_set("Etc/GMT");//这是格林威治标准时间

// 制定配置
$config['start_url'] = 'http://m.thepaper.cn/channel_25951';
$config['file_title'] = 'thepaper-economics';
$config['feed_title'] = '澎湃财经';
$config['feed_description'] = '';
$config['feed_size'] = 10;
$config['cache_time'] = 6*3600;
$config['output_encoding'] = 'utf-8';
//$config['input_encoding'] = 'gbk';
//$request_header[] = "Host:m.thepaper.cn"; 
//$request_header[] = 'Referer:'.'http://m.thepaper.cn/channel_25951';

$config['list_scopen'] = '#v3cont_id div.t_news';
$config['list_href'] = 'div.txt_t > div > p > a';
$config['list_href_prefix'] = 'http://m.thepaper.cn/';

$config['item_scopen'] = '#v3cont_id > div.news_content'; // 包含 title，pubDate，author
$config['item_title'] = 'h1';
$config['item_content'] = 'div.news_part';
$config['item_pubdate'] = 'p:nth-child(3)';
//$config['item_author'] = '';

crawling($config);// 开始抓取


// 提取正文的规则编写
function extracting_article ( $art_html, $config ){
    phpQuery::newDocument( $art_html );
    $art_scopen = pq( $config['item_scopen'] );
    $item['title'] = $art_scopen->find( $config['item_title'] )->text();
    $item['content'] = $art_scopen->find( $config['item_content'] )->html();
    $item['pubdate'] = $art_scopen->find( $config['item_pubdate'] )->text();
    $item['pubdate'] = date('r',strtotime( $item['pubdate'] ));
    return $item;
}

?>