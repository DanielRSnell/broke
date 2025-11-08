/**
 * Content Custom Element Handler
 *
 * Handles bidirectional conversion between <Content> HTML elements
 * and core/post-content WordPress blocks
 */

module.exports = {
  // Custom element tag name
  tagName: 'Content',

  // WordPress core block name
  blockName: 'core/post-content',

  /**
   * Convert HTML <Content> element to core/post-content block
   * @param {Element} element - DOM element
   * @returns {Object} Block object
   */
  toBlock: (element) => {
    const attributes = {};

    // Extract className
    if (element.className) {
      attributes.className = element.className;
    }

    return {
      name: 'core/post-content',
      attributes,
      innerBlocks: []
    };
  },

  /**
   * Convert core/post-content block to HTML <Content> element
   * @param {Object} block - Block object
   * @returns {string} HTML string
   */
  toHTML: (block) => {
    const attrs = block.attributes || {};
    const parts = ['<Content'];

    if (attrs.className) {
      parts.push(` class="${escapeAttribute(attrs.className)}"`);
    }

    parts.push('></Content>');
    return parts.join('');
  }
};

/**
 * Escape HTML attribute value
 * @param {string} value - Attribute value
 * @returns {string} Escaped value
 */
function escapeAttribute(value) {
  if (typeof value !== 'string') {
    value = String(value);
  }
  return value
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#x27;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}
