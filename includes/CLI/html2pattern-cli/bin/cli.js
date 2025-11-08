#!/usr/bin/env node

/**
 * HTML to Pattern CLI
 * Convert HTML files to WordPress block patterns
 */

const { Command } = require('commander');
const fs = require('fs');
const path = require('path');
const chalk = require('chalk');
const { glob } = require('glob');
const { convertHTMLToPattern } = require('../src/index');

const program = new Command();

program
  .name('html2pattern')
  .description('CLI tool to convert HTML files to WordPress block patterns')
  .version('1.0.0');

/**
 * Convert Command - HTML to PHP Pattern Files
 */
program
  .command('convert')
  .description('Convert HTML files to WordPress PHP pattern files')
  .argument('<input>', 'Input HTML file or directory')
  .option('-o, --output <path>', 'Output directory (default: ./patterns)')
  .option(
    '-p, --pattern <pattern>',
    'Glob pattern for HTML files (default: **/*.html)',
    '**/*.html'
  )
  .option('--namespace <namespace>', 'Pattern namespace (e.g., "mytheme")')
  .option('--categories <categories>', 'Pattern categories (comma-separated)')
  .option('--keywords <keywords>', 'Pattern keywords (comma-separated)')
  .option('--description <description>', 'Pattern description')
  .option('--viewport-width <width>', 'Viewport width for pattern preview', '1280')
  .action(async (input, options) => {
    try {
      const outputDir = options.output || './patterns';
      const stats = fs.statSync(input);

      console.log(chalk.blue('🔄 Converting HTML to PHP Patterns...'));
      console.log(chalk.gray(`Input: ${input}`));
      console.log(chalk.gray(`Output: ${outputDir}\n`));

      // Ensure output directory exists
      if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
      }

      let files = [];

      if (stats.isDirectory()) {
        // Find all HTML files in directory
        const pattern = path.join(input, options.pattern);
        files = await glob(pattern, { nodir: true });
      } else {
        // Single file
        files = [input];
      }

      if (files.length === 0) {
        console.log(chalk.yellow('⚠️  No HTML files found'));
        return;
      }

      let successCount = 0;
      let errorCount = 0;

      // Parse pattern options
      const patternOptions = {
        namespace: options.namespace,
        categories: options.categories ? options.categories.split(',').map(c => c.trim()) : [],
        keywords: options.keywords ? options.keywords.split(',').map(k => k.trim()) : [],
        description: options.description,
        viewportWidth: parseInt(options.viewportWidth) || 1280,
      };

      for (const file of files) {
        try {
          // Read HTML file
          const html = fs.readFileSync(file, 'utf-8');

          // Get filename without extension for pattern name
          const patternName = path.basename(file, '.html');

          // Convert to PHP pattern
          const phpContent = convertHTMLToPattern(html, patternName, patternOptions);

          // Generate output filename
          const relativePath = stats.isDirectory()
            ? path.relative(input, file)
            : path.basename(file);
          const outputFilename = relativePath.replace(/\.html$/, '.php');
          const outputFile = path.join(outputDir, outputFilename);

          // Ensure output subdirectory exists
          const outputSubdir = path.dirname(outputFile);
          if (!fs.existsSync(outputSubdir)) {
            fs.mkdirSync(outputSubdir, { recursive: true });
          }

          // Write PHP file
          fs.writeFileSync(outputFile, phpContent, 'utf-8');

          console.log(
            chalk.green('✓'),
            chalk.gray(path.basename(file)),
            '→',
            chalk.cyan(path.basename(outputFile))
          );
          successCount++;
        } catch (error) {
          console.log(chalk.red('✗'), chalk.gray(path.basename(file)), chalk.red(error.message));
          errorCount++;
        }
      }

      console.log();
      console.log(chalk.green(`✅ Converted ${successCount} file(s)`));
      if (errorCount > 0) {
        console.log(chalk.red(`❌ ${errorCount} error(s)`));
      }
    } catch (error) {
      console.error(chalk.red('Error:'), error.message);
      process.exit(1);
    }
  });

program.parse();
