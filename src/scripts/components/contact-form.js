/**
 * Contact Form Handler
 * Handles form submission to webhook endpoint
 */

function setupFormHandler() {
  const form = document.querySelector('.js-contact-form');
  const successMsg = document.querySelector('.js-form-success');
  const errorMsg = document.querySelector('.js-form-error');

  if (!form) {
    return false;
  }

  console.log('✅ Contact form found, attaching handler');
  console.log('Form element:', form);
  console.log('Success message:', successMsg);
  console.log('Error message:', errorMsg);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    console.log('✓ Form submission prevented - handling with JavaScript');

    // Hide any previous messages
    if (successMsg) successMsg.classList.add('hidden');
    if (errorMsg) errorMsg.classList.add('hidden');

    // Get form data
    const formData = new FormData(form);

    // Build payload matching form.json structure
    const payload = {
      name: formData.get('name'),
      email: formData.get('email'),
      company: formData.get('company') || '',
      project_type: formData.get('project-type'),
      budget: formData.get('budget'),
      message: formData.get('message'),
      domain: window.location.hostname,
      page_url: window.location.href,
      ip_address: '', // Will be captured server-side
      user_agent: navigator.userAgent,
      referrer: document.referrer || '',
      status: 'new',
      submitted_at: new Date().toISOString()
    };

    console.log('📤 Sending payload to webhook:', payload);

    // Disable submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="w-full">Sending...</span>';

    try {
      console.log('🚀 POSTing to https://api.umbral.ai/webhook/broke-form');

      const response = await fetch('https://api.umbral.ai/webhook/broke-form', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
      });

      console.log('✅ Webhook response:', response.status, response.statusText);

      if (response.ok) {
        // Show success message
        if (successMsg) {
          successMsg.classList.remove('hidden');
          successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        // Reset form
        form.reset();
      } else {
        throw new Error('Form submission failed');
      }
    } catch (error) {
      console.error('Form submission error:', error);
      if (errorMsg) {
        errorMsg.classList.remove('hidden');
        errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    } finally {
      // Re-enable submit button
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });

  return true;
}

export function initializeContactForm() {
  console.log('🔍 Initializing contact form handler...');

  // Try immediate setup
  if (setupFormHandler()) {
    console.log('✓ Contact form initialized immediately');
    return;
  }

  // If not found, retry after a short delay (for dynamically loaded content)
  console.log('⏳ Form not found yet, retrying in 100ms...');
  setTimeout(() => {
    if (setupFormHandler()) {
      console.log('✓ Contact form initialized after delay');
    } else {
      console.log('ℹ️ Contact form not found on this page (this is normal for non-contact pages)');
    }
  }, 100);

  // Also watch for dynamic content loading
  const observer = new MutationObserver(() => {
    if (document.querySelector('.js-contact-form')) {
      console.log('✅ Contact form detected in DOM');
      if (setupFormHandler()) {
        observer.disconnect();
      }
    }
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true
  });

  // Stop observing after 5 seconds
  setTimeout(() => observer.disconnect(), 5000);
}
