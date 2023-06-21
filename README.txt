=== WP Shieldon - WordPress Firewall ===
Contributors: terrylin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40terryl.in&item_name=WordPress+Plugin+-+WP+Shieldon&currency_code=USD&source=url
Tags: anti-scriping, security, firewall, brute-force, xss-protection
Requires at least: 4.7
Tested up to: 6.2.2
Stable tag: 2.0.2
Requires PHP: 7.1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html

== Description ==

WP Shieldon is a WordPress security plugin based on [Shieldon](https://github.com/terrylinooo/shieldon) library, a Web Application Firewall (WAF) for PHP.

When users or robots try to view many of your web pages within a short period of time, they will be temporarily banned. They can get unbanned by solving a Captcha.

You can visit the plugin author - [Terry L.](https://terryl.in)'s blog and try reloading the pages several times to see how this plugin works. You can also try Terry's login page to find it protected. For more information about Shieldon, please visit [shieldon.io](https://shieldon.io/en/)

Please note that there are three important things you must understand before using WP Shieldon:

1. WP Shieldon is not for beginners.
2. Turn the `Trusted Bot` component on to allow search engine crawlers such as Google, Bing, Yahoo, and others to smoothly crawl your website.

== Open Source Code ==

Plugin:
[https://github.com/terrylinooo/wp-shieldon](https://github.com/terrylinooo/wp-shieldon)

Core library:
[https://github.com/terrylinooo/shieldon](https://github.com/terrylinooo/shieldon)

== Features ==

- Realtime statistics - See who are browsing your website and their status.
- Beautiful and detailed statistics and dashboard.
- Block bad bots by default - Backlink crawlers, copyright crawlers and WayBack machine bot.
- IP manager - Block signle IP or IP range as you want. (IPv6 supported)
- Online session control - You can limit just how many visitors browsing your website. Good for webmasters whose blog is hosted on a share hosting.
- SEO friendly - You can allow popular search engines such as Google, Bing, Yahoo and others, put them in the whitelist.
- XML RPC, Login, Signup page protection.
- Multiple data drivers - Redis, SQLite, File system, MySQL.
- Multiple CAPTCHA modules - Google reCAPTCHA v2, v3 and Image CAPTCHA.
- XSS Protection.
- Page authentication.
- Many others you can find by yourself.

Check out my other WordPress works here:

- [Markdown Editor](https://wordpress.org/plugins/wp-githuber-md/) - WP Githuber MD - an all in one Markdown editor.
- [Cache Master](https://wordpress.org/plugins/cache-master/) - WordPress cache plugin.
- [Mynote Theme](https://wordpress.org/themes/mynote/) - Theme for programmers.

== Screenshots ==

1. Settings.
2. IP manager.
3. Statistics and dashboard.
4. Rule table.
5. IP log table.
6. Session table.
7. User has reached the limit and asked for CAPTCHA.

== Copyright ==

WP Shieldon, Copyright 2019 TerryL.in
WP Shieldon is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Changelog ==

= 1.0.0

* First release.

= 1.0.1

* Fix Redis check.

= 1.0.2

* Add CDN setting.

= 1.0.3

* Fix setting URL in plugin page.
* Fix SQLite check.
* Fix daemon Enable button not working.

= 1.1.0

* Add dashboard
* Fix passcode not working.

= 1.2.0

* Add report - IP log table.
* Add report - Session table.
* Add report - Rule table.

= 1.2.1

* Fix variable type error.

= 1.2.2

* Add new tabs for more period data on dashboard.
* Fix component loading issue.

= 1.2.3

* Update readme.
* Fix i10n issues.
* Imporve statistics pages.

= 1.3.0

* Add IP management on IP rule table page.

= 1.3.1

* Fix passcode login issue.

= 1.3.2

* Fix JavaScript conflicts.

= 1.3.3

* Fix passcode login issue.
* Improve performance.

= 1.3.4

* Exclude password-protected posts' form.
* Update uninstall.php

= 1.4.0

- Update Shieldon core.
- Add feature - XSS protection.
- Add feature - WWW-authenticte page protection.
- Add setting page - Overview.

= 1.4.1

- Update localization strings. (zh_TW, zh_CN)
- Fix typo.

= 1.4.2

- Fix issue #6 - Issue with 'Save Draft' feature.

= 1.4.3

- Update Shieldon core up to 0.1.7
- Update translation strings.

= 1.5.0 (WordCamp Taipei version)

- Assign language code to dialog UI.
- Update translation strings.

= 1.6.0

- Update Shieldon kernel.
- Add options in settings allowing to avoid conflicts with some WordPress core functions.
- Add an option for switching Action Logger.
- Add import/export settings feature.
- Add page - operation status.
- Update translation strings.

= 1.6.1

- Fix Website Healthy Check issue.
- Add an option for allowing only logged-in users can accesss REST API.
- Add an option for disabling XML-RPC.

= 1.6.2

- Add Facebook and Twitter bots into whitelist in Trusted Bot component.
- Add current website's IP address into whitelist by default.
- Prevent automatic favicon requests.

= 1.6.3

- Ignore AJAX calls.
- Fix issue #3 - Notice appearing on PHP 7.4

= 2.0.0

- Upgrade Sheidlon core from 0.1.7 to 2.1.0
- Refactor code to fit with Shieldon 2.1.0
- Test up to WordPress 6.2.2 and PHP 8.2.5

= 2.0.2

- Check and set the reverse DNS for IP address.
