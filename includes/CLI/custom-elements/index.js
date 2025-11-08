/**
 * Custom Element Handler Registry
 *
 * Loads all custom element handlers and provides lookup maps
 * for bidirectional HTML ↔ Block conversion
 */

const handlers = [
  require('./Part'),
  require('./Pattern'),
  require('./Content')
];

// Build lookup maps for efficient access
const byTagName = {}; // 'part' -> handler
const byBlockName = {}; // 'core/template-part' -> handler

handlers.forEach(handler => {
  // Tag names are case-insensitive in HTML
  byTagName[handler.tagName.toLowerCase()] = handler;

  // Block names are case-sensitive
  byBlockName[handler.blockName] = handler;
});

module.exports = {
  // Lookup by HTML tag name (lowercase)
  byTagName,

  // Lookup by WordPress block name
  byBlockName,

  // All handlers array (for iteration/debugging)
  handlers
};
