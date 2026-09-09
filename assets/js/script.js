document.addEventListener('DOMContentLoaded', function () {
    var body = document.body;

    function clearPasswordFields(scope) {
        (scope || document).querySelectorAll('input[type="password"]').forEach(function (input) {
            input.value = '';
            input.removeAttribute('value');
        });
    }

   
    clearPasswordFields();

    // Fires on every load, including when the browser restores this page
    // from the back/forward cache (e.g. hitting Back after logging out).
    // That's the case a typed-in password can otherwise reappear, so this
    // is the one place clearing it actually matters.
    window.addEventListener('pageshow', function (event) {
        clearPasswordFields();

        if (event.persisted) {
            // Page came from bfcache, not a fresh request — the server-rendered
            // "logged in" state (nav, account menu, username) may now be stale
            // if the user logged out in another tab or via this same back nav.
            // Ask the server for the real status and reload only if it's wrong,
            // so we don't force a reload on every ordinary back/forward visit.
            fetch('api/check-login.php', { cache: 'no-store' })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    var actuallyLoggedIn = !!data.loggedIn;
                    var shownAsLoggedIn = body.getAttribute('data-logged-in') === '1';
                    if (actuallyLoggedIn !== shownAsLoggedIn) {
                        location.reload();
                    }
                })
                .catch(function () { /* offline or blocked — ignore, non-critical */ });
        }
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {

            window.setTimeout(function () {
                clearPasswordFields(form);
            }, 0);
        });
    });

    
    var accountMenus = document.querySelectorAll('.account-menu');

    accountMenus.forEach(function (menu) {
        var toggle = menu.querySelector('.account-menu-toggle');
        var dropdown = menu.querySelector('.account-dropdown');

        if (!toggle || !dropdown) return;

        function closeMenu() {
            dropdown.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var open = !dropdown.hidden;
            document.querySelectorAll('.account-dropdown').forEach(function (other) {
                other.hidden = true;
            });
            document.querySelectorAll('.account-menu-toggle').forEach(function (other) {
                other.setAttribute('aria-expanded', 'false');
            });

            dropdown.hidden = open;
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
        });

        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) closeMenu();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeMenu();
        });
    });

    var modal = document.getElementById('authModal');
    if (!modal) return;

    var isLoggedIn = body.getAttribute('data-logged-in') === '1';
    var closeBtn = document.getElementById('authModalClose');
    var contextEl = document.getElementById('authModalContext');
    var errorEl = document.getElementById('authModalError');
    var panels = modal.querySelectorAll('.auth-panel');
    var switchTriggers = modal.querySelectorAll('.auth-switch-trigger');
    var redirectFields = modal.querySelectorAll('.js-redirect-field');
    var lastFocused = null;
    var BOOK_CONTEXT_MESSAGE = 'Log in or create an account to book your appointment.';

    function setTab(tabName) {
        panels.forEach(function (panel) {
            panel.hidden = panel.getAttribute('data-panel') !== tabName;
        });
        clearPasswordFields(modal);
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
        contextEl.textContent = message || '';
        contextEl.hidden = !message;
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
        clearPasswordFields(modal);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        body.style.overflow = 'hidden';

        var firstInput = modal.querySelector('.auth-panel:not([hidden]) input');
        if (firstInput) firstInput.focus();
    }

    function closeModal() {
        clearPasswordFields(modal);
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        body.style.overflow = '';
        if (lastFocused) lastFocused.focus();
    }

    switchTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            setTab(trigger.getAttribute('data-tab'));
        });
    });

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    document.querySelectorAll('.js-account-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            openModal({ tab: 'login' });
        });
    });

    document.querySelectorAll('.js-book-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            if (isLoggedIn) return;
            event.preventDefault();
            openModal({ tab: 'login', context: BOOK_CONTEXT_MESSAGE, redirect: 'book-appointment' });
        });
    });

    modal.querySelectorAll('form[data-ajax-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            hideError();

            var submitBtn = form.querySelector('.auth-submit');
            var originalLabel = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'PLEASE WAIT…';

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(form),
                cache: 'no-store'
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                clearPasswordFields(form);
                if (data.success) {
                    window.location.replace(data.redirect);
                } else {
                    showError(data.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(function () {
                clearPasswordFields(form);
                showError('Something went wrong. Please try again.');
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = originalLabel;
            });
        });
    });
});

/* BOOK APPOINTMENT — service selection */
document.addEventListener('DOMContentLoaded', function () {
    var serviceInput = document.getElementById('service');
    var serviceMessage = document.getElementById('selectedServiceMessage');
    var selectedServiceName = document.getElementById('selectedServiceName');

    if (!serviceInput) return;

    function selectService(serviceName) {
        serviceInput.value = serviceName;

        document.querySelectorAll('[data-service-card]').forEach(function (card) {
            card.classList.toggle('selected', card.getAttribute('data-service-card') === serviceName);
        });

        if (selectedServiceName) {
            selectedServiceName.textContent = serviceName;
        }

        if (serviceMessage) {
            serviceMessage.hidden = !serviceName;
        }
    }

    document.querySelectorAll('[data-service-select]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            selectService(trigger.getAttribute('data-service-select'));
        });
    });

    document.querySelector('.booking-form')?.addEventListener('submit', function (event) {
        var form = event.target;

        if (!serviceInput.value) {
            event.preventDefault();
            var firstService = document.querySelector('[data-service-card]');
            if (firstService) {
                firstService.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        if (form.dataset.confirmed === 'true') {
            return;
        }

        if (!form.checkValidity()) {
            return; // let the browser show its native "please fill this field" messages
        }

        event.preventDefault();

        var summaryEl = document.getElementById('bookingConfirmSummary');
        var dateInput = document.getElementById('appointment_date');
        var timeSelect = document.getElementById('appointment_time');

        if (summaryEl && dateInput && dateInput.value && timeSelect) {
            var dateObj = new Date(dateInput.value + 'T00:00:00');
            var dateText = dateObj.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
            var timeOption = timeSelect.options[timeSelect.selectedIndex];
            summaryEl.textContent = serviceInput.value + ' — ' + dateText + ' at ' + (timeOption ? timeOption.text : '');
        }

        var bookingModalEl = document.getElementById('bookingConfirmModal');
        if (bookingModalEl && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(bookingModalEl).show();
        }
    });

    document.getElementById('bookingConfirmBtn')?.addEventListener('click', function () {
        var form = document.querySelector('.booking-form');
        if (form) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {

    // shared cancel modal, filled in per row when opened
    var cancelModalEl = document.getElementById('cancelConfirmModal');
    if (cancelModalEl) {
        cancelModalEl.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var idField = document.getElementById('cancelConfirmIdField');
            var labelEl = document.getElementById('cancelConfirmLabel');

            if (idField) idField.value = trigger.getAttribute('data-id') || '';
            if (labelEl) labelEl.textContent = trigger.getAttribute('data-label') || "This can't be undone.";
        });
    }

    // confirm reschedule before actually submitting
    var pendingRescheduleForm = null;
    var rescheduleModalEl = document.getElementById('rescheduleConfirmModal');
    var rescheduleModal = rescheduleModalEl ? new bootstrap.Modal(rescheduleModalEl) : null;

    document.querySelectorAll('.js-reschedule-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            event.preventDefault();

            var dateInput = form.querySelector('input[name="appointment_date"]');
            var timeInput = form.querySelector('input[name="appointment_time"]');
            var summaryEl = document.getElementById('rescheduleConfirmSummary');

            if (summaryEl && dateInput && timeInput && dateInput.value && timeInput.value) {
                var combined = new Date(dateInput.value + 'T' + timeInput.value);
                var dateText = combined.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
                var timeText = combined.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
                summaryEl.textContent = dateText + ' at ' + timeText;
            }

            pendingRescheduleForm = form;
            if (rescheduleModal) rescheduleModal.show();
        });
    });

    var confirmRescheduleBtn = document.getElementById('rescheduleConfirmBtn');
    if (confirmRescheduleBtn) {
        confirmRescheduleBtn.addEventListener('click', function () {
            if (pendingRescheduleForm) {
                pendingRescheduleForm.dataset.confirmed = 'true';
                pendingRescheduleForm.submit();
            }
        });
    }

    // Checkout confirmation
    var checkoutForm = document.querySelector('.js-checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (event) {
            if (checkoutForm.dataset.confirmed === 'true') {
                return;
            }
            event.preventDefault();
            var modalEl = document.getElementById('checkoutConfirmModal');
            if (modalEl && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    }

    document.getElementById('checkoutConfirmBtn')?.addEventListener('click', function () {
        if (checkoutForm) {
            checkoutForm.dataset.confirmed = 'true';
            checkoutForm.submit();
        }
    });

});
