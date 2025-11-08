<?php

/**
 * Register WP-CLI template commands
 */

require_once __DIR__ . '/TemplatePullCommand.php';
require_once __DIR__ . '/TemplatePushCommand.php';

WP_CLI::add_command('template pull', 'BlankTheme\CLI\TemplatePullCommand');
WP_CLI::add_command('template push', 'BlankTheme\CLI\TemplatePushCommand');
