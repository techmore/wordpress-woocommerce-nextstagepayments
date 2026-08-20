(function () {
	'use strict';
	var COOKIE = 'nsp_age';
	var gate = document.getElementById('nsp-age-gate');
	if (!gate) {
		return;
	}

	function hasConsented() {
		return document.cookie.indexOf(COOKIE + '=21') !== -1;
	}

	function allow() {
		document.cookie = COOKIE + '=21; path=/; max-age=31536000; SameSite=Lax';
		gate.setAttribute('hidden', '');
		document.body.classList.remove('nsp-age-locked');
		document.body.style.overflow = '';
	}

	function deny() {
		window.location.href = 'https://www.google.com/';
	}

	var yes = gate.querySelector('.nsp-age-yes');
	var no = gate.querySelector('.nsp-age-no');
	if (yes) {
		yes.addEventListener('click', allow);
	}
	if (no) {
		no.addEventListener('click', deny);
	}

	if (!hasConsented()) {
		gate.removeAttribute('hidden');
		document.body.classList.add('nsp-age-locked');
		document.body.style.overflow = 'hidden';
	}
})();