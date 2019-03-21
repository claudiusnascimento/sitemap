
<?php 
	
	Route::get(config('causitemap.sitemap_url', 'sitemap.xml'), array('as' => 'claudiusnascimento.sitemap.xml', 'uses' => 'SitemapController@sitemap'));