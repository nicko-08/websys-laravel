import axios from "axios";
import { getToken } from "./auth.js";

const API_BASE = "/api/v1";

// Create axios instance with defaults
const api = axios.create({
    baseURL: API_BASE,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// Add auth token to requests automatically
api.interceptors.request.use((config) => {
    const token = getToken();
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Handle 401 and 403 errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Log the error
            console.warn("401 Unauthorized:", error.config?.url);

            const authEndpoints = ["/login", "/logout", "/user"];
            const isAuthEndpoint = authEndpoints.some((endpoint) =>
                error.config?.url?.includes(endpoint),
            );

            const isPollingRequest = error.config?.url?.includes("/analytics/");

            if (
                isAuthEndpoint ||
                (!isPollingRequest &&
                    error.response?.data?.message?.includes("Unauthenticated"))
            ) {
                console.error("Token invalid or expired - logging out");
                localStorage.removeItem("gbms_token");
                localStorage.removeItem("gbms_user");
                window.location.href = "/login";
            } else {
                console.warn(
                    "401 error but keeping session - may be transient issue",
                );
            }
        } else if (error.response?.status === 403) {
            console.error(
                "Permission denied:",
                error.response?.data?.message ||
                    "You don't have permission to perform this action",
            );
        }
        return Promise.reject(error);
    },
);

// API Methods
export default {
    // Analytics
    getOverallSummary: () => api.get("/analytics/overall-summary"),
    getBarangayList: () => api.get("/analytics/barangay-list"),
    getBarangayAnalytics: (budgetId) =>
        api.get(`/analytics/barangay/${budgetId}`),

    // Budgets
    getBudgets: (params) => api.get("/budgets", { params }),
    getBudget: (id) => api.get(`/budgets/${id}`),
    createBudget: (data) => api.post("/budgets", data),
    updateBudget: (id, data) => api.put(`/budgets/${id}`, data),
    deleteBudget: (id) => api.delete(`/budgets/${id}`),

    // Budget Items
    getBudgetItems: (params) => api.get("/budget-items", { params }),
    getBudgetItem: (id) => api.get(`/budget-items/${id}`),
    getBudgetItemSummary: (id) => api.get(`/budget-items/${id}/summary`),
    createBudgetItem: (data) => api.post("/budget-items", data),
    updateBudgetItem: (id, data) => api.put(`/budget-items/${id}`, data),
    deleteBudgetItem: (id) => api.delete(`/budget-items/${id}`),

    // Expenses
    getExpenses: (params) => api.get("/expenses", { params }),
    getExpense: (id) => api.get(`/expenses/${id}`),
    getExpenseSummary: (params) => api.get("/expenses/summary", { params }),
    createExpense: (data) => api.post("/expenses", data),
    updateExpense: (id, data) => api.put(`/expenses/${id}`, data),
    deleteExpense: (id) => api.delete(`/expenses/${id}`),

    // Budget Categories
    getCategories: () => api.get("/budget-categories"),
    getCategory: (id) => api.get(`/budget-categories/${id}`),
    createCategory: (data) => api.post("/budget-categories", data),
    updateCategory: (id, data) => api.put(`/budget-categories/${id}`, data),
    deleteCategory: (id) => api.delete(`/budget-categories/${id}`),

    // Government Units
    getGovernmentUnits: () => api.get("/government-units"),
    getGovernmentUnit: (id) => api.get(`/government-units/${id}`),
    createGovernmentUnit: (data) => api.post("/government-units", data),
    updateGovernmentUnit: (id, data) =>
        api.put(`/government-units/${id}`, data),
    deleteGovernmentUnit: (id) => api.delete(`/government-units/${id}`),

    // Fiscal Years
    getFiscalYears: () => api.get("/fiscal-years"),
    getFiscalYear: (id) => api.get(`/fiscal-years/${id}`),
    createFiscalYear: (data) => api.post("/fiscal-years", data),
    updateFiscalYear: (id, data) => api.put(`/fiscal-years/${id}`, data),
    deleteFiscalYear: (id) => api.delete(`/fiscal-years/${id}`),

    // Users
    getUsers: (params) => api.get("/users", { params }),
    getUser: (id) => api.get(`/users/${id}`),
    createUser: (data) => api.post("/users", data),
    updateUser: (id, data) => api.put(`/users/${id}`, data),
    deleteUser: (id) => api.delete(`/users/${id}`),

    // Audit Logs
    getAuditLogs: (params) => api.get("/audit-logs", { params }),
    getAuditLog: (id) => api.get(`/audit-logs/${id}`),
    getAuditLogsByDate: (params) => api.get("/audit-logs/by-date", { params }),
};
