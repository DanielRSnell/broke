<?php
/**
 * Register Content CLI Commands
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

require_once __DIR__ . '/ContentPullCommand.php';
require_once __DIR__ . '/ContentPushCommand.php';

WP_CLI::add_command('content pull', 'BlankTheme\CLI\ContentPullCommand');
WP_CLI::add_command('content push', 'BlankTheme\CLI\ContentPushCommand');
