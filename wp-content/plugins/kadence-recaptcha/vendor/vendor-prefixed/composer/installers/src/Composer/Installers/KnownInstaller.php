<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\Composer\Installers;

class KnownInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'plugin'    => 'IdnoPlugins/{$name}/',
        'theme'     => 'Themes/{$name}/',
        'console'   => 'ConsolePlugins/{$name}/',
    );
}
