<?php
error_reporting(E_ERROR);

$default_header['User-Agent'] = 'Mozilla/5.0 (Linux; U; Android 5.1; zh-CN; MX5 Build/LMY47I) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.9.2.712 U3/0.8.0 Mobile Safari/534.30';
$default_header['Accept-Language'] = 'zh-cn';
$default_header["timeout"] = 9000;
$cookie_file;


function getHtml( $url ){
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => $useragent,
        CURLOPT_TIMEOUT_MS => $GLOBALS['default_header']["timeout"],
        CURLOPT_NOSIGNAL => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1
    );
    
    $options[CURLOPT_COOKIEFILE] = $GLOBALS['cookie_file']; //读取cookie
    $options[CURLOPT_HTTPHEADER] = $GLOBALS['default_header'];
    if (preg_match('/^https/',$url)){
        $options[CURLOPT_SSL_VERIFYHOST] = 1;
        $options[CURLOPT_SSL_VERIFYPEER] = 0;
    }
    curl_setopt_array($ch, $options);
    $data = curl_exec($ch);
    $curl_errno = curl_errno($ch); 
    curl_close($ch);//关闭cURL资源，并且释放系统资源
    if($curl_errno>0){
        return 'error';
    }else{
        return $data;
    }
}

function login( $url, $post ) {
    $curl = curl_init();//初始化curl模块
    curl_setopt($curl, CURLOPT_URL, $url );//登录提交的地址
    curl_setopt($curl, CURLOPT_HEADER, 0);//如果你想把一个头包含在输出中，设置这个选项为一个非零值
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//设置不输出在浏览器上
    curl_setopt($curl, CURLOPT_COOKIEJAR, $GLOBALS['cookie_file'] ); //设置Cookie信息保存在指定的文件中
    curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
    curl_setopt($curl, CURLOPT_HTTPHEADER, $GLOBALS['default_header'] );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post );//要提交的信息
    $r = curl_exec($curl);//执行cURL
    
	//echo $GLOBALS['cookie_file'];
	//echo $url.$post.'-----';
    $curl_errno = curl_errno($curl);
    curl_close($curl);//关闭cURL资源，并且释放系统资源
    if($curl_errno>0){
        return 'error';
    }
}

function crawling( $config ){
    $output_folder = '..\feed';
    $output_file = dirname( __FILE__ ).'\\'.$output_folder.'\\'.$config['file_title'].'.xml' ;
    if ( is_file( $output_file ) ){
        $revise_time = time() - filemtime( $output_file ) ; // 以秒为单位
        if ( $revise_time < $config['cache_time'] ){
            header('Content-Type: text/html; charset='.$config['output_encoding']) ;
            @readfile( $output_file );
            exit();
        }
    }elseif( !is_dir(dirname( __FILE__ ).'\\'.$output_folder) ){
        mkdir( dirname( __FILE__ ).'\\'.$output_folder );
    }
    
	$GLOBALS['cookie_file'] = dirname( __FILE__ ).'\\..\\cookie\\'.$config['file_title'].'.txt';
    if( $config['request_header'] ){
	    $GLOBALS['default_header'] = array_merge( $config['request_header'] , $GLOBALS['default_header'] );
    }

    $header = '<?xml version="1.0" encoding="'.$config['output_encoding'].'"?><rss version="2.0"><link rel="stylesheet" href="lib/rss.css"/><channel>
<title>'.$config['feed_title'].'</title>
<link>'.$config['start_url'].'</link>
<description>'.$config['feed_description'].'</description>'; 
    $footer = '</channel></rss>';

	if ( $config['post'] ){
    	$html = getHtml( $config['start_url'] );
		call_user_func_array( 'have_login', array($html, $config) );
	}

    // 采集规则编写

    // 【1】获取所有文章链接
    $html = getHtml( $config['start_url'] );
    //file_put_contents( $output_file.'.txt', $html );
    //echo $html;
    
    if( function_exists( 'extracting_artlist' ) ){
        $params_artlist = array( $html, $config );
	    $list = call_user_func_array( 'extracting_artlist', $params_artlist );
	    $links = $list['link']?$list['link']:$list;
	    //echo count( $list['link'] ).'==='.count( $links );
    }else{
    	phpQuery::newDocument( $html );
    	$artlist = pq( $config['list_scopen'] );
    	$href_prefix = $config['list_href_prefix'] ? $config['list_href_prefix']:'';
	    if( function_exists( 'handle_link' ) ){
	    	foreach( $artlist as $li ){
        		$href = pq($li)->find( $config['list_href'] ) -> attr('href');
	    		$href = call_user_func_array( 'handle_link', array( $href ) );
        		$links[] = $href_prefix.$href;
    		}
    	}else{
	    	foreach( $artlist as $li ){
        		$href = pq($li)->find( $config['list_href'] ) -> attr('href');
        		$links[] = $href_prefix.$href;
    		}
    	}
	}

    // 【2】控制要输出的链接数目
    $size = count( $links );
    $feed_size = $config['feed_size'] ? $config['feed_size']:10;
    $size = ( $size > $feed_size ) ? $feed_size:$size;
    //echo '=size='.count( $list ).'--'.$size;

    $config['list'] = $list;
    // 【3】抽取每个链接的内容信息
    for( $i=0; $i<$size; $i++ ){
        $link = $links[$i];
        $art_html = getHtml( $link );
        if ( $art_html == 'error' ){
	        $art_html = getHtml( $link );
        }
        
    	$config['i'] = $i ;
        $params_article = array( $art_html, $config );
        $item = call_user_func_array('extracting_article', $params_article);

        
        $item['title'] = trim( $item['title'] );
        if( $item['title']=='' ){
            continue;
        }
        $item['content'] = trim( $item['content'] );
    
        $rss_item.='<item><title>'.$item['title'].'</title><link>'.$link.'</link><author>'.$item['author'].'</author><pubDate>'.$item['pubdate'].'</pubDate><description><![CDATA['.$item['content'].']]></description></item>';
    }

    // 【4】写出、输出
    if($rss_item==null){
	    exit();
    }
    file_put_contents( $output_file, $header.$rss_item.$footer );
    header('Content-Type: text/html; charset='.$config['output_encoding']) ;
    @readfile( $output_file );
}

?>