[![CS](https://github.com/ProgressPlanner/comment-free-zone/actions/workflows/cs.yml/badge.svg)](https://github.com/ProgressPlanner/comment-free-zone/actions/workflows/cs.yml)
[![PHPStan](https://github.com/ProgressPlanner/comment-free-zone/actions/workflows/phpstan.yml/badge.svg)](https://github.com/ProgressPlanner/comment-free-zone/actions/workflows/phpstan.yml)
[![Lint](https://github.com/ProgressPlanner/comment-free-zone/actions/workflows/lint.yml/badge.svg)](https://github.com/ProgressPlanner/comment-free-zone/actions/workflows/lint.yml)

[![Try Comment Free Zone on the WordPress playground](https://img.shields.io/badge/Try%20Comment%20Free%20Zone%20on%20the%20WordPress%20Playground-%23117AC9.svg?style=for-the-badge&logo=WordPress&logoColor=ddd)](https://playground.wordpress.net/#{"landingPage":"/wp-admin/","features":{"networking":true},"login":true,"plugins":["https://github-proxy.com/proxy/?repo=ProgressPlanner/comment-free-zone"],"steps":[{"step":"defineWpConfigConsts","consts":{"IS_PLAYGROUND_PREVIEW":true}}]})

![Comment Free Zone](/.wordpress-org/github_banner_cfz_pp.png)

# Comment Free Zone

Disable comments, pingbacks, trackbacks, and comment-related UI across your entire WordPress site.

Comment Free Zone is a no-settings WordPress plugin for sites that do not want comments at all. Activate it once and it removes comment functionality from the admin, the front end, feeds, REST API responses, and block output.

## What it does

After activation, the plugin:

- removes the **Comments** menu and the discussion settings screen
- removes comment links from the admin bar
- removes the comments column from page and post list tables
- disables comments and pingbacks on all post types
- removes comment and trackback support from post types that support them
- removes comment blocks from the block editor and Site Editor output
- removes the comment meta box from the editor experience
- disables comment RSS feeds
- removes comment endpoints and comment-related fields from relevant REST API responses
- disables outgoing pings and incoming pingbacks

## Why use it

Use Comment Free Zone if you want to:

- run a brochure site, company site, or documentation site without comments
- simplify the WordPress admin for clients or editors
- reduce moderation overhead and comment spam surface area
- remove comment-related clutter from classic and block-based experiences

## Installation

1. Upload the plugin to `/wp-content/plugins/`, or install it through **Plugins → Add New**.
2. Activate **Comment Free Zone**.
3. That's it — there are no settings to configure.

## Support

- For bugs and feature requests, open an issue in this repository.
- For security reports, use the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/comment-free-zone).

## Development

This repository includes CI workflows for coding standards, linting, and static analysis.

## License

GPL-3.0+
