<?php
/**
 * Register Page CLI Commands
 *
 * Registers wp page pull and wp page push commands
 */

require_once __DIR__ . '/PagePullCommand.php';
require_once __DIR__ . '/PagePushCommand.php';

WP_CLI::add_command('page pull', 'BlankTheme\CLI\PagePullCommand');
WP_CLI::add_command('page push', 'BlankTheme\CLI\PagePushCommand');
