<?php
/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends \dokuwiki\Extension\ActionPlugin
{

    /** @inheritDoc */
    public function register(Doku_Event_Handler $controller)
    {
@@REGISTER@@
    }

    /**
     * FIXME Event handler for
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  optional parameter passed when event was registered
     * @return void
     */
@@HANDLERS@@
}

