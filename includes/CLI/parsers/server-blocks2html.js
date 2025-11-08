#!/usr/bin/env node

/**
 * Server-side Blocks to HTML Parser
 *
 * Uses JSDOM to run client-blocks2html.js in a Node.js environment
 * This maintains 100% integrity between client and server parsing
 */

const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// Read blocks JSON from stdin or file argument
const input = process.argv[2];
let blocksJson;

if (input && input !== '-') {
  // Read from file
  blocksJson = fs.readFileSync(input, 'utf8');
} else {
  // Read from stdin
  const chunks = [];
  process.stdin.setEncoding('utf8');

  process.stdin.on('data', (chunk) => {
    chunks.push(chunk);
  });

  process.stdin.on('end', () => {
    blocksJson = chunks.join('');
    processBlocks(blocksJson);
  });

  return;
}

processBlocks(blocksJson);

function processBlocks(blocksJson) {
  try {
    // Parse input blocks
    const blocks = JSON.parse(blocksJson);

    // Create a JSDOM environment
    const dom = new JSDOM('<!DOCTYPE html><html><body></body></html>', {
      runScripts: 'outside-only'
    });

    const { window } = dom;
    global.window = window;

    // Load the client-blocks2html.js script
    const clientParserPath = path.join(__dirname, '../client-blocks2html.js');
    const clientParserCode = fs.readFileSync(clientParserPath, 'utf8');

    // Execute the client parser in the JSDOM context
    dom.window.eval(clientParserCode);

    // Use the blocks2html function from the client parser
    const html = dom.window.universal.blocks2html(blocks);

    // Output the HTML
    process.stdout.write(html);

    // Clean up
    delete global.window;
    dom.window.close();

  } catch (error) {
    console.error('Error processing blocks:', error.message);
    process.exit(1);
  }
}
