import { login, isLoggedIn } from "../../auth.js";

if (isLoggedIn()) {
    window.location.replace("/dashboard");
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("login-form");
    const errorEl = document.getElementById("login-error");
    const submitBtn = document.getElementById("login-submit");
    const submitText = document.getElementById("submit-text");
    const spinner = document.getElementById("submit-spinner");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        errorEl.classList.add("hidden");
        submitBtn.disabled = true;
        submitText.textContent = "Signing in…";
        spinner.classList.remove("hidden");

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;

        try {
            await login(email, password);
            window.location.replace("/dashboard");
        } catch (err) {
            const msg =
                err.response?.data?.errors?.email?.[0] ??
                err.response?.data?.message ??
                "Login failed. Please try again.";
            errorEl.textContent = msg;
            errorEl.classList.remove("hidden");
        } finally {
            submitBtn.disabled = false;
            submitText.textContent = "Sign In";
            spinner.classList.add("hidden");
        }
    });
});
