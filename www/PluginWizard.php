<?php

namespace dokuwiki\plugin\dev\www;

use dokuwiki\plugin\dev\Skeletor;
use splitbrain\PHPArchive\ArchiveIllegalCompressionException;
use splitbrain\PHPArchive\ArchiveIOException;
use splitbrain\PHPArchive\Zip;

class PluginWizard
{
    /**
     * @throws ArchiveIllegalCompressionException
     * @throws ArchiveIOException
     */
    public function handle()
    {
        if (!isset($_POST['base'])) return null;

        $base = preg_replace('/[^a-z0-9]/i', '', $_POST['base']);

        $skeletor = new Skeletor(
            Skeletor::TYPE_PLUGIN,
            $base,
            $_POST['desc'] ?: '',
            $_POST['author'] ?: '',
            $_POST['mail'] ?: '',
            '',
            $_POST['url'] ?: ''
        );
        $skeletor->addBasics();

        if (!empty($_POST['use_lang'])) $skeletor->addLang();
        if (!empty($_POST['use_conf'])) $skeletor->addConf();
        if (!empty($_POST['use_test'])) $skeletor->addTest();

        foreach ($_POST['components'] as $id) {
            [$type, , , $component] = array_pad(explode('_', $id, 4), 4, '');
            if (isset($_POST['options'][$id])) {
                $options = array_filter(array_map('trim', explode(',', $_POST['options'][$id])));
            } else {
                $options = [];
            }

            $skeletor->addComponent($type, $component, $options);
        }

        $zip = new Zip();
        $zip->setCompression(9);
        $zip->create();
        foreach ($skeletor->getFiles() as $file => $content) {
            $zip->addData($base . '/' . $file, $content);
        }

        return $zip->getArchive();
    }


    /**
     * Get options for all available plugin types
     */
    public function getPluginTypes()
    {
        return Skeletor::PLUGIN_TYPES;
    }

    public function getEvents()
    {
        return array_map('trim', file(__DIR__ . '/../events.txt', FILE_IGNORE_NEW_LINES));
    }
}
