import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { formatCurrency, showToast } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const user = getUser();
const canManage = user && ["admin", "budget-officer"].includes(user.role);

window.budgetManager = () => ({
    showModal: false,
    editMode: false,
    saving: false,
    governmentUnits: [],
    fiscalYears: [],
    form: {
        id: null,
        name: "",
        government_unit_id: "",
        fiscal_year_id: "",
        total_amount: "",
    },

    async init() {
        await this.loadDropdowns();
    },

    async loadDropdowns() {
        try {
            const [units, years] = await Promise.all([
                api.getGovernmentUnits(),
                api.getFiscalYears(),
            ]);
            this.governmentUnits = units.data.data;
            this.fiscalYears = years.data.data;
        } catch (error) {
            showToast("Failed to load form data", "error");
        }
    },

    openModal() {
        this.resetForm();
        this.editMode = false;
        this.showModal = true;
    },

    async edit(id) {
        try {
            const { data } = await api.getBudget(id);
            this.form = {
                id: data.data.id,
                name: data.data.name,
                government_unit_id: data.data.government_unit_id,
                fiscal_year_id: data.data.fiscal_year_id,
                total_amount: data.data.total_amount,
            };
            this.editMode = true;
            this.showModal = true;
        } catch (error) {
            showToast("Failed to load budget", "error");
        }
    },

    async saveBudget() {
        this.saving = true;
        try {
            const payload = {
                name: this.form.name,
                government_unit_id: parseInt(this.form.government_unit_id),
                fiscal_year_id: parseInt(this.form.fiscal_year_id),
                total_amount: parseFloat(this.form.total_amount),
            };

            if (this.editMode) {
                await api.updateBudget(this.form.id, payload);
                showToast("Budget updated", "success");
            } else {
                await api.createBudget(payload);
                showToast("Budget created", "success");
            }

            this.showModal = false;
            loadBudgets();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to save",
                "error",
            );
        } finally {
            this.saving = false;
        }
    },

    resetForm() {
        this.form = {
            id: null,
            name: "",
            government_unit_id: "",
            fiscal_year_id: "",
            total_amount: "",
        };
    },
});

document.addEventListener("DOMContentLoaded", () => {
    if (canManage)
        document.getElementById("create-btn").classList.remove("hidden");
    loadBudgets();
});

async function loadBudgets(page = 1) {
    try {
        const { data } = await api.getBudgets({ page });
        renderTable(data.data);
        renderPagination(data);
        document.getElementById("loading").classList.add("hidden");
        document.getElementById("content").classList.remove("hidden");
    } catch (error) {
        showToast(
            error.response?.data?.message || "Failed to load budgets",
            "error",
        );
    }
}

function renderTable(budgets) {
    const container = document.getElementById("budgets-table");

    if (budgets.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No budgets found</h3>
                <p class="text-sm text-gray-500 mb-6 text-center max-w-sm">
                    Create your first budget to start tracking allocations and expenses.
                </p>
            </div>
        `;
        return;
    }

    const html = `
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Government Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiscal Year</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                ${budgets
                    .map(
                        (b) => `
                    <tr class="group hover:bg-gray-50 cursor-pointer transition-colors duration-150" 
                        onclick="window.location.href='/budgets/${b.id}'">
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900 group-hover:text-[#0d6efd] font-medium transition-colors">
                                ${b.name}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                ${b.government_unit?.name || "N/A"}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                FY ${b.fiscal_year?.year || "N/A"}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-bold text-[#128a43]">
                                ${formatCurrency(b.total_amount)}
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
}

function renderPagination(data) {
    if (data.last_page <= 1) return;
    const html = `
        <div class="flex justify-center gap-1.5">
            ${Array.from({ length: data.last_page }, (_, i) => i + 1)
                .map(
                    (p) => `
                <button onclick="loadBudgets(${p})" 
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
}

window.deleteBudget = async (id) => {
    if (!confirm("Delete this budget?")) return;
    try {
        await api.deleteBudget(id);
        showToast("Budget deleted", "success");
        loadBudgets();
    } catch (error) {
        showToast(error.response?.data?.message || "Failed to delete", "error");
    }
};

window.editBudget = async (id) => {
    const main = document.querySelector("[x-data]");
    if (main && main._x_dataStack) {
        const component = main._x_dataStack[0];
        await component.edit(id);
    }
};

window.loadBudgets = loadBudgets;
