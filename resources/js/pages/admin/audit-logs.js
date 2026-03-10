import api from "../../api.js";
import { showToast } from "../../utils.js";

window.auditLogsManager = () => ({
    logs: [],
    filters: {
        action: "",
        resource: "",
        from: "",
        to: "",
    },
    loading: false,
    showDetails: false,
    selectedLog: null,

    async init() {
        await this.loadLogs();
    },

    async loadLogs() {
        this.loading = true;
        try {
            const params = {};
            if (this.filters.action) params.action = this.filters.action;
            if (this.filters.resource) params.resource = this.filters.resource;
            if (this.filters.from) params.from = this.filters.from;
            if (this.filters.to) params.to = this.filters.to;

            const { data } = await api.getAuditLogs(params);
            this.logs = data.data;
        } catch (error) {
            showToast("Failed to load audit logs", "error");
        } finally {
            this.loading = false;
        }
    },

    resetFilters() {
        this.filters = {
            action: "",
            resource: "",
            from: "",
            to: "",
        };
        this.loadLogs();
    },

    viewDetails(log) {
        this.selectedLog = log;
        this.showDetails = true;
    },

    formatDate(dateString) {
        // Parse ISO 8601 date string correctly
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return "Invalid Date";
        }
        return date.toLocaleString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    },

    getActionClass(action) {
        const classes = {
            created: "bg-green-100 text-green-800",
            updated: "bg-blue-100 text-blue-800",
            deleted: "bg-red-100 text-red-800",
            account_activated: "bg-purple-100 text-purple-800",
            account_activation_email_sent: "bg-yellow-100 text-yellow-800",
            system_user_created: "bg-gray-100 text-gray-800",
        };
        return classes[action] || "bg-gray-100 text-gray-800";
    },
});
