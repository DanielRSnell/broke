/**
 * HTML to Pattern CLI - Main Module
 */

const { html2blocks, generateBlockMarkup } = require('./parser');
const {
  convertHTMLToPattern,
  generatePatternMetadata,
  generatePatternFile,
} = require('./pattern-generator');

module.exports = {
  // Parser functions
  html2blocks,
  generateBlockMarkup,

  // Pattern generator functions
  convertHTMLToPattern,
  generatePatternMetadata,
  generatePatternFile,
};
