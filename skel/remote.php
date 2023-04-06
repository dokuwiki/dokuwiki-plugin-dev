<?php
/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends \dokuwiki\Extension\RemotePlugin
{

    /**
     * Example function
     *
     * All public methods become automatically part of the API
     */
    public function myExample($id)
    {
        // FIXME handle security in your method!
        $id = cleanID($id);
        if (auth_quickaclcheck($id) < AUTH_READ) {
            throw new RemoteAccessDeniedException('You are not allowed to read this file', 111);
        }

        return 'example';
    }
}

