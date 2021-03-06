<?php

namespace Avalanche\Bundle\ImagineBundle\Routing;

use Avalanche\Bundle\ImagineBundle\Exception\UnsupportedOptionException;
use Avalanche\Bundle\ImagineBundle\Imagine\ParamResolver;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ImagineLoader extends Loader
{
    private $cacheParams;
    private $filters;

    public function __construct(ParamResolver $params, array $filters = [])
    {
        $this->cacheParams = $params;
        $this->filters     = $filters;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'imagine';
    }

    public function load($resource, $type = null)
    {
        $requirements = array('_method' => 'GET', 'filter' => '[A-z0-9_\-]*', 'path' => '.+');
        $defaults     = array('_controller' => 'imagine.controller:filter');
        $routes       = new RouteCollection();
        if (count($this->filters) > 0) {
            foreach ($this->filters as $filter => $options) {
                if (isset($options['path'])) {
                    $pattern = '/'.trim($options['path'], '/').'/{path}';
                } else {
                    $pattern = '/imgcache/{filter}/{path}';
                }
                $routes->add('_imagine_'.$filter, new Route(
                    $pattern,
                    array_merge( $defaults, array('filter' => $filter)),
                    $requirements
                ));
            }
        }
        return $routes;
    }
}
