import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { formatCurrency, showToast } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const expenseId = window.location.pathname.split("/")[2];
const user = getUser();
const canManage = user && ["admin", "budget-officer"].includes(user.role);

window.expenseDetailManager = () => ({
    expense: null,
    budgetItems: [],
    showEditModal: false,
    saving: false,
    editForm: {
        budget_item_id: "",
        description: "",
        amount: "",
        transaction_date: "",
    },

    async init() {
        await this.loadBudgetItems();
        await this.loadExpense();
    },

    async loadBudgetItems() {
        try {
            const { data } = await api.getBudgetItems();
            this.budgetItems = data.data;
        } catch (error) {
            console.error("Failed to load budget items:", error);
        }
    },

    async loadExpense() {
        try {
            const { data } = await api.getExpense(expenseId);
            this.expense = data.data;

            // Populate expense details
            document.getElementById("expense-date").textContent = new Date(
                this.expense.transaction_date,
            ).toLocaleDateString("en-PH", {
                year: "numeric",
                month: "long",
                day: "numeric",
            });

            document.getElementById("expense-amount").textContent =
                formatCurrency(this.expense.amount);

            document.getElementById("expense-description").textContent =
                this.expense.description;

            // Populate budget item details
            const budgetItem = this.expense.budget_item;
            if (budgetItem) {
                document.getElementById("budget-item-name").textContent =
                    budgetItem.name;
                document.getElementById("budget-category").textContent =
                    budgetItem.category?.name || "N/A";
                document.getElementById("budget-name").textContent =
                    budgetItem.budget?.name || "N/A";
                document.getElementById("allocated-amount").textContent =
                    formatCurrency(budgetItem.allocated_amount);

                // Calculate and show spending
                const totalSpent = parseFloat(
                    budgetItem.expenses_sum_amount || 0,
                );
                const allocated = parseFloat(budgetItem.allocated_amount);
                const remaining = allocated - totalSpent;
                const utilizationRate =
                    allocated > 0 ? (totalSpent / allocated) * 100 : 0;

                document.getElementById("total-spent").textContent =
                    formatCurrency(totalSpent);
                document.getElementById("remaining-budget").textContent =
                    formatCurrency(remaining);
                document.getElementById("utilization-rate").textContent =
                    utilizationRate.toFixed(1) + "%";
            }

            // Show actions if user can manage
            if (canManage) {
                document
                    .getElementById("actions-section")
                    .classList.remove("hidden");
            }

            document.getElementById("loading").classList.add("hidden");
            document.getElementById("content").classList.remove("hidden");
        } catch (error) {
            showToast("Failed to load expense details", "error");
            setTimeout(() => {
                window.location.href = "/expenses";
            }, 2000);
        }
    },

    openEditModal() {
        // Populate form with current expense data
        this.editForm = {
            budget_item_id: this.expense.budget_item?.id || "",
            description: this.expense.description,
            amount: this.expense.amount,
            transaction_date: this.expense.transaction_date,
        };
        this.showEditModal = true;
    },

    closeEditModal() {
        this.showEditModal = false;
        this.resetForm();
    },

    async saveExpense() {
        this.saving = true;
        try {
            const payload = {
                budget_item_id: parseInt(this.editForm.budget_item_id),
                description: this.editForm.description,
                amount: parseFloat(this.editForm.amount),
                transaction_date: this.editForm.transaction_date,
            };

            await api.updateExpense(expenseId, payload);
            showToast("Expense updated successfully", "success");

            this.showEditModal = false;

            // Reload the expense data
            await this.loadExpense();
        } catch (error) {
            const message =
                error.response?.data?.message ||
                error.response?.data?.errors?.transaction_date?.[0] ||
                "Failed to update expense";
            showToast(message, "error");
        } finally {
            this.saving = false;
        }
    },

    async deleteExpenseAction() {
        if (!confirm("Delete this expense? This action cannot be undone."))
            return;

        try {
            await api.deleteExpense(expenseId);
            showToast("Expense deleted successfully", "success");
            setTimeout(() => {
                window.location.href = "/expenses";
            }, 1500);
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to delete expense",
                "error",
            );
        }
    },

    resetForm() {
        this.editForm = {
            budget_item_id: "",
            description: "",
            amount: "",
            transaction_date: "",
        };
    },
});
