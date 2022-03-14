<?php

namespace dokuwiki\plugin\dev\test;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

/**
 * Does nothing
 */
class NullLogger extends CLI
{
    /** @inheritdoc */
    public function __construct($autocatch = true)
    {
        parent::__construct(false);
    }

    /** @inheritdoc */
    protected function setup(Options $options)
    {
    }

    /** @inheritdoc */
    protected function main(Options $options)
    {
    }

    /** @inheritdoc */
    public function log($level, $message, array $context = array())
    {
        return '';
    }

}
