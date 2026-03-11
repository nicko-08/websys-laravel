import { isLoggedIn, getUser, logout } from "./auth.js";

document.addEventListener("DOMContentLoaded", () => {
    const user = getUser();

    if (isLoggedIn() && user) {
        // Show user menu, hide login button
        document.getElementById("user-menu")?.classList.remove("hidden");
        document.getElementById("login-link")?.classList.add("hidden");
        document.getElementById("mobile-user-menu")?.classList.remove("hidden");
        document.getElementById("mobile-login-link")?.classList.add("hidden");

        // Set user info
        const initials = user.name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .slice(0, 2);

        // Safely set text content for all potential elements
        const els = {
            initials: document.getElementById("user-initials"),
            nameDisplay: document.getElementById("user-name-display"),
            emailDisplay: document.getElementById("user-email-display"),
            roleDisplay: document.getElementById("user-role-display"),
            mobileName: document.getElementById("mobile-user-name"),
            mobileEmail: document.getElementById("mobile-user-email"),
        };

        if (els.initials) els.initials.textContent = initials;
        if (els.nameDisplay) els.nameDisplay.textContent = user.name;
        if (els.emailDisplay) els.emailDisplay.textContent = user.email;
        if (els.roleDisplay)
            els.roleDisplay.textContent = user.role.replace(/-/g, " ");
        if (els.mobileName) els.mobileName.textContent = user.name;
        if (els.mobileEmail) els.mobileEmail.textContent = user.email;

        // Show role-specific links
        if (user.role === "admin" || user.role === "auditor") {
            const auditLink = document.getElementById("audit-link");
            if (auditLink) {
                auditLink.classList.remove("hidden");
                auditLink.classList.add("block");
            }
        }
        if (user.role === "admin") {
            const usersLink = document.getElementById("users-link");
            const adminLink = document.getElementById("admin-link");
            const fiscalLink = document.getElementById("fiscal-link");

            if (usersLink) {
                usersLink.classList.remove("hidden");
                usersLink.classList.add("block");
            }
            if (adminLink) {
                adminLink.classList.remove("hidden");
                adminLink.classList.add("block");
            }
            if (fiscalLink) {
                fiscalLink.classList.remove("hidden");
                fiscalLink.classList.add("block");
            }
        }
    }

    // Mobile menu toggle
    document
        .getElementById("mobile-menu-btn")
        ?.addEventListener("click", () => {
            document.getElementById("mobile-menu")?.classList.toggle("hidden");
        });

    // Logout handlers
    const handleLogout = async (e) => {
        e.preventDefault();
        await logout();
        window.location.href = "/login";
    };

    const navLogout = document.getElementById("logout-btn-nav");
    if (navLogout) navLogout.addEventListener("click", handleLogout);

    const mobileLogout = document.getElementById("logout-btn-mobile");
    if (mobileLogout) mobileLogout.addEventListener("click", handleLogout);
});
