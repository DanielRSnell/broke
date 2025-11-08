/**
 * HTML to Universal Blocks Parser (Server-side)
 *
 * Uses the client-side parser with JSDOM polyfills
 * This ensures server-side and client-side parsers are EXACTLY the same
 */

const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// Load the client-side parser
const clientParserPath = path.resolve(__dirname, '../../../client-html2blocks.js');
const clientParserCode = fs.readFileSync(clientParserPath, 'utf8');

// Load custom element handlers
const customElementsPath = path.resolve(__dirname, '../../custom-elements');
const customHandlers = require(customElementsPath);

/**
 * Parse HTML string to universal/element blocks
 * Uses the client-side parser with JSDOM for server-side compatibility
 * @param {string} html - HTML string to parse
 * @returns {Array} Array of block objects
 */
function html2blocks(html) {
  if (!html || typeof html !== 'string') {
    return [];
  }

  // Create a JSDOM instance with the client parser code
  const dom = new JSDOM(`<!DOCTYPE html><html><body></body></html>`, {
    runScripts: 'outside-only'
  });

  // Inject custom handlers into the JSDOM window so they're available to the parser
  dom.window.__customHandlers = customHandlers;

  // Execute the client-side parser in the JSDOM context
  dom.window.eval(clientParserCode);

  // Call the html2blocks function that's now in the JSDOM window
  return dom.window.html2blocks(html);
}


/**
 * Generate WordPress block markup from block data
 * @param {Array} blocks - Array of block objects
 * @param {Object} options - Options for markup generation
 * @param {boolean} options.doubleEscape - Whether to double-escape backslashes for WordPress (default: true for content, false for pages)
 * @returns {string} WordPress block markup
 */
function generateBlockMarkup(blocks, options = {}) {
  if (!blocks || !Array.isArray(blocks)) {
    return '';
  }

  const { doubleEscape = true } = options;

  const serializeBlock = block => {
    const { name, attributes, innerBlocks } = block;

    // Serialize attributes to JSON
    let attrsJson = '';
    if (Object.keys(attributes).length > 0) {
      const jsonString = JSON.stringify(attributes);
      // Double-escape backslashes if needed (for markdown/code blocks from content)
      // Pages with SVGs don't need this, content with code blocks does
      attrsJson = ' ' + (doubleEscape ? jsonString.replace(/\\/g, '\\\\') : jsonString);
    }

    // Check if block has inner blocks
    const hasInnerBlocks = innerBlocks && innerBlocks.length > 0;

    if (hasInnerBlocks) {
      // Self-closing comment with inner blocks
      const innerMarkup = innerBlocks.map(serializeBlock).join('\n');
      return `<!-- wp:${name}${attrsJson} -->\n${innerMarkup}\n<!-- /wp:${name} -->`;
    } else {
      // Self-closing block
      return `<!-- wp:${name}${attrsJson} /-->`;
    }
  };

  return blocks.map(serializeBlock).join('\n\n');
}

module.exports = {
  html2blocks,
  generateBlockMarkup,
};
