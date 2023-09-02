<?php

use dokuwiki\Extension\AdminPlugin;

/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends AdminPlugin
{
    /** @inheritDoc */
    public function handle()
    {
        // FIXME data processing
    }

    /** @inheritDoc */
    public function html()
    {
        // FIXME render output
        echo '<h1>' . $this->getLang('menu') . '</h1>';
    }
}
