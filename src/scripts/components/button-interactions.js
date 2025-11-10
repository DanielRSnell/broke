/**
 * Button Interactions Component
 * Handles gradient button hover and press effects
 * Extracted from inline event handlers
 */

export function initializeButtonInteractions() {
  // Find all gradient CTA buttons (those with inline flex and gradient background)
  // These are the primary action buttons with the purple/blue gradient
  const gradientButtons = document.querySelectorAll('a[style*="linear-gradient"]');

  gradientButtons.forEach(button => {
    const span = button.querySelector('span');

    if (!span) return; // Skip if no span child

    // Mouseover: Remove span background
    button.addEventListener('mouseover', () => {
      span.style.background = 'none';
    });

    // Mouseout: Restore span background
    button.addEventListener('mouseout', () => {
      span.style.backgroundColor = 'rgb(5, 6, 45)';
    });

    // Mousedown: Scale down button
    button.addEventListener('mousedown', () => {
      button.style.transform = 'scale(0.9)';
    });

    // Mouseup: Scale back to normal
    button.addEventListener('mouseup', () => {
      button.style.transform = 'scale(1)';
    });

    // Also reset on mouseleave (in case mouse moves off while pressed)
    button.addEventListener('mouseleave', () => {
      button.style.transform = 'scale(1)';
    });
  });

  console.log(`✓ Button interactions initialized (${gradientButtons.length} buttons)`);
}
