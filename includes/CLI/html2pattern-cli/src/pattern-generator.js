/**
 * WordPress Pattern Generator
 *
 * Generates WordPress PHP pattern files with proper headers
 */

const path = require('path');
const { html2blocks, generateBlockMarkup } = require('./parser');

/**
 * Generate pattern metadata from filename and options
 * @param {string} filename - Pattern filename (without extension)
 * @param {Object} options - Pattern options
 * @returns {Object} Pattern metadata
 */
function generatePatternMetadata(filename, options = {}) {
  // Convert filename to title (e.g., "hero-section" -> "Hero Section")
  const title = options.title || filename.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

  // Generate slug from filename
  const slug = options.slug || filename.toLowerCase().replace(/[^a-z0-9-]/g, '-');

  return {
    title,
    slug: options.namespace ? `${options.namespace}/${slug}` : slug,
    description: options.description || '',
    categories: options.categories || [],
    keywords: options.keywords || [],
    viewportWidth: options.viewportWidth || 1280,
    blockTypes: options.blockTypes || [],
    postTypes: options.postTypes || [],
    inserter: options.inserter !== false,
  };
}

/**
 * Generate PHP pattern file content
 * @param {Object} metadata - Pattern metadata
 * @param {string} blockMarkup - WordPress block markup
 * @returns {string} PHP file content
 */
function generatePatternFile(metadata, blockMarkup) {
  const lines = ['<?php'];
  lines.push('/**');
  lines.push(` * Title: ${metadata.title}`);
  lines.push(` * Slug: ${metadata.slug}`);

  if (metadata.description) {
    lines.push(` * Description: ${metadata.description}`);
  }

  if (metadata.categories && metadata.categories.length > 0) {
    lines.push(` * Categories: ${metadata.categories.join(', ')}`);
  }

  if (metadata.keywords && metadata.keywords.length > 0) {
    lines.push(` * Keywords: ${metadata.keywords.join(', ')}`);
  }

  if (metadata.viewportWidth) {
    lines.push(` * Viewport Width: ${metadata.viewportWidth}`);
  }

  if (metadata.blockTypes && metadata.blockTypes.length > 0) {
    lines.push(` * Block Types: ${metadata.blockTypes.join(', ')}`);
  }

  if (metadata.postTypes && metadata.postTypes.length > 0) {
    lines.push(` * Post Types: ${metadata.postTypes.join(', ')}`);
  }

  lines.push(` * Inserter: ${metadata.inserter ? 'true' : 'false'}`);
  lines.push(' */');
  lines.push('?>');

  // Add block markup
  lines.push(blockMarkup);

  return lines.join('\n');
}

/**
 * Convert HTML to PHP pattern file
 * @param {string} html - HTML content
 * @param {string} filename - Pattern filename (without extension)
 * @param {Object} options - Pattern options
 * @returns {string} PHP pattern file content
 */
function convertHTMLToPattern(html, filename, options = {}) {
  // Parse HTML to blocks
  const blocks = html2blocks(html);

  // Convert blocks to WordPress markup
  const blockMarkup = generateBlockMarkup(blocks);

  // Generate metadata
  const metadata = generatePatternMetadata(filename, options);

  // Generate PHP file
  return generatePatternFile(metadata, blockMarkup);
}

module.exports = {
  convertHTMLToPattern,
  generatePatternMetadata,
  generatePatternFile,
};
