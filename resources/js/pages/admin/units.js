import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { showToast } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const user = getUser();
if (user?.role !== "admin") {
    showToast("Access denied", "error");
    window.location.replace("/dashboard");
}

window.unitsManager = () => ({
    showModal: false,
    editMode: false,
    saving: false,
    units: [],

    async init() {
        await this.loadUnits();
    },

    async loadUnits() {
        try {
            const { data } = await api.getGovernmentUnits();
            this.units = data.data;
            this.renderTable();
            document.getElementById("loading").classList.add("hidden");
            document.getElementById("content").classList.remove("hidden");
        } catch (error) {
            showToast("Failed to load units", "error");
        }
    },

    renderTable() {
        const html = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${this.units
                        .map(
                            (u) => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">${u.name}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">${u.type}</td>
                            <td class="px-6 py-4 text-sm text-right space-x-3">
                                <button @click="edit(${u.id})" class="text-[#0d6efd] hover:text-blue-800">Edit</button>
                                <button @click="deleteUnit(${u.id})" class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                    `,
                        )
                        .join("")}
                </tbody>
            </table>
        `;
        document.getElementById("units-table").innerHTML = html;
    },

    openModal() {
        this.resetForm();
        this.editMode = false;
        this.showModal = true;
    },

    async edit(id) {
        const unit = this.units.find((u) => u.id === id);
        if (!unit) return;
        this.form = {
            id: unit.id,
            name: unit.name,
            type: unit.type,
        };
        this.editMode = true;
        this.showModal = true;
    },

    async saveUnit() {
        this.saving = true;
        try {
            if (this.editMode) {
                await api.updateGovernmentUnit(this.form.id, this.form);
                showToast("Unit updated", "success");
            } else {
                await api.createGovernmentUnit(this.form);
                showToast("Unit created", "success");
            }
            this.showModal = false;
            await this.loadUnits();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to save",
                "error",
            );
        } finally {
            this.saving = false;
        }
    },

    async deleteUnit(id) {
        if (!confirm("Delete this unit?")) return;
        try {
            await api.deleteGovernmentUnit(id);
            showToast("Unit deleted", "success");
            await this.loadUnits();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to delete",
                "error",
            );
        }
    },

    resetForm() {
        this.form = { id: null, name: "", code: "", type: "" };
    },
});
