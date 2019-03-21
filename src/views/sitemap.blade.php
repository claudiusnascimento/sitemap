<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	@foreach($arr as $a)

		<url>
			<loc>{!! $a['loc'] !!}</loc>
			<changefreq>{!! $a['changefreq'] !!}</changefreq>
			<priority>{!! $a['priority'] !!}</priority>
		</url>
	
	@endforeach

</urlset>