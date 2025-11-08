/**
 * Global Form Handler
 *
 * Purpose: Automatically detects and handles form submissions with AJAX
 * Pattern: Use this as a base for implementing form handling in your project
 *
 * Features:
 * - Auto-detects all forms with email inputs
 * - Prevents page reload
 * - Shows loading state
 * - Handles success/error messages
 * - Smooth transitions with CSS
 *
 * HTML Structure Required:
 * <form>
 *   <div class="form-fields">
 *     <input type="email" name="email" required>
 *     <!-- other fields -->
 *     <button type="submit">Submit</button>
 *   </div>
 *   <div class="form-success-message hidden">Success message</div>
 *   <div class="form-error-message hidden">Error message</div>
 * </form>
 *
 * CSS Required:
 * .hidden { display: none; }
 * .fade-in { animation: fadeIn 0.3s ease-in; }
 * .fade-out { animation: fadeOut 0.3s ease-out; }
 */
export function initFormHandler() {
  const forms = document.querySelectorAll('form');

  if (!forms.length) return;

  forms.forEach(form => {
    const submitButton = form.querySelector('button[type="submit"]');
    const emailInput = form.querySelector('input[type="email"]');
    const successMessage = form.querySelector('.form-success-message');
    const errorMessage = form.querySelector('.form-error-message');
    const formFields = form.querySelector('.form-fields');

    // Skip forms that don't have email input (not our forms)
    if (!emailInput) return;

    if (!submitButton) {
      console.warn('Form missing submit button:', form);
      return;
    }

    // Make form position relative for absolute positioning of children
    form.style.position = 'relative';

    // Store original button content (HTML to preserve spans/icons)
    const originalButtonHTML = submitButton.innerHTML;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Get all form data
      const formData = new FormData(form);
      const data = {};

      // Convert FormData to plain object
      for (const [key, value] of formData.entries()) {
        data[key] = value;
      }

      // Basic validation - at least email should be present
      if (!data.email || !data.email.trim()) return;

      // Store form height to prevent layout shift
      const formHeight = form.offsetHeight;
      form.style.minHeight = `${formHeight}px`;

      // Set loading state
      submitButton.disabled = true;
      submitButton.innerHTML = 'Sending...';

      // Hide previous messages with CSS transitions
      if (successMessage && !successMessage.classList.contains('hidden')) {
        successMessage.classList.add('fade-out');
        setTimeout(() => {
          successMessage.classList.add('hidden');
          successMessage.classList.remove('fade-out');
        }, 300);
      }
      if (errorMessage && !errorMessage.classList.contains('hidden')) {
        errorMessage.classList.add('fade-out');
        setTimeout(() => {
          errorMessage.classList.add('hidden');
          errorMessage.classList.remove('fade-out');
        }, 300);
      }

      try {
        // TODO: Replace with your actual endpoint
        const response = await fetch('/wp-json/theme/v1/form-submit', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            current: window.location.href,
            email: data.email || '',
            data: data
          })
        });

        // Check if response is ok (200-299)
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        // Check if submission was successful
        if (result.success) {
          // Fade out form fields
          formFields.classList.add('fade-out');

          setTimeout(() => {
            // Hide form fields
            formFields.style.position = 'absolute';
            formFields.style.visibility = 'hidden';

            // Show success message
            if (successMessage) {
              successMessage.classList.remove('hidden');
              successMessage.classList.add('fade-in');
            }
          }, 300);

          // Clear form
          form.reset();
        } else {
          // Show error message
          if (errorMessage) {
            errorMessage.classList.remove('hidden');
            errorMessage.classList.add('fade-in');
          }

          // Reset button state for retry
          submitButton.disabled = false;
          submitButton.innerHTML = originalButtonHTML;
        }
      } catch (err) {
        console.error('Form submission error:', err);

        // Show error message
        if (errorMessage) {
          errorMessage.classList.remove('hidden');
          errorMessage.classList.add('fade-in');
        }

        // Reset button state for retry
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonHTML;
      }
    });
  });
}
