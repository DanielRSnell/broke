<?php
/**
 * Register Pattern CLI Commands
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

require_once __DIR__ . '/PatternExtractCommand.php';
require_once __DIR__ . '/PatternPushCommand.php';

WP_CLI::add_command('pattern extract', 'BlankTheme\CLI\PatternExtractCommand');
WP_CLI::add_command('pattern push', 'BlankTheme\CLI\PatternPushCommand');
