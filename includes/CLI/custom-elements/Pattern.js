/**
 * Pattern Custom Element Handler
 *
 * Handles bidirectional conversion between <Pattern> HTML elements
 * and core/pattern WordPress blocks
 */

module.exports = {
  // Custom element tag name
  tagName: 'Pattern',

  // WordPress core block name
  blockName: 'core/pattern',

  /**
   * Convert HTML <Pattern> element to core/pattern block
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

    // Extract category attribute (optional)
    const category = element.getAttribute('category');
    if (category) {
      attributes.category = category;
    }

    // Extract className
    if (element.className) {
      attributes.className = element.className;
    }

    return {
      name: 'core/pattern',
      attributes,
      innerBlocks: []
    };
  },

  /**
   * Convert core/pattern block to HTML <Pattern> element
   * @param {Object} block - Block object
   * @returns {string} HTML string
   */
  toHTML: (block) => {
    const attrs = block.attributes || {};
    const parts = ['<Pattern'];

    if (attrs.slug) {
      parts.push(` slug="${escapeAttribute(attrs.slug)}"`);
    }

    if (attrs.category) {
      parts.push(` category="${escapeAttribute(attrs.category)}"`);
    }

    if (attrs.className) {
      parts.push(` class="${escapeAttribute(attrs.className)}"`);
    }

    parts.push('></Pattern>');
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
