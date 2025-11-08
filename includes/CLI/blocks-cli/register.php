<?php

/**
 * Register WP-CLI blocks commands
 */

require_once __DIR__ . '/BlocksToHtmlCommand.php';
require_once __DIR__ . '/HtmlToBlocksCommand.php';

WP_CLI::add_command('blocks:html', 'BlankTheme\CLI\BlocksToHtmlCommand');
WP_CLI::add_command('html:blocks', 'BlankTheme\CLI\HtmlToBlocksCommand');
