<?php
/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (Renderer Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends Doku_Renderer_xhtml
{

    /**
     * @inheritDoc
     * Make available as XHTML replacement renderer
     */
    public function canRender($format)
    {
        if ($format == 'xhtml') {
            return true;
        }
        return false;
    }

    // FIXME override any methods of Doku_Renderer_xhtml here
}

