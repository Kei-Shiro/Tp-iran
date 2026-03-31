
(() => {
	'use strict';

	const toggle = document.querySelector('.nav-toggle');
	const nav = document.getElementById('site-nav');
	const overlay = document.querySelector('.nav-overlay');

	if (!toggle || !nav || !overlay) return;

	let lastActive = null;

	const focusablesSelector = [
		'a[href]',
		'button:not([disabled])',
		'input:not([disabled])',
		'select:not([disabled])',
		'textarea:not([disabled])',
		'[tabindex]:not([tabindex="-1"])'
	].join(',');

	function setOpen(isOpen) {
		document.body.classList.toggle('nav-open', isOpen);
		toggle.setAttribute('aria-expanded', String(isOpen));
		toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
		overlay.hidden = !isOpen;

		if (isOpen) {
			lastActive = document.activeElement;
			const firstLink = nav.querySelector('a');
			if (firstLink) firstLink.focus();
		} else {
			const backTo = (lastActive && lastActive.focus) ? lastActive : toggle;
			backTo.focus();
		}
	}

	function isOpen() {
		return document.body.classList.contains('nav-open');
	}

	toggle.addEventListener('click', () => setOpen(!isOpen()));

	overlay.addEventListener('click', () => setOpen(false));

	document.addEventListener('keydown', (e) => {
		if (!isOpen()) return;

		if (e.key === 'Escape') {
			e.preventDefault();
			setOpen(false);
			return;
		}

		// Focus trap leger
		if (e.key === 'Tab') {
			const focusables = nav.querySelectorAll(focusablesSelector);
			if (!focusables.length) return;

			const first = focusables[0];
			const last = focusables[focusables.length - 1];

			if (e.shiftKey && document.activeElement === first) {
				e.preventDefault();
				last.focus();
			} else if (!e.shiftKey && document.activeElement === last) {
				e.preventDefault();
				first.focus();
			}
		}
	});

	// Ferme le menu apres clic sur un lien (mobile)
	nav.addEventListener('click', (e) => {
		const target = e.target;
		if (!(target instanceof Element)) return;
		const link = target.closest('a');
		if (link && isOpen()) {
			setOpen(false);
		}
	});

	// Etat initial
	overlay.hidden = true;
})();


