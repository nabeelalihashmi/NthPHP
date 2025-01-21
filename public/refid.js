(function () {
    const existingGlobalKeys = new Set(Object.keys(window));
  
    function initializeReferences() {
      const elements = document.querySelectorAll('[id]');
      elements.forEach((el) => {
        const id = el.id;
        if (!existingGlobalKeys.has(id)) {
          window[id] = el;
        }
      });
    }
  
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initializeReferences);
    } else {
      initializeReferences();
    }
  })();
  