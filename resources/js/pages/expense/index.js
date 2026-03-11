import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { formatCurrency, showToast } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const user = getUser();
const canManage = user && ["admin", "budget-officer"].includes(user.role);

window.expenseManager = () => ({
    showModal: false,
    editMode: false,
    saving: false,
    budgets: [],
    budgetItems: [],
    expenses: [],
    filters: {
        budget_id: "",
        budget_item_id: "",
        from_date: "",
        to_date: "",
    },
    form: {
        id: null,
        budget_item_id: "",
        description: "",
        amount: "",
        transaction_date: new Date().toISOString().split("T")[0],
    },

    async init() {
        await this.loadDropdowns();
        await this.loadExpenses();
    },

    async loadDropdowns() {
        try {
            const [budgetsRes, itemsRes] = await Promise.all([
                api.getBudgets(),
                api.getBudgetItems(),
            ]);
            this.budgets = budgetsRes.data.data;
            this.budgetItems = itemsRes.data.data;
        } catch (error) {
            showToast("Failed to load form data", "error");
        }
    },

    async loadExpenses(page = 1) {
        try {
            const params = { page };

            if (this.filters.budget_id) {
                params.budget_id = this.filters.budget_id;
            }

            if (this.filters.budget_item_id) {
                params.budget_item_id = this.filters.budget_item_id;
            }

            if (this.filters.from_date) {
                params.from_date = this.filters.from_date;
            }

            if (this.filters.to_date) {
                params.to_date = this.filters.to_date;
            }

            // Fetch paginated expenses AND summary statistics
            const [expensesResponse, summaryResponse] = await Promise.all([
                api.getExpenses(params),
                api.getExpenseSummary(params), // Get totals from ALL expenses
            ]);

            this.expenses = expensesResponse.data.data;

            // Update summary cards with overall statistics
            document.getElementById("total-expenses").textContent =
                formatCurrency(summaryResponse.data.total);
            document.getElementById("month-expenses").textContent =
                formatCurrency(summaryResponse.data.month_total);
            document.getElementById("transaction-count").textContent =
                summaryResponse.data.count.toLocaleString();
            document.getElementById("avg-expenses").textContent =
                formatCurrency(summaryResponse.data.average);

            // Render table with current page only
            this.renderTable(this.expenses);
            this.renderPagination(expensesResponse.data);

            document.getElementById("loading").classList.add("hidden");
            document.getElementById("content").classList.remove("hidden");
        } catch (error) {
            showToast("Failed to load expenses", "error");
        }
    },

    calculateSummary(expenses) {
        const total = expenses.reduce(
            (sum, e) => sum + parseFloat(e.amount),
            0,
        );
        const count = expenses.length;
        const avg = count > 0 ? total / count : 0;

        const now = new Date();
        const monthExpenses = expenses.filter((e) => {
            const date = new Date(e.transaction_date);
            return (
                date.getMonth() === now.getMonth() &&
                date.getFullYear() === now.getFullYear()
            );
        });
        const monthTotal = monthExpenses.reduce(
            (sum, e) => sum + parseFloat(e.amount),
            0,
        );

        document.getElementById("total-expenses").textContent =
            formatCurrency(total);
        document.getElementById("month-expenses").textContent =
            formatCurrency(monthTotal);
        document.getElementById("transaction-count").textContent =
            count.toLocaleString();
        document.getElementById("avg-expenses").textContent =
            formatCurrency(avg);
    },

    renderTable(expenses) {
        const container = document.getElementById("expenses-table");

        if (expenses.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 px-4">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No expenses recorded yet</h3>
                    <p class="text-sm text-gray-500 mb-6 text-center max-w-sm">
                        Start recording expenses to track spending and monitor budget utilization.
                    </p>
                    ${
                        canManage
                            ? `
                        <button onclick="document.querySelector('[x-data]')._x_dataStack[0].openModal()"
                            class="px-5 py-2.5 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Record Expense
                        </button>
                    `
                            : ""
                    }
                </div>
            `;
            return;
        }

        const html = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${expenses
                        .map(
                            (e) => `
                        <tr class="group hover:bg-gray-50 cursor-pointer transition-colors duration-150" 
                            onclick="window.location.href='/expenses/${e.id}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600 font-medium">
                                    ${new Date(
                                        e.transaction_date,
                                    ).toLocaleDateString("en-PH", {
                                        year: "numeric",
                                        month: "short",
                                        day: "numeric",
                                    })}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 group-hover:text-[#0d6efd] font-medium transition-colors">
                                    ${e.description}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    ${e.budget_item?.name || "N/A"}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    ${e.budget_item?.budget?.name || "N/A"}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="text-sm font-bold text-red-600">
                                    ${formatCurrency(e.amount)}
                                </span>
                            </td>
                        </tr>
                    `,
                        )
                        .join("")}
                </tbody>
            </table>
        `;
        container.innerHTML = html;
    },

    renderPagination(data) {
        if (!data.last_page || data.last_page <= 1) {
            document.getElementById("pagination").innerHTML = "";
            return;
        }

        const html = `
            <div class="flex justify-center gap-1.5">
                ${Array.from({ length: data.last_page }, (_, i) => i + 1)
                    .map(
                        (p) => `
                    <button onclick="loadExpensesPage(${p})" 
                        class="px-3 py-1.5 text-sm font-medium border rounded-sm transition ${
                            p === data.current_page
                                ? "bg-[#0d6efd] text-white border-[#0d6efd] shadow-sm"
                                : "bg-white text-gray-700 border-gray-300 hover:bg-gray-50"
                        }">
                        ${p}
                    </button>
                `,
                    )
                    .join("")}
            </div>
        `;
        document.getElementById("pagination").innerHTML = html;
    },

    applyFilters() {
        this.loadExpenses();
    },

    clearFilters() {
        this.filters = {
            budget_id: "",
            budget_item_id: "",
            from_date: "",
            to_date: "",
        };
        this.loadExpenses();
    },

    openModal() {
        this.resetForm();
        this.editMode = false;
        this.showModal = true;
    },

    async saveExpense() {
        this.saving = true;
        try {
            const payload = {
                budget_item_id: parseInt(this.form.budget_item_id),
                description: this.form.description,
                amount: parseFloat(this.form.amount),
                transaction_date: this.form.transaction_date,
            };

            await api.createExpense(payload);
            showToast("Expense recorded", "success");

            this.showModal = false;
            await this.loadExpenses();
        } catch (error) {
            const message =
                error.response?.data?.message ||
                error.response?.data?.errors?.transaction_date?.[0] ||
                "Failed to save expense";
            showToast(message, "error");
        } finally {
            this.saving = false;
        }
    },

    resetForm() {
        this.form = {
            id: null,
            budget_item_id: "",
            description: "",
            amount: "",
            transaction_date: new Date().toISOString().split("T")[0],
        };
    },
});

// Global function for pagination
window.loadExpensesPage = (page) => {
    const main = document.querySelector("[x-data]");
    if (main && main._x_dataStack) {
        main._x_dataStack[0].loadExpenses(page);
    }
};

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    if (canManage) {
        document.getElementById("create-btn").classList.remove("hidden");
    }
});
