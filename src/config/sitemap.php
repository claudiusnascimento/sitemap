<?php 

return [
	
	'sitemap_url' => 'sitemap.xml',

	'excepts_routes_names' => [],

	'default_priority' => 0.8,

	'change_freq_options' => [

		'always',
		'hourly',
		'daily',
		'weekly',
		'monthly',
		'anual',
		'never'
	],

	'change_freq_default' => 'monthly',

	'dinamics' => [

		/*'categories' => [

			'base_url' => ['e-commerce/categoria'],
			'model' => 'App\Models\ProductCategory',
			'slug' => 'slug',

			'where' => [
				['active', '=', 1]
			],

			'whereHas' => [

				'relationships' => [

					'products' => [

						'where' => [
							['active', '=', 1]
						]
					]
				]
			]

		],*/

		/* generally used when slug needs a more elaborated method instead a simple slug attribute */

		/*'posts' => [

			'manually' => 'getSitemapUrl',
			'model' => 'App\Models\Post'

		],*/

	]

];