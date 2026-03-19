import './bootstrap';
import 'notyf/notyf.min.css';
import { Notyf } from 'notyf';
import Alpine from 'alpinejs';
import aiSummarizer from './ai-summarizer.js';

const notyf = new Notyf({
  duration: 4000,
  position: { x: 'right', y: 'top' },
  ripple: true,
  dismissible: true,
  types: [
    {
      type: 'success',
      background: 'var(--lime)',
      icon: {
        className: 'w-5 h-5 mr-2',
        tagName: 'svg',
        viewBox: '0 0 24 24',
        innerHTML: '<path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" fill="none"/>'
      }
    },
    {
      type: 'error',
      background: 'var(--red)',
      icon: {
        className: 'w-5 h-5 mr-2',
        tagName: 'svg',
        viewBox: '0 0 24 24',
        innerHTML: '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2"/>'
      }
    },
    {
      type: 'warning',
      background: 'var(--amber)',
      icon: { className: 'w-5 h-5 mr-2', tagName: 'i', innerHTML: '⚠️' }
    }
  ]
});

window.notyf = notyf;

window.toast = {
  success(text) {
    notyf.success(text);
  },
  error(text) {
    notyf.error(text);
  },
  warning(text) {
    notyf.open({
      type: 'warning',
      message: text
    });
  }
};

window.Alpine = Alpine;

Alpine.data('aiSummarizer', aiSummarizer);

Alpine.start();