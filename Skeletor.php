<?php

namespace dokuwiki\plugin\dev;

use RuntimeException;

/**
 * This class holds basic information about a plugin or template and uses the skeleton files to
 * create new plugin or template specific versions of them.
 *
 * This class does not write any files, but only provides the data for the actual file creation.
 */
class Skeletor
{
    // FIXME this may change upstream we may want to update it via github action
    const PLUGIN_TYPES = ['auth', 'admin', 'syntax', 'action', 'renderer', 'helper', 'remote', 'cli'];

    const TYPE_PLUGIN = 'plugin';
    const TYPE_TEMPLATE = 'template';

    protected $type;
    protected $base;
    protected $author;
    protected $desc;
    protected $name;
    protected $email;
    protected $url;
    protected $dir;

    /** @var array The files to be created in the form of [path => content] */
    protected $files = [];

    /**
     * Initialize the skeletor from provided data
     *
     * @param string $type
     * @param string $base
     * @param string $desc
     * @param string $author
     * @param string $email
     * @param string $name
     * @param string $url
     */
    public function __construct($type, $base, $desc, $author, $email, $name = '', $url = '')
    {
        $this->type = $type;
        $this->base = $base;
        $this->desc = $desc;
        $this->author = $author;
        $this->email = $email;
        $this->name = $name ?: ucfirst($base . ' ' . $type);

        if ($type == self::TYPE_PLUGIN) {
            $this->url = $url ?: 'https://www.dokuwiki.org/plugin:' . $base;
            $this->dir = 'lib/plugins/' . $base;
        } else {
            $this->url = $url ?: 'https://www.dokuwiki.org/template:' . $base;
            $this->dir = 'lib/tpl/' . $base;
        }
    }

    /**
     * Create an instance using an existing plugin or template directory
     *
     * @param string $dir
     * @return Skeletor
     */
    static public function fromDir($dir)
    {
        if (file_exists($dir . '/plugin.info.txt')) {
            $type = self::TYPE_PLUGIN;
        } elseif (file_exists($dir . '/template.info.txt')) {
            $type = self::TYPE_TEMPLATE;
        } else {
            throw new RuntimeException('Not a plugin or template directory');
        }

        $data = file($dir . '/' . $type . '.info.txt', FILE_IGNORE_NEW_LINES);
        $data = array_map(function ($item) {
            return array_map('trim', sexplode(' ', $item, 2, ''));
        }, $data);
        $data = array_combine(array_column($data, 0), array_column($data, 1));

        return new self($type, $data['base'], $data['desc'], $data['author'], $data['email'], $data['url']);
    }

    /**
     * Return the files to be created
     *
     * @return array [path => content]
     */
    public function getFiles()
    {
        return $this->files;
    }

    // region content creators

    /**
     * Add the basic files to the plugin
     */
    public function addBasics()
    {
        $this->loadSkeleton('info.txt', $this->type . '.info.txt');
        $this->loadSkeleton('README');
        $this->loadSkeleton('LICENSE');
        $this->loadSkeleton('_gitattributes', '.gitattributes');
    }

    /**
     * Add another component to the plugin
     *
     * @param string $type
     * @param string $component
     */
    public function addComponent($type, $component = '', $options = [])
    {
        if ($this->type !== self::TYPE_PLUGIN) {
            throw new RuntimeException('Components can only be added to plugins');
        }

        if (!in_array($type, self::PLUGIN_TYPES)) {
            throw new RuntimeException('Invalid type ' . $type);
        }

        $plugin = $this->base;

        if ($component) {
            $path = $type . '/' . $component . '.php';
            $class = $type . '_plugin_' . $plugin . '_' . $component;
            $self = 'plugin_' . $plugin . '_' . $component;
        } else {
            $path = $type . '.php';
            $class = $type . '_plugin_' . $plugin;
            $self = 'plugin_' . $plugin;
        }

        if ($type === 'action') {
            $replacements = $this->actionReplacements($options);
        }
        if ($type === 'renderer' && isset($options[0]) && $options[0] === 'Doku_Renderer_xhtml') {
            $type = 'renderer_xhtml'; // different template then
        }

        $replacements['@@PLUGIN_COMPONENT_NAME@@'] = $class;
        $replacements['@@SYNTAX_COMPONENT_NAME@@'] = $self;
        $this->loadSkeleton($type . '.php', $path, $replacements);
    }

    /**
     * Add test framework optionally with a specific test
     *
     * @param string $test Name of the Test to add
     */
    public function addTest($test = '')
    {
        // pick a random day and time for the cron job
        $cron = sprintf(
            '%d %d %d * *',
            random_int(0, 59),
            random_int(0, 23),
            random_int(1, 28)
        );

        $test = ucfirst($test);
        $this->loadSkeleton('.github/workflows/dokuwiki.yml', '', ['@@CRON@@' => $cron]);
        if ($test) {
            $replacements = ['@@TEST@@' => $test];
            $this->loadSkeleton('_test/StandardTest.php', '_test/' . $test . 'Test.php', $replacements);
        } else {
            $this->loadSkeleton('_test/GeneralTest.php');
        }
    }

    /**
     * Add configuration
     *
     * @param bool $translate if true the settings language file will be be added, too
     */
    public function addConf($translate = false)
    {
        $this->loadSkeleton('conf/default.php');
        $this->loadSkeleton('conf/metadata.php');

        if ($translate) {
            $this->loadSkeleton('lang/settings.php', 'lang/en/settings.php');
        }
    }

    /**
     * Add language
     *
     * Currently only english is added, theoretically this could also copy over the keys from an
     * existing english language file.
     *
     * @param bool $conf if true the settings language file will be be added, too
     */
    public function addLang($conf = false)
    {
        $this->loadSkeleton('lang/lang.php', 'lang/en/lang.php');
        if ($conf) {
            $this->loadSkeleton('lang/settings.php', 'lang/en/settings.php');
        }
    }

    // endregion


    /**
     * Prepare the string replacements
     *
     * @param array $replacements override defaults
     * @return array
     */
    protected function prepareReplacements($replacements = [])
    {
        // defaults
        $data = [
            '@@AUTHOR_NAME@@' => $this->author,
            '@@AUTHOR_MAIL@@' => $this->email,
            '@@PLUGIN_NAME@@' => $this->base, // FIXME rename to @@PLUGIN_BASE@@
            '@@PLUGIN_DESC@@' => $this->desc,
            '@@PLUGIN_URL@@' => $this->url,
            '@@PLUGIN_TYPE@@' => $this->type,
            '@@INSTALL_DIR@@' => ($this->type == self::TYPE_PLUGIN) ? 'plugins' : 'tpl',
            '@@DATE@@' => date('Y-m-d'),
        ];

        // merge given overrides
        return array_merge($data, $replacements);
    }

    /**
     * Replacements needed for action components.
     *
     * @param string[] $event Event names to handle
     * @return string[]
     */
    protected function actionReplacements($events = [])
    {
        if (!$events) $events = ['EXAMPLE_EVENT'];

        $register = '';
        $handler = '';

        $template = file_get_contents(__DIR__ . '/skel/action_handler.php');

        foreach ($events as $event) {
            $event = strtoupper($event);
            $fn = 'handle' . str_replace('_', '', ucwords(strtolower($event), '_'));

            $register .= '        $controller->register_hook(\'' . $event .
                '\', \'AFTER|BEFORE\', $this, \'' . $fn . '\');' . "\n";

            $handler .= str_replace(['@@EVENT@@', '@@HANDLER@@'], [$event, $fn], $template);
        }

        return [
            '@@REGISTER@@' => rtrim($register, "\n"),
            '@@HANDLERS@@' => rtrim($handler, "\n"),
        ];
    }

    /**
     * Load a skeleton file, do the replacements and add it to the list of files
     *
     * @param string $skel Skeleton relative to the skel dir
     * @param string $target File name in the final plugin/template, empty for same as skeleton
     * @param array $replacements Non-default replacements to use
     */
    protected function loadSkeleton($skel, $target = '', $replacements = [])
    {
        $replacements = $this->prepareReplacements($replacements);
        if (!$target) $target = $skel;


        $file = __DIR__ . '/skel/' . $skel;
        if (!file_exists($file)) {
            throw new RuntimeException('Skeleton file not found: ' . $skel);
        }
        $content = file_get_contents($file);
        $this->files[$target] = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $content
        );
    }
}
