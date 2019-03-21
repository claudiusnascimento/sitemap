<?php

namespace ClaudiusNascimento\Sitemap;

use Illuminate\Support\Facades\Route;

class Sitemap
{
    protected $arr = [];

    protected $manually;

    /**
     *
     * Generates the sitemap xml
     *
     * @return [type] xml
     */
    public function generate()
    {

        $routes = $this->getSitemapRoutes();

        foreach ($routes as $route) {
            $this->setRouteType($route);
        }

        $content = view('sitemap::sitemap')->withArr($this->arr)->render();

        return response()->make($content, 200)->header('Content-Type', 'application/xml');

    }

    /**
     * Indicates if are dinamic/static route
     *
     * @param [type] Route
     */
    protected function setRouteType($route)
    {
        array_key_exists('dinamic', $route->getAction()) ? $this->setDinamic($route) : $this->setStatic($route);
    }

    /**
     * Get all routes if to use in xml (route contains action 'dinamic')
     *
     * @return [type] RouteCollection
     */
    protected function getSitemapRoutes()
    {
        $all_routes = collect(Route::getRoutes()->getRoutes());

        $excepts = config('sitemap.excepts_routes_names');

        return $all_routes->filter(function ($route) use ($excepts) {

            return array_key_exists('sitemap', $route->getAction()) &&
            !in_array($route->getName(), $excepts) &&
            in_array('GET', $route->methods);

        });
    }

    /**
     * Simple set xml url path
     *
     * @param [type] Route
     */
    public function setStatic($route)
    {

        $priority   = $this->getPriority($route);
        $changefreq = $this->getChangeFreq($route);

        $this->arr[] = [
            'loc'        => url($route->uri),
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }

    /**
     * Load the model in config and set the xml url path
     *
     * @param [type] \Route
     */
    public function setDinamic($route)
    {

        
        $config = config('sitemap.dinamics.' . $route->getAction()['dinamic']);

        if (isset($config['manually'])) {
            //$manually = isset($this->manually) ? $this->manually : new \Cau\Sitemap\Manually;

            $method = $config['manually'];

            $model_m = app()->make($config['model']);

            if (method_exists($model_m, $method)) {
                $model_m->{$method}();
            }

            return false;
        }

        $priority = $this->getPriority($route);

        $model = app()->make($config['model']);

        $query = $model->query();

        /**
         * whereHas query
         */
        if (array_key_exists('whereHas', $config) && is_array($config['whereHas'])) {
            $relations = $config['whereHas']['relationships'];

            foreach ($relations as $method => $relation) {
                if (method_exists($model, $method)) {
                    $query->whereHas($method, function ($query) use ($relation) {

                        foreach ($relation['where'] as $where) {
                            $query->where($where[0], $where[1], $where[2]);
                        }

                    });
                } else {
                    throw new \Exception("The class " . get_class($model) . " doesn't has '" . $method . "' relation", 1);
                }

            }
        }

        /**
         *  where query
         */
        if (array_key_exists('where', $config) && is_array($config['where'])) {

            foreach ($config['where'] as $where) {
                $query->where($where[0], $where[1], $where[2]);
            }
        }

        $result = $query->select($config['slug'])->get();

        $base_url = $config['base_url'];

        $base_url = is_array($base_url) ? $base_url : [$base_url];

        foreach ($result as $res) {

            foreach ($base_url as $key => $b_url) {

                $url = rtrim($config['base_url'][$key]) . '/' . $res->{$config['slug']};

                $this->arr[] = [
                    'loc'        => url($url),
                    'changefreq' => 'monthly',
                    'priority'   => $priority,
                ];
            }

        }
    }

    /**
     * Define the priority url in xml
     *
     * @param  [type] Route
     * @return [type] float
     */
    public function getPriority($route)
    {
        return isset($route->getAction()['priority']) ?
        (float) $route->getAction()['priority'] :
        (float) config('sitemap.default_priority');
    }

    /**
     * Define the <changefreq> tag in xml
     *
     * @param  [type] Route
     * @return [type] string
     */
    public function getChangeFreq($route)
    {
        return isset($route->getAction()['changefreq']) ?
        $route->getAction()['changefreq'] :
        config('sitemap.change_freq_default');
    }

    public function addUrl($url, $changefreq = null, $priority = null)
    {
        if (!$changefreq) {
            $changefreq = config('sitemap.change_freq_default');
        }
        if (!$priority) {
            $priority = (float) config('sitemap.default_priority');
        }

        $this->arr[] = [
            'loc'        => url($url),
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }

}
