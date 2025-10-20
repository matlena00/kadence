<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\Composer\Installers;

class ChefInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'cookbook'  => 'Chef/{$vendor}/{$name}/',
        'role'      => 'Chef/roles/{$name}/',
    );
}
