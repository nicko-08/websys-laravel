import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { formatCurrency, showToast } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const budgetId = window.location.pathname.split("/")[2];
const user = getUser();
const canManage = user && ["admin", "budget-officer"].includes(user.role);

window.budgetDetailManager = () => ({
    showModal: false,
    editMode: false,
    saving: false,
    categories: [],
    budget: null,
    items: [],
    form: {
        id: null,
        name: "",
        code: "",
        budget_category_id: "",
        allocated_amount: "",
    },

    async init() {
        await this.loadCategories();
        await this.loadBudget();
    },

    async loadCategories() {
        try {
            const { data } = await api.getCategories();
            this.categories = data.data;
        } catch (error) {
            showToast("Failed to load categories", "error");
        }
    },

    async loadBudget() {
        try {
            const { data } = await api.getBudget(budgetId);
            this.budget = data.data;

            document.getElementById("budget-name").textContent =
                this.budget.name;
            document.getElementById("budget-meta").textContent =
                `${this.budget.government_unit?.name || "N/A"} • FY ${this.budget.fiscal_year?.year || "N/A"}`;
            document.getElementById("budget-amount").textContent =
                formatCurrency(this.budget.total_amount);

            this.items = this.budget.budget_items || [];
            this.renderItems();

            if (canManage)
                document
                    .getElementById("add-item-btn")
                    .classList.remove("hidden");

            document.getElementById("loading").classList.add("hidden");
            document.getElementById("content").classList.remove("hidden");
        } catch (error) {
            alert("Failed to load budget");
            window.location.href = "/budgets";
        }
    },

    renderItems() {
        const container = document.getElementById("items-table");

        if (this.items.length === 0) {
            container.innerHTML =
                '<p class="px-6 py-8 text-center text-sm text-gray-500">No budget items allocated yet.</p>';
            return;
        }

        const html = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                        ${canManage ? '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>' : ""}
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${this.items
                        .map(
                            (item) => `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">${item.name}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${item.code}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${item.category?.name || "N/A"}</td>
                            <td class="px-6 py-4 text-sm text-right font-semibold text-[#128a43]">${formatCurrency(item.allocated_amount)}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-700">${formatCurrency(item.expenses_sum_amount || 0)}</td>
                            ${
                                canManage
                                    ? `
                                <td class="px-6 py-4 text-sm text-right space-x-3 font-medium">
                                    <button @click="editItem(${item.id})" class="text-[#0d6efd] hover:text-blue-800 transition">Edit</button>
                                    <button @click="deleteItem(${item.id})" class="text-red-600 hover:text-red-800 transition">Delete</button>
                                </td>
                            `
                                    : ""
                            }
                        </tr>
                    `,
                        )
                        .join("")}
                </tbody>
            </table>
        `;
        container.innerHTML = html;
    },

    openModal() {
        this.resetForm();
        this.editMode = false;
        this.showModal = true;
    },

    async editItem(id) {
        const item = this.items.find((i) => i.id === id);
        if (!item) return;

        this.form = {
            id: item.id,
            name: item.name,
            code: item.code,
            budget_category_id: item.budget_category_id,
            allocated_amount: item.allocated_amount,
        };
        this.editMode = true;
        this.showModal = true;
    },

    async saveItem() {
        this.saving = true;
        try {
            const payload = {
                budget_id: parseInt(budgetId),
                name: this.form.name,
                code: this.form.code,
                budget_category_id: parseInt(this.form.budget_category_id),
                allocated_amount: parseFloat(this.form.allocated_amount),
            };

            if (this.editMode) {
                await api.updateBudgetItem(this.form.id, payload);
                showToast("Budget item updated", "success");
            } else {
                await api.createBudgetItem(payload);
                showToast("Budget item created", "success");
            }

            this.showModal = false;
            await this.loadBudget();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to save",
                "error",
            );
        } finally {
            this.saving = false;
        }
    },

    async deleteItem(id) {
        if (!confirm("Delete this budget item?")) return;
        try {
            await api.deleteBudgetItem(id);
            showToast("Budget item deleted", "success");
            await this.loadBudget();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to delete",
                "error",
            );
        }
    },

    resetForm() {
        this.form = {
            id: null,
            name: "",
            code: "",
            budget_category_id: "",
            allocated_amount: "",
        };
    },
});

window.editItem = (id) => {
    const main = document.querySelector("[x-data]");
    if (main && main._x_dataStack) {
        main._x_dataStack[0].edit(id);
    }
};

window.deleteItem = (id) => {
    const main = document.querySelector("[x-data]");
    if (main && main._x_dataStack) {
        main._x_dataStack[0].deleteItem(id);
    }
};
