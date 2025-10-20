=== Kadence CAPTCHA ===
Author: britner, woodardmc
Tags: reCAPTCHA, comments antispam, antispam, comments reCAPTCHA, antispam protection, google reCAPTCHA, comments form, secure form, security, antispam RTL, RTL Language Support
Requires at least: 6.0.0
Tested up to: 6.7
Stable tag: 1.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Googles reCAPTCHA or Cloudflare Turnstile to WP comment forms, login forms, registration forms, woocommerce reviews, checkout, etc.

== Description ==

Adds Googles reCAPTCHA or Cloudflare Turnstile to WP comment forms, login forms, registration forms, woocommerce reviews, checkout, etc.

== Frequently Asked Questions ==

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the
Kadence CAPTCHA plugin through the Patchstack
Vulnerability Disclosure Program https://patchstack.com/database/vdp/kadence-recaptcha. The
Patchstack team will assist you with verification, CVE assignment, and
notify the developers of this plugin.

== Security Policy ==

= Reporting Security Bugs =

Please report security bugs found in the
Kadence CAPTCHA plugin's source code through the
Patchstack Vulnerability Disclosure
Program https://patchstack.com/database/vdp/kadence-recaptcha. The Patchstack team will
assist you with verification, CVE assignment, and notify the
developers of this plugin.

== Changelog ==

== 1.3.5 | 6th January 2025 ==
* Add: Support for block checkout.
* Add: Protection for store orders API endpoint.

== 1.3.4 | 11th December 2024 ==
* Update: Allow CAPTCHA to work in WC checkout login modal.
* Fix: CAPTCHA labels.

== 1.3.3 | 12th November 2024 ==
* Fix: Recaptcha v2 fields not showing on fresh install.

== 1.3.2 | 15th May 2024 ==
* Fix: Issue when multiple Turnstile captcha were on the same page.

== 1.3.1 | 21st December 2023 ==
* Fix: Small fix for turnstile keys with no google keys entered

== 1.3.0 | 16th January 2023 ==
* Add: Option to validate site and secret keys from settings page.
* Add: Cloudflare Turnstile support
* Update: Language around some settings to be more captcha agnostic
* Fix: Small fix for select input arrows

== 1.2.3 | 20th October 2022 ==
* Add: Option to set custom notice in form for V3 when hiding badge.

== 1.2.2 | 27th December 2021 ==
* Add: Option to force a language.
* Fix: Issue with settings panel not showing certain settings disabled.

== 1.2.1 | 30th November 2021 ==
* Add: Option to use recaptcha.net

= 1.2.0 =
* Add: Option to hide v3 reCAPTCHA badge.
* Add: Option to change v3 threshold score.
* Add: Option to place notice on form.
* Update: New Settings Panel.

= 1.1.1 =
* Fix: Issue with lost password reset.

= 1.1.0 =
* Add: V3 support.
* Add: Network settings option through add_filter( 'kadence_recaptcha_network', '__return_true' );

= 1.0.11 =
* Add: Restrict Content Pro Login Form Support.

= 1.0.10 =
* Update: Plugin updater.
* Update: Admin settings framework.
* Add: Woocommerce Lost Password form protection.

= 1.0.9 =
* Update: CSS fix.
* Update: PHP 7.4 issue.

= 1.0.8 =
* Update: Update issue.

= 1.0.7 =
* Add: Checkout Option
* Add: Woocommmerce registration.

= 1.0.6 =
* Add: registration option.

= 1.0.5 =
* Fix: pagebuilder issue.

= 1.0.4 =
* Fix: not turning off issue.

= 1.0.3 =
* Fix: php5.4 issue.

= 1.0.2 =
* Fix: odd cookie names.
* Fix: for non pretty permalink struture.

= 1.0.1 =
* Fix for Better login authentication.

= 1.0.0 =
*initial release
