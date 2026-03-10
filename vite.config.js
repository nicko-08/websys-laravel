import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/nav.js",
                "resources/js/pages/auth/login.js",
                "resources/js/pages/dashboard.js",
                "resources/js/pages/budget/index.js",
                "resources/js/pages/budget/details.js",
                "resources/js/pages/admin/units.js",
                "resources/js/pages/admin/fiscal-years.js",
                "resources/js/pages/expense/index.js",
                "resources/js/pages/expense/details.js",
                "resources/js/pages/analytics/index.js",
                "resources/js/pages/admin/audit-logs.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
