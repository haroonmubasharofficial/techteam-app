import './quotation.js';

document.addEventListener('keydown', (event) => {
  const tag = document.activeElement?.tagName;
  const typing = ['INPUT','TEXTAREA','SELECT'].includes(tag);
  if (event.ctrlKey && event.key.toLowerCase() === 'k') {
    event.preventDefault();
    document.querySelector('[data-global-search]')?.focus();
  }
  if (!typing && event.key === 'F2') { event.preventDefault(); window.location.href = '/quotations/create'; }
  if (event.ctrlKey && event.key.toLowerCase() === 's') {
    event.preventDefault(); document.querySelector('form[data-save-form]')?.requestSubmit();
  }
});
