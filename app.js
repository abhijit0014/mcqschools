window.onload = () => {
    'use strict';
  
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
      .then(() => console.log('service worker installed'))
      .catch(err => console.error('Error', err));
    }
}


/*
let installPrompt;
window.addEventListener('beforeinstallprompt', event => {
    // Don't show the install popup without the user asking for it
    event.preventDefault();

    // Save event for later
    installPrompt = event;

    // Show or enable install button in your app
});


document.querySelector('#install-button').addEventListener('click', async () => {
    // Show prompt to user
    installPrompt.prompt();

    const installed = await installPrompt.userChoice;
    console.log('User installed app: ' + installed);

    // Clean up. Prompt cannot be reused.
    installPrompt = null;
});
*/