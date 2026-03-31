(() => {
	function updateCounter(input) {
		const targetId = input.dataset.counterTarget;
		const max = Number(input.dataset.counterMax || 0);
		if (!targetId || !max) {
			return;
		}

		const counter = document.getElementById(targetId);
		if (!counter) {
			return;
		}

		const len = input.value.length;
		counter.textContent = `${len}/${max}`;
		counter.style.color = len > max * 0.9 ? '#a11a2c' : '';
	}

	function initCounters() {
		const fields = document.querySelectorAll('[data-counter-target][data-counter-max]');
		fields.forEach((field) => {
			updateCounter(field);
			field.addEventListener('input', () => updateCounter(field));
		});
	}

	function loadScript(src) {
		return new Promise((resolve, reject) => {
			const script = document.createElement('script');
			script.src = src;
			script.referrerPolicy = 'origin';
			script.onload = resolve;
			script.onerror = reject;
			document.head.appendChild(script);
		});
	}

	async function initTinyMce() {
		const editorTargets = document.querySelectorAll('[data-editor^="tinymce"]');
		if (editorTargets.length === 0) {
			return;
		}

		if (!window.tinymce) {
			const key = (window.APP_CONFIG && window.APP_CONFIG.tinyMceApiKey) || 'no-api-key';
			try {
				await loadScript(`https://cdn.tiny.cloud/1/${encodeURIComponent(key)}/tinymce/6/tinymce.min.js`);
			} catch (e) {
				await loadScript('https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js');
			}
		}

		if (!window.tinymce) {
			return;
		}

		if (document.querySelector('#titre[data-editor="tinymce-title"]') && !window.tinymce.get('titre')) {
			window.tinymce.init({
				selector: '#titre[data-editor="tinymce-title"]',
				menubar: false,
				plugins: 'autolink code',
				toolbar: 'undo redo | bold italic forecolor | removeformat | code',
				height: 140,
				branding: false,
				statusbar: false,
				forced_root_block: '',
				convert_urls: false,
			});
		}

		if (document.querySelector('#contenu[data-editor="tinymce-content"]') && !window.tinymce.get('contenu')) {
			window.tinymce.init({
				selector: '#contenu[data-editor="tinymce-content"]',
				menubar: false,
				plugins: 'lists link image table code fullscreen',
				toolbar: 'undo redo | blocks | bold italic | bullist numlist | link image | code fullscreen',
				height: 420,
				branding: false,
				convert_urls: false,
			});
		}
	}

	document.addEventListener('DOMContentLoaded', () => {
		initCounters();
		initTinyMce();

		document.querySelectorAll('form.article-form').forEach((form) => {
			form.addEventListener('submit', () => {
				if (window.tinymce) {
					window.tinymce.triggerSave();
				}
			});
		});
	});
})();


