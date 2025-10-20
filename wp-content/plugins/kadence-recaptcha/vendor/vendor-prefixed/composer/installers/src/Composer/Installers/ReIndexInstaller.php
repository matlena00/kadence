<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\Composer\Installers;

class ReIndexInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'theme'     => 'themes/{$name}/',
        'plugin'    => 'plugins/{$name}/'
    );
}
