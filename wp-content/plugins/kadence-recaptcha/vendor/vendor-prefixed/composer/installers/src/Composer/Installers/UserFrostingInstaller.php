<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\Composer\Installers;

class UserFrostingInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'sprinkle' => 'app/sprinkles/{$name}/',
    );
}
