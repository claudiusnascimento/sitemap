<?php 

namespace ClaudiusNascimento\Sitemap;

use App\Http\Controllers\Controller;

class SitemapController extends Controller {


	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	
	public function sitemap()
	{

		return \Sitemap::generate();
	}

}