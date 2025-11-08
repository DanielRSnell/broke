/**
 * Part Custom Element Handler
 *
 * Handles bidirectional conversion between <Part> HTML elements
 * and core/template-part WordPress blocks
 */

module.exports = {
  // Custom element tag name
  tagName: 'Part',

  // WordPress core block name
  blockName: 'core/template-part',

  /**
   * Convert HTML <Part> element to core/template-part block
   * @param {Element} element - DOM element
   * @returns {Object} Block object
   */
  toBlock: (element) => {
    const attributes = {};

    // Extract slug attribute
    const slug = element.getAttribute('slug');
    if (slug) {
      attributes.slug = slug;
    }

    // Extract theme attribute
    const theme = element.getAttribute('theme');
    if (theme) {
      attributes.theme = theme;
    }

    // Extract className
    if (element.className) {
      attributes.className = element.className;
    }

    return {
      name: 'core/template-part',
      attributes,
      innerBlocks: []
    };
  },

  /**
   * Convert core/template-part block to HTML <Part> element
   * @param {Object} block - Block object
   * @returns {string} HTML string
   */
  toHTML: (block) => {
    const attrs = block.attributes || {};
    const parts = ['<Part'];

    if (attrs.slug) {
      parts.push(` slug="${escapeAttribute(attrs.slug)}"`);
    }

    if (attrs.theme) {
      parts.push(` theme="${escapeAttribute(attrs.theme)}"`);
    }

    if (attrs.className) {
      parts.push(` class="${escapeAttribute(attrs.className)}"`);
    }

    parts.push('></Part>');
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
