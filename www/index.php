<?php
require __DIR__ . '/../vendor/autoload.php';

$WIZ = new dokuwiki\plugin\dev\www\PluginWizard();
try {
    $archive = $WIZ->handle();
    if ($archive) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="plugin.zip"');
        echo $archive;
        exit;
    }
} catch (Exception $ignored) {
    // errors should only happen when the frontend validation is ignored
}

header('Content-Type: text/html; charset=utf-8');
?>
<html lang="en">
<head>
    <title>DokuWiki Plugin Wizard</title>
    <script type="text/javascript">
        const ACTION_EVENTS = <?php echo json_encode($WIZ->getEvents()); ?>;
    </script>

    <link rel="stylesheet" href="style.css"/>
    <link rel="stylesheet" href="awesomplete.css"
    /
</head>
<body>
<main>
    <h1>DokuWiki Plugin Wizard</h1>


    <div class="intro">
        <p>
            This wizard generates a <a href="https://www.dokuwiki.org/devel:plugins">DokuWiki plugin</a>
            skeleton to help you get started with coding your plugin.
            Before using it you should familiarize your self with how plugins in DokuWiki work
            and determine what components your plugin will need.
        </p>

        <p>
            To use it, fill in the general plugin info and add plugin components. Once you're
            done, click "create" and download your plugin skeleton.
        </p>

        <p>
            Alternatively you can also use the <a href="https://www.dokuwiki.org/plugin:dev">dev plugin</a>.
            This plugin will also come in handy when editing and extending your plugin later.
        </p>
    </div>

    <noscript>
        <div class="nojs">
            Sorry, this wizard needs JavaScript to do its magic. It will not work with your
            current setup.
        </div>
    </noscript>


    <form action="index.php" method="post" id="ajax__plugin_wiz">

        <section>
            <div id="plugin_info">
                <h2>Plugin Information</h2>

                <label>
                    <span>Plugin base name:</span>
                    <input type="text" name="base" required="required" pattern="^[a-z0-9]+$"
                           placeholder="yourplugin">
                    <small>(lowercase, no special chars)</small>
                </label>

                <label>
                    <span>A short description of what the plugin does:</span>
                    <input type="text" name="desc" required="required"
                           placeholder="A plugin to flurb the blarg">
                </label>

                <label>
                    <span>Your name:</span>
                    <input type="text" name="author" required="required" placeholder="Jane Doe">
                </label>

                <label>
                    <span>Your E-Mail address:</span>
                    <input type="text" name="mail" required="required" placeholder="jane@example.com">
                </label>

                <label>
                    <span>URL for the plugin:</span>
                    <input type="text" name="url" placeholder="https://www.dokuwiki.org/plugin:yourplugin">
                    <small>(leave empty for default dokuwiki.org location)</small>
                </label>

                <label>
                    <input type="checkbox" name="use_lang" value="1"/>
                    <span>Use localization</span>
                </label>

                <label>
                    <input type="checkbox" name="use_conf" value="1"/>
                    <span>Use configuration</span>
                </label>

                <label>
                    <input type="checkbox" name="use_tests" value="1"/>
                    <span>Use unit tests</span>
                </label>
            </div>

            <div id="plugin_components">
                <h2>Add Plugin Components</h2>

                <label>
                    <span>Type:</span>
                    <select>
                        <?php foreach ($WIZ->getPluginTypes() as $type): ?>
                            <option value="<?php echo $type ?>"><?php echo ucfirst($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Add as a Sub-Component:</span>
                    <input type="text" value="" pattern="^[a-z0-9]+$" placeholder="subcomponent"/>
                    <small>(leave empty to add top level)</small>
                </label>

                <button type="button">Add Component</button>

                <ul id="output"></ul>

            </div>
        </section>

        <button type="submit" name="plugin_wiz_create">Create and Download<br>Plugin Skeleton</button>

    </form>

</main>
<script src="awesomplete.min.js"></script>
<script src="script.js"></script>
</body>
</html>
