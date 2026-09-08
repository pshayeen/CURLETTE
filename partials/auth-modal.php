<!-- ACCOUNT MODAL -->
<div class="modal-overlay" id="authModal" aria-hidden="true">
    <div class="modal-box auth-card" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">

        <button type="button" class="modal-close" id="authModalClose" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="auth-tabs" role="tablist">
            <button type="button" class="auth-tab active" data-tab="login" role="tab" aria-selected="true">LOG IN</button>
            <button type="button" class="auth-tab" data-tab="signup" role="tab" aria-selected="false">SIGN UP</button>
        </div>

        <p class="auth-context" id="authModalContext" hidden></p>
        <p class="auth-error" id="authModalError" hidden></p>

        <div class="auth-panel" data-panel="login">
            <h2 class="auth-title" id="authModalTitle">Welcome back.</h2>
            <p class="auth-sub">Log in to manage your appointments and orders.</p>

            <form action="actions/login.php" method="post" class="auth-form" novalidate data-ajax-form>
                <input type="hidden" name="login" value="1">
                <input type="hidden" name="redirect" value="" class="js-redirect-field">

                <div class="form-group">
                    <label for="modal-login-email">Email</label>
                    <input type="email" id="modal-login-email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="modal-login-password">Password</label>
                    <input type="password" id="modal-login-password" name="password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn-main auth-submit">LOG IN</button>
            </form>
        </div>

        <div class="auth-panel" data-panel="signup" hidden>
            <h2 class="auth-title">Join Curlétte.</h2>
            <p class="auth-sub">Create an account to book appointments and shop products.</p>

            <form action="actions/login.php" method="post" class="auth-form" novalidate data-ajax-form>
                <input type="hidden" name="signup" value="1">
                <input type="hidden" name="redirect" value="" class="js-redirect-field">

                <div class="form-group">
                    <label for="modal-signup-username">Username</label>
                    <input type="text" id="modal-signup-username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="modal-signup-email">Email</label>
                    <input type="email" id="modal-signup-email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="modal-signup-password">Password</label>
                    <input type="password" id="modal-signup-password" name="password" autocomplete="new-password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" title="Password must be at least 8 characters and include a letter, a number, and a special character." required>
                    <small class="password-hint">At least 8 characters, with a letter, a number, and a special character.</small>
                </div>

                <div class="form-group">
                    <label for="modal-signup-confirm">Confirm Password</label>
                    <input type="password" id="modal-signup-confirm" name="confirm_password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn-main auth-submit">CREATE ACCOUNT</button>
            </form>
        </div>

    </div>
</div>
