// Career Connect Hub - Main App JavaScript
// PWA Installation & Core Functionality

// Register Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js')
      .then((registration) => {
        console.log('✅ Service Worker registered:', registration.scope);
      })
      .catch((error) => {
        console.log('❌ Service Worker registration failed:', error);
      });
  });
}

// PWA Install Prompt
let deferredPrompt;
const installButton = document.getElementById('install-app-btn');

window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault();
  
  // Stash the event so it can be triggered later
  deferredPrompt = e;
  
  // Show install button if it exists
  if (installButton) {
    installButton.style.display = 'block';
    
    installButton.addEventListener('click', async () => {
      if (!deferredPrompt) {
        return;
      }
      
      // Show the install prompt
      deferredPrompt.prompt();
      
      // Wait for the user to respond to the prompt
      const { outcome } = await deferredPrompt.userChoice;
      
      console.log(`User response to install prompt: ${outcome}`);
      
      // Clear the deferredPrompt
      deferredPrompt = null;
      
      // Hide the install button
      installButton.style.display = 'none';
    });
  }
});

// Track when PWA is installed
window.addEventListener('appinstalled', () => {
  console.log('✅ PWA installed successfully');
  deferredPrompt = null;
  
  // Hide install button
  if (installButton) {
    installButton.style.display = 'none';
  }
  
  // Show success message
  showToast('App installed successfully! 🎉');
});

// Check if app is running in standalone mode
function isStandalone() {
  return (window.matchMedia('(display-mode: standalone)').matches) || 
         (window.navigator.standalone) || 
         document.referrer.includes('android-app://');
}

if (isStandalone()) {
  console.log('✅ Running in standalone mode (PWA)');
  document.body.classList.add('standalone-mode');
}

// Toast notification helper
function showToast(message, duration = 3000) {
  const toast = document.createElement('div');
  toast.className = 'toast-notification';
  toast.textContent = message;
  toast.style.cssText = `
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    z-index: 10000;
    animation: slideUp 0.3s ease-out;
  `;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.animation = 'slideDown 0.3s ease-out';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// Add CSS for toast animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideUp {
    from {
      transform: translateX(-50%) translateY(100px);
      opacity: 0;
    }
    to {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }
  }
  
  @keyframes slideDown {
    from {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }
    to {
      transform: translateX(-50%) translateY(100px);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);

// Network status monitoring
window.addEventListener('online', () => {
  showToast('✅ Back online!');
  document.body.classList.remove('offline');
});

window.addEventListener('offline', () => {
  showToast('📡 You are offline', 5000);
  document.body.classList.add('offline');
});

// Form submission with offline support
function handleFormSubmit(form, endpoint) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(form);
    
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        showToast('✅ ' + (result.message || 'Success!'));
        if (result.redirect) {
          setTimeout(() => window.location.href = result.redirect, 1000);
        }
      } else {
        showToast('❌ ' + (result.error || 'An error occurred'));
      }
    } catch (error) {
      if (!navigator.onLine) {
        // Queue for background sync
        showToast('📡 Saved offline. Will sync when online.');
        queueOfflineSubmission(formData, endpoint);
      } else {
        showToast('❌ Network error. Please try again.');
      }
    }
  });
}

// Queue offline submissions for background sync
function queueOfflineSubmission(formData, endpoint) {
  const queue = JSON.parse(localStorage.getItem('offline-queue') || '[]');
  queue.push({
    endpoint,
    data: Object.fromEntries(formData),
    timestamp: Date.now()
  });
  localStorage.setItem('offline-queue', JSON.stringify(queue));
  
  // Register sync if supported
  if ('serviceWorker' in navigator && 'sync' in ServiceWorkerRegistration.prototype) {
    navigator.serviceWorker.ready.then((registration) => {
      return registration.sync.register('sync-applications');
    });
  }
}

// Process offline queue when back online
window.addEventListener('online', async () => {
  const queue = JSON.parse(localStorage.getItem('offline-queue') || '[]');
  
  if (queue.length > 0) {
    console.log(`Processing ${queue.length} offline submissions...`);
    
    for (const item of queue) {
      try {
        const formData = new FormData();
        Object.entries(item.data).forEach(([key, value]) => {
          formData.append(key, value);
        });
        
        await fetch(item.endpoint, {
          method: 'POST',
          body: formData
        });
      } catch (error) {
        console.error('Failed to sync:', error);
      }
    }
    
    // Clear queue
    localStorage.setItem('offline-queue', '[]');
    showToast('✅ Offline submissions synced!');
  }
});

// Export functions for use in other scripts
window.CareerConnectApp = {
  showToast,
  handleFormSubmit,
  isStandalone
};
