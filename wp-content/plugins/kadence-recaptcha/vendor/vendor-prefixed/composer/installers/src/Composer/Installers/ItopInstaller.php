<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\Composer\Installers;

class ItopInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'extension'    => 'extensions/{$name}/',
    );
}
