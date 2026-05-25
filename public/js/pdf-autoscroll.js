(function () {
  'use strict';

  const DEFAULT_INTERVAL = 50;
  const DEFAULT_SCROLL_SPEED = 1;

  class PDFAutoScroll {
    constructor(config) {
      this.interval = Math.max(1, Number(config?.interval) || DEFAULT_INTERVAL);
      this.scrollSpeed = Math.max(1, Number(config?.scrollSpeed) || DEFAULT_SCROLL_SPEED);
      this.viewerContainer = null;
      this.buttons = [];
      this.timerId = null;
      this.isRunning = false;
    }

    initialize() {
      this.viewerContainer = document.getElementById('viewerContainer');
      this.buttons = Array.from(document.querySelectorAll('[data-pdf-autoscroll-toggle]'));

      if (!this.viewerContainer || this.buttons.length === 0) {
        return;
      }

      this.buttons.forEach((button) => {
        button.addEventListener('click', () => this.toggle());
      });

      this.renderState();
    }

    start() {
      if (this.isRunning || !this.viewerContainer) {
        return;
      }

      this.isRunning = true;
      this.timerId = window.setInterval(() => {
        if (!this.viewerContainer) {
          this.stop();
          return;
        }

        const atBottom = this.viewerContainer.scrollTop + this.viewerContainer.clientHeight >= this.viewerContainer.scrollHeight - 1;
        if (atBottom) {
          this.stop();
          return;
        }

        this.viewerContainer.scrollBy(0, this.scrollSpeed);
      }, this.interval);

      this.renderState();
    }

    stop() {
      if (this.timerId !== null) {
        window.clearInterval(this.timerId);
        this.timerId = null;
      }

      this.isRunning = false;
      this.renderState();
    }

    toggle() {
      if (this.isRunning) {
        this.stop();
      } else {
        this.start();
      }
    }

    renderState() {
      const label = this.isRunning ? 'Stop Auto Scroll' : 'Auto Scroll';
      const title = this.isRunning ? 'Stop auto-scroll' : 'Start auto-scroll';

      this.buttons.forEach((button) => {
        button.classList.toggle('toggled', this.isRunning);
        button.setAttribute('aria-pressed', this.isRunning ? 'true' : 'false');
        button.title = title;

        const span = button.querySelector('span');
        if (span) {
          span.textContent = label;
        } else {
          button.textContent = label;
        }
      });
    }
  }

  function bootstrap() {
    const autoScroll = new PDFAutoScroll(window.pdfAutoScrollConfig);
    autoScroll.initialize();
    window.pdfAutoScrollInstance = autoScroll;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
  } else {
    bootstrap();
  }
})();