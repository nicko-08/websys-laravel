import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { showToast, formatDate } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const user = getUser();
if (user?.role !== "admin") {
    showToast("Access denied", "error");
    window.location.replace("/dashboard");
}

window.fiscalYearsManager = () => ({
    showModal: false,
    editMode: false,
    saving: false,
    years: [],
    form: {
        id: null,
        year: new Date().getFullYear(),
        start_date: "",
        end_date: "",
        is_active: false,
    },

    async init() {
        await this.loadYears();
    },

    async loadYears() {
        try {
            const { data } = await api.getFiscalYears();
            this.years = data.data;
            this.renderTable();
            document.getElementById("loading").classList.add("hidden");
            document.getElementById("content").classList.remove("hidden");
        } catch (error) {
            showToast("Failed to load fiscal years", "error");
        }
    },

    renderTable() {
        const html = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${this.years
                        .map(
                            (y) => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">FY ${y.year}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(y.start_date)} - ${formatDate(y.end_date)}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded ${y.is_active ? "bg-green-100 text-green-800" : "bg-gray-100 text-gray-600"}">
                                    ${y.is_active ? "Active" : "Inactive"}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right space-x-3">
                                <button @click="edit(${y.id})" class="text-[#0d6efd] hover:text-blue-800">Edit</button>
                                <button @click="deleteYear(${y.id})" class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                    `,
                        )
                        .join("")}
                </tbody>
            </table>
        `;
        document.getElementById("years-table").innerHTML = html;
    },

    openModal() {
        this.resetForm();
        this.editMode = false;
        this.showModal = true;
    },

    async edit(id) {
        const year = this.years.find((y) => y.id === id);
        if (!year) return;
        this.form = {
            id: year.id,
            year: year.year,
            start_date: year.start_date,
            end_date: year.end_date,
            is_active: year.is_active,
        };
        this.editMode = true;
        this.showModal = true;
    },

    async saveYear() {
        this.saving = true;
        try {
            const payload = {
                ...this.form,
                is_active: this.form.is_active ? 1 : 0,
            };
            if (this.editMode) {
                await api.updateFiscalYear(this.form.id, payload);
                showToast("Fiscal year updated", "success");
            } else {
                await api.createFiscalYear(payload);
                showToast("Fiscal year created", "success");
            }
            this.showModal = false;
            await this.loadYears();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to save",
                "error",
            );
        } finally {
            this.saving = false;
        }
    },

    async deleteYear(id) {
        if (!confirm("Delete this fiscal year?")) return;
        try {
            await api.deleteFiscalYear(id);
            showToast("Fiscal year deleted", "success");
            await this.loadYears();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to delete",
                "error",
            );
        }
    },

    resetForm() {
        const year = new Date().getFullYear();
        this.form = {
            id: null,
            year,
            start_date: `${year}-01-01`,
            end_date: `${year}-12-31`,
            is_active: false,
        };
    },
});
