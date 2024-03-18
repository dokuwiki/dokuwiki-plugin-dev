<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\EventHandler;
use dokuwiki\Extension\Event;

/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends ActionPlugin
{
    /** @inheritDoc */
    public function register(EventHandler $controller)
    {
@@REGISTER@@
    }
@@HANDLERS@@
}
