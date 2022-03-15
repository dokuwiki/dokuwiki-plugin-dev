<?php

namespace dokuwiki\plugin\dev;

use splitbrain\phpcli\CLI;

class LangProcessor
{

    /** @var CLI */
    protected $logger;

    /** @var array The language keys used in the code */
    protected $codeKeys;

    /** @var array The language keys matching the configuration settings */
    protected $settingKeys;

    public function __construct(CLI $logger)
    {
        $this->logger = $logger;
        $this->codeKeys = $this->findLanguageKeysInCode();
        $this->settingKeys = $this->findLanguageKeysInSettings();
    }

    /**
     * Remove the obsolete strings from the given lang.php
     *
     * @param string $file
     * @return void
     */
    public function processLangFile($file)
    {
        $lang = [];
        include $file;

        $drop = array_diff_key($lang, $this->codeKeys);
        if (isset($found['js']) && isset($lang['js'])) {
            $drop['js'] = array_diff_key($lang['js'], $found['js']);
            if (!count($drop['js'])) unset($drop['js']);
        }

        foreach ($drop as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subvalue) {
                    $this->removeLangKey($file, $subkey, $key);
                }
            } else {
                $this->removeLangKey($file, $key);
            }
        }
    }

    /**
     * Remove obsolete string from the given settings.php
     *
     * @param string $file
     * @return void
     */
    public function processSettingsFile($file)
    {
        $lang = [];
        include $file;

        $drop = array_diff_key($lang, $this->settingKeys);
        foreach ($drop as $key => $value) {
            $this->removeLangKey($file, $key);
        }
    }

    /**
     * Remove the given key from the given language file
     *
     * @param string $file
     * @param string $key
     * @param string $sub
     * @return void
     */
    protected function removeLangKey($file, $key, $sub = '')
    {
        $q = '[\'"]';

        if ($sub) {
            $re = '/\$lang\[' . $q . $sub . $q . '\]\[' . $q . $key . $q . '\]/';
        } else {
            $re = '/\$lang\[' . $q . $key . $q . '\]/';
        }

        if (io_deleteFromFile($file, $re, true)) {
            $this->logger->success('{key} removed from {file}', [
                'key' => $sub ? "$sub.$key" : $key,
                'file' => $file,
            ]);
        }
    }

    /**
     * @return array
     */
    public function findLanguageKeysInSettings()
    {
        if (file_exists('./conf/metadata.php')) {
            return $this->metaExtract('./conf/metadata.php');
        }
        return [];
    }

    /**
     * Find used language keys in the actual code
     * @return array
     */
    public function findLanguageKeysInCode()
    {
        // get all non-hidden php and js files
        $ite = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator('.', \RecursiveDirectoryIterator::SKIP_DOTS),
                function ($file) {
                    /** @var \SplFileInfo $file */
                    if ($file->isFile() && $file->getExtension() != 'php' && $file->getExtension() != 'js') return false;
                    return $file->getFilename()[0] !== '.';
                }
            )
        );

        $found = [];
        foreach ($ite as $file) {
            /** @var \SplFileInfo $file */
            $path = str_replace('\\', '/', $file->getPathname());
            if (substr($path, 0, 7) == './lang/') continue; // skip language files
            if (substr($path, 0, 9) == './vendor/') continue; // skip vendor files

            if ($file->getExtension() == 'php') {
                $found = array_merge($found, $this->phpExtract($path));
            } elseif ($file->getExtension() == 'js') {
                if (!isset($found['js'])) $found['js'] = [];
                $found['js'] = array_merge($found['js'], $this->jsExtract($path));
            }
        }

        // admin menu entry
        if (is_dir('admin')) {
            $found['menu'] = 'admin/';
        }
        if (file_exists('admin.php')) {
            $found['menu'] = 'admin.php';
        }

        return $found;
    }

    /**
     * Extract language keys from given settings file
     *
     * @param string $file
     * @return array
     */
    public function metaExtract($file)
    {
        $meta = [];
        include $file;

        $found = [];
        foreach ($meta as $key => $info) {
            $found[$key] = $file;

            if (isset($info['_choices'])) {
                foreach ($info['_choices'] as $choice) {
                    $found[$key . '_o_' . $choice] = $file;
                }
            }
        }

        return $found;
    }

    /**
     * Extract language keys from given javascript file
     *
     * @param string $file
     * @return array (key => file:line)
     */
    public function jsExtract($file)
    {
        $sep = '[\[\]\.\'"]+'; // closes and opens brackets and dots - we don't care yet
        $any = '[^\[\]\.\'"]+'; // stuff we don't care for
        $close = '[\]\'"]*'; // closes brackets

        $dotvalue = '\.(\w+)';
        $bracketvalue = '\[[\'"](.*?)[\'"]\]';

        // https://regex101.com/r/uTjHwc/1
        $regex = '/\bLANG' . $sep . 'plugins' . $sep . $any . $close . '(?:' . $dotvalue . '|' . $bracketvalue . ')/';
        // echo "\n\n$regex\n\n";

        return $this->extract($file, $regex);
    }

    /**
     * Extract language keys from given php file
     *
     * @param string $file
     * @return array (key => file:line)
     */
    public function phpExtract($file)
    {
        $regex = '/(?:tpl_getLang|->getLang) ?\((.*?)\)/';
        return $this->extract($file, $regex);
    }

    /**
     * Use the given regex to extract language keys from the given file
     *
     * @param string $file
     * @param string $regex
     * @return array
     */
    private function extract($file, $regex)
    {
        $found = [];
        $lines = file($file);
        foreach ($lines as $lno => $line) {
            if (!preg_match_all($regex, $line, $matches, PREG_SET_ORDER)) {
                continue;
            }
            foreach ($matches as $match) {
                $key = $match[2] ?? $match[1];
                $key = trim($key, '\'"');
                if (!isset($found[$key])) {
                    $found[$key] = $file . ':' . ($lno + 1);
                }

            }
        }

        return $found;
    }
}
