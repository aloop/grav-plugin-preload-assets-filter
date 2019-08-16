<?php

namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class PreloadAssetsFilterPlugin
 * @package Grav\Plugin
 */
class PreloadAssetsFilterPlugin extends Plugin
{
    /**
     * A list of the generated header strings
     */
    protected $http_headers = [];

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onTwigInitialized' => ['onTwigInitialized', 0],
            'onOutputGenerated' => ['onOutputGenerated', 0]
        ]);
    }

    public function onTwigInitialized(Event $e)
    {
        $this->grav['twig']->twig()->addFilter(
            new \Twig_SimpleFilter('preloadAssets', [$this, 'preloadAssets'])
        );

        $this->grav['twig']->twig()->addFilter(
            new \Twig_SimpleFilter('preloadUrl', [$this, 'preloadUrl'])
        );
    }


    public function onOutputGenerated(Event $e)
    {
        if (count($this->http_headers) > 0) {
            header('Link: ' . implode(',', $this->http_headers), false);
        }
    }

    public function preloadUrl(string $url, string $as = "image", bool $crossorigin = false)
    {
        if (!empty($url)) {
            $this->http_headers[] = $this->formatHeaderString($url, $as, $crossorigin);;
        }

        return $url;
    }

    public function preloadAssets(string $assetsHtml, bool $crossorigin = false)
    {
        if (empty($assetsHtml)) {
            return $assetsHtml;
        }

        try {
            $dom = new \DOMDocument();
            $html = $dom->loadHTML($assetsHtml);

            $scriptEls = $dom->getElementsByTagName('script');
            $linkEls = $dom->getElementsByTagName('link');

            foreach ($scriptEls as $scriptEl) {
                if ($scriptEl->hasAttribute('src')) {
                    $url = $scriptEl->getAttribute('src');

                    if (!empty($url)) {
                        $this->http_headers[] = $this->formatHeaderString($url, 'script', $crossorigin);
                    }
                }
            }

            foreach ($linkEls as $linkEl) {
                if (!$linkEl->hasAttribute('rel')) {
                    continue;
                }

                if ($linkEl->getAttribute('rel') === "stylesheet" && $linkEl->hasAttribute('href')) {
                    $url = $linkEl->getAttribute('href');

                    if (!empty($url)) {
                        $this->http_headers[] = $this->formatHeaderString($url, 'style', $crossorigin);
                    }
                }
            }
        } finally {
            return $assetsHtml;
        }
    }

    protected function formatHeaderString(string $url, string $as, bool $crossorigin)
    {
        return '<' . $url . '>; rel=preload; as=' . $as . ';' . ($crossorigin ?  ' crossorigin;' : '');
    }
}
