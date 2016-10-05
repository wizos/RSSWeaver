<?php
//
// Author: wizos <wizos@qq.com>
//
// Copyright (c) 2016 GNU


header("Content-Type: text/html; charset=utf-8");

	echo "<html><head><title>RSS-爬虫管理</title><link rel='stylesheet' href='lib/style.css'/><meta name='viewport' content='width=device-width, initial-scale=1' />  \n";
	echo "</head><body>\n";

	echo "<div class='wrapper'><header class='site-header clearfix'><div class='avatar'><a href='#'><img src='lib/logo.png' width='120' height='120'></a></div><h1 class='logo'>织薇</h1><div class='description'><p>目前有以下爬虫输出 RSS</p></div></header>";
	echo "<section class='feed-list'><ul>";
	
	echo "<li><a href='catcoder.php' target='_blank'>CatCoder</a></li>\n";
	echo "<li><a href='guancha-economy.php' target='_blank'>观察者：财经</a></li>\n";
	echo "<li><a href='thepaper-economics.php' target='_blank'>澎湃：财经</a></li>\n";

	
	echo "</section></ul>\n";
	echo "<hr><footer class='site-footer clearfix'><div class='footer'><div class='bottom-nav'><a href='#'>关于</a> - <a href='#'>邮件</a></div><div class='copyright'> 2016 © Wizos</div></div></footer></div>";
	echo "</body></html>";

?>