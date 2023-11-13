<?php

namespace dokuwiki\plugin\dev;

use dokuwiki\HTTP\DokuHTTPClient;
use splitbrain\phpcli\CLI;

/**
 * Download and clean SVG icons
 */
class SVGIcon
{
    public const SOURCES = [
        'mdi' => "https://raw.githubusercontent.com/Templarian/MaterialDesign/master/svg/%s.svg",
        'fab' => "https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/svgs/brands/%s.svg",
        'fas' => "https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/svgs/solid/%s.svg",
        'fa' => "https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/svgs/regular/%s.svg",
        'twbs' => "https://raw.githubusercontent.com/twbs/icons/main/icons/%s.svg",
    ];

    /** @var CLI for logging */
    protected $logger;

    /** @var bool keep the SVG namespace for when the image is not used in embed? */
    protected $keepns = false;

    /**
     * @throws \Exception
     */
    public function __construct(CLI $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Call before cleaning to keep the SVG namespace
     *
     * @param bool $keep
     */
    public function keepNamespace($keep = true)
    {
        $this->keepns = $keep;
    }

    /**
     * Download and save a remote icon
     *
     * @param string $ident prefixed name of the icon
     * @param string $save
     * @return bool
     * @throws \Exception
     */
    public function downloadRemoteIcon($ident, $save = '')
    {
        $icon = $this->remoteIcon($ident);
        $svgdata = $this->fetchSVG($icon['url']);
        $svgdata = $this->cleanSVG($svgdata);

        if (!$save) {
            $save = $icon['name'] . '.svg';
        }

        io_makeFileDir($save);
        $ok = io_saveFile($save, $svgdata);
        if ($ok) $this->logger->success('saved ' . $save);
        return $ok;
    }

    /**
     * Clean an existing SVG file
     *
     * @param string $file
     * @return bool
     * @throws \Exception
     */
    public function cleanSVGFile($file)
    {
        $svgdata = io_readFile($file, false);
        if (!$svgdata) {
            throw new \Exception('Failed to read ' . $file);
        }

        $svgdata = $this->cleanSVG($svgdata);
        $ok = io_saveFile($file, $svgdata);
        if ($ok) $this->logger->success('saved ' . $file);
        return $ok;
    }

    /**
     * Get info about an icon from a known remote repository
     *
     * @param string $ident prefixed name of the icon
     * @return array
     * @throws \Exception
     */
    public function remoteIcon($ident)
    {
        if (strpos($ident, ':')) {
            [$prefix, $name] = explode(':', $ident);
        } else {
            $prefix = 'mdi';
            $name = $ident;
        }
        if (!isset(self::SOURCES[$prefix])) {
            throw new \Exception("Unknown prefix $prefix");
        }

        $url = sprintf(self::SOURCES[$prefix], $name);

        return [
            'prefix' => $prefix,
            'name' => $name,
            'url' => $url,
        ];
    }

    /**
     * Minify SVG
     *
     * @param string $svgdata
     * @return string
     */
    protected function cleanSVG($svgdata)
    {
        $old = strlen($svgdata);

        // strip namespace declarations FIXME is there a cleaner way?
        $svgdata = preg_replace('/\sxmlns(:.*?)?="(.*?)"/', '', $svgdata);

        $dom = new \DOMDocument();
        $dom->loadXML($svgdata, LIBXML_NOBLANKS);

        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;

        $svg = $dom->getElementsByTagName('svg')->item(0);

        // prefer viewbox over width/height
        if (!$svg->hasAttribute('viewBox')) {
            $w = $svg->getAttribute('width');
            $h = $svg->getAttribute('height');
            if ($w && $h) {
                $svg->setAttribute('viewBox', "0 0 $w $h");
            }
        }

        // remove unwanted attributes from root
        $this->removeAttributes($svg, ['viewBox']);

        // remove unwanted attributes from primitives
        foreach ($dom->getElementsByTagName('path') as $elem) {
            $this->removeAttributes($elem, ['d']);
        }
        foreach ($dom->getElementsByTagName('rect') as $elem) {
            $this->removeAttributes($elem, ['x', 'y', 'rx', 'ry']);
        }
        foreach ($dom->getElementsByTagName('circle') as $elem) {
            $this->removeAttributes($elem, ['cx', 'cy', 'r']);
        }
        foreach ($dom->getElementsByTagName('ellipse') as $elem) {
            $this->removeAttributes($elem, ['cx', 'cy', 'rx', 'ry']);
        }
        foreach ($dom->getElementsByTagName('line') as $elem) {
            $this->removeAttributes($elem, ['x1', 'x2', 'y1', 'y2']);
        }
        foreach ($dom->getElementsByTagName('polyline') as $elem) {
            $this->removeAttributes($elem, ['points']);
        }
        foreach ($dom->getElementsByTagName('polygon') as $elem) {
            $this->removeAttributes($elem, ['points']);
        }

        // remove comments see https://stackoverflow.com/a/60420210
        $xpath = new \DOMXPath($dom);
        for ($els = $xpath->query('//comment()'), $i = $els->length - 1; $i >= 0; $i--) {
            $els->item($i)->parentNode->removeChild($els->item($i));
        }

        // readd namespace if not meant for embedding
        if ($this->keepns) {
            $svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        }

        $svgdata = $dom->saveXML($svg);
        $new = strlen($svgdata);

        $this->logger->info(sprintf('Minified SVG %d bytes -> %d bytes (%.2f%%)', $old, $new, $new * 100 / $old));
        if ($new > 2048) {
            $this->logger->warning('%d bytes is still too big for standard inlineSVG() limit!');
        }
        return $svgdata;
    }

    /**
     * Remove all attributes except the given keepers
     *
     * @param \DOMNode $element
     * @param string[] $keep
     */
    protected function removeAttributes($element, $keep)
    {
        $attributes = $element->attributes;
        for ($i = $attributes->length - 1; $i >= 0; $i--) {
            $name = $attributes->item($i)->name;
            if (in_array($name, $keep)) continue;
            $element->removeAttribute($name);
        }
    }

    /**
     * Fetch the content from the given URL
     *
     * @param string $url
     * @return string
     * @throws \Exception
     */
    protected function fetchSVG($url)
    {
        $http = new DokuHTTPClient();
        $svg = $http->get($url);

        if (!$svg) {
            throw new \Exception("Failed to download $url: " . $http->status . ' ' . $http->error);
        }

        return $svg;
    }
}
