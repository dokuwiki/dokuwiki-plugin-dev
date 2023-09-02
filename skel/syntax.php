<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends SyntaxPlugin
{
    /** @inheritDoc */
    public function getType()
    {
        return 'FIXME: container|baseonly|formatting|substition|protected|disabled|paragraphs';
    }

    /** @inheritDoc */
    public function getPType()
    {
        return 'FIXME: normal|block|stack';
    }

    /** @inheritDoc */
    public function getSort()
    {
        return FIXME;
    }

    /** @inheritDoc */
    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('<FIXME>', $mode, '@@SYNTAX_COMPONENT_NAME@@');
//        $this->Lexer->addEntryPattern('<FIXME>', $mode, '@@SYNTAX_COMPONENT_NAME@@');
    }

//    /** @inheritDoc */
//    public function postConnect()
//    {
//        $this->Lexer->addExitPattern('</FIXME>', '@@SYNTAX_COMPONENT_NAME@@');
//    }

    /** @inheritDoc */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $data = [];

        return $data;
    }

    /** @inheritDoc */
    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }

        return true;
    }
}
