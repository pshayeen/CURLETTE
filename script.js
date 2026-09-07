document.addEventListener('DOMContentLoaded', function () {

    var modal = document.getElementById('authModal');
    if (!modal) {
        return; // this page has no auth modal (e.g. account.php, bookAppointment.php)
    }

    var isLoggedIn   = document.body.getAttribute('data-logged-in') === '1';
    var closeBtn     = document.getElementById('authModalClose');
    var contextEl    = document.getElementById('authModalContext');
    var errorEl      = document.getElementById('authModalError');
    var tabs         = modal.querySelectorAll('.auth-tab');
    var panels       = modal.querySelectorAll('.auth-panel');
    var redirectFields = modal.querySelectorAll('.js-redirect-field');
    var lastFocused   = null;

    var BOOK_CONTEXT_MESSAGE = 'Log in or create an account to book your appointment.';

    function setTab(tabName) {
        tabs.forEach(function (tab) {
            var active = tab.getAttribute('data-tab') === tabName;
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach(function (panel) {
            panel.hidden = panel.getAttribute('data-panel') !== tabName;
        });
        hideError();
    }

    function showError(message) {
        errorEl.textContent = message;
        errorEl.hidden = false;
    }

    function hideError() {
        errorEl.hidden = true;
        errorEl.textContent = '';
    }

    function setContext(message) {
        if (message) {
            contextEl.textContent = message;
            contextEl.hidden = false;
        } else {
            contextEl.hidden = true;
            contextEl.textContent = '';
        }
    }

    function setRedirect(value) {
        redirectFields.forEach(function (field) {
            field.value = value;
        });
    }

    function openModal(options) {
        options = options || {};
        lastFocused = document.activeElement;

        setTab(options.tab || 'login');
        setContext(options.context || null);
        setRedirect(options.redirect || '');
        hideError();

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        var firstInput = modal.querySelector('.auth-panel:not([hidden]) input');
        if (firstInput) {
            firstInput.focus();
        }
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocused) {
            lastFocused.focus();
        }
    }

    // Tab switching
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            setTab(tab.getAttribute('data-tab'));
        });
    });

    // Close interactions
    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    // Account button -> open modal (only rendered/tagged when logged out)
    document.querySelectorAll('.js-account-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            openModal({ tab: 'login' });
        });
    });

    // Book appointment buttons -> open modal only if logged out, otherwise let the link through
    document.querySelectorAll('.js-book-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            if (isLoggedIn) {
                return; // normal navigation to bookAppointment.php
            }
            e.preventDefault();
            openModal({ tab: 'login', context: BOOK_CONTEXT_MESSAGE, redirect: 'book-appointment' });
        });
    });

    // AJAX form submission for both login and signup forms
    modal.querySelectorAll('form[data-ajax-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            hideError();

            var submitBtn = form.querySelector('.auth-submit');
            var originalLabel = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'PLEASE WAIT…';

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.success) {
                        window.location = data.redirect;
                    } else {
                        showError(data.message || 'Something went wrong. Please try again.');
                    }
                })
                .catch(function () {
                    showError('Something went wrong. Please try again.');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalLabel;
                });
        });
    });

});