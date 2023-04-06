<?php

use splitbrain\phpcli\Options;

/**
 * DokuWiki Plugin @@PLUGIN_NAME@@ (CLI Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  @@AUTHOR_NAME@@ <@@AUTHOR_MAIL@@>
 */
class @@PLUGIN_COMPONENT_NAME@@ extends \dokuwiki\Extension\CLIPlugin
{

    /** @inheritDoc */
    protected function setup(Options $options)
    {
        $options->setHelp('FIXME: What does this CLI do?');

        // main arguments
        $options->registerArgument('FIXME:argumentName', 'FIXME:argument description', 'FIXME:required? true|false');

        // options
        // $options->registerOption('FIXME:longOptionName', 'FIXME: helptext for option', 'FIXME: optional shortkey', 'FIXME:needs argument? true|false', 'FIXME:if applies only to subcommand: subcommandName');

        // sub-commands and their arguments
        // $options->registerCommand('FIXME:subcommandName', 'FIXME:subcommand description');
        // $options->registerArgument('FIXME:subcommandArgumentName', 'FIXME:subcommand-argument description', 'FIXME:required? true|false', 'FIXME:subcommandName');
    }

    /** @inheritDoc */
    protected function main(Options $options)
    {
        // $command = $options->getCmd()
        // $arguments = $options->getArgs()
    }

}

