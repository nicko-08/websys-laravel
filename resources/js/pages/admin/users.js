// /resources/js/pages/admin/users.js
import { isLoggedIn, getUser } from "../../auth.js";
import api from "../../api.js";
import { showToast } from "../../utils.js";

if (!isLoggedIn()) window.location.replace("/login");

const user = getUser();
if (user?.role !== "admin") {
    showToast("Access denied", "error");
    window.location.replace("/dashboard");
}

window.usersManager = () => ({
    showModal: false,
    editMode: false,
    saving: false,
    changePassword: false,
    users: [],
    form: {
        id: null,
        name: "",
        email: "",
        role: "",
        password: "",
        password_confirmation: "",
    },

    async init() {
        await this.loadUsers();
    },

    async loadUsers() {
        try {
            const { data } = await api.getUsers();
            this.users = data.data;
            this.renderTable();
            document.getElementById("loading").classList.add("hidden");
            document.getElementById("content").classList.remove("hidden");
        } catch (error) {
            showToast("Failed to load users", "error");
        }
    },

    renderTable() {
        const roleColors = {
            admin: "bg-red-100 text-red-800",
            "budget-officer": "bg-blue-100 text-blue-800",
            auditor: "bg-purple-100 text-purple-800",
            user: "bg-gray-100 text-gray-800",
        };

        const roleLabels = {
            admin: "Administrator",
            "budget-officer": "Budget Officer",
            auditor: "Auditor",
            user: "User",
        };

        const html = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${this.users
                        .map((u) => {
                            const currentUser = getUser();
                            const isSelf = currentUser?.id === u.id;
                            const created = new Date(
                                u.created_at,
                            ).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "short",
                                day: "numeric",
                            });

                            return `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-[#128a43] text-white rounded-full flex items-center justify-center text-xs font-bold">
                                        ${u.name
                                            .split(" ")
                                            .map((n) => n[0])
                                            .join("")
                                            .toUpperCase()
                                            .slice(0, 2)}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${u.name}${isSelf ? ' <span class="text-xs text-gray-500">(You)</span>' : ""}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">${u.email}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-sm ${roleColors[u.role] || "bg-gray-100 text-gray-800"}">
                                    ${roleLabels[u.role] || u.role}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">${created}</td>
                            <td class="px-6 py-4 text-sm text-right space-x-3">
<button @click="edit(${u.id})" class="text-[#0d6efd] hover:text-blue-800 font-medium cursor-pointer ">Edit</button>                                ${
                                !isSelf
                                    ? `<button @click="deleteUser(${u.id})" class="text-red-600 hover:text-red-800 font-medium cursor-pointer">Delete</button>`
                                    : `<span class="text-gray-400 text-xs">Cannot delete self</span>`
                            }
                            </td>
                        </tr>
                    `;
                        })
                        .join("")}
                </tbody>
            </table>
        `;
        document.getElementById("users-table").innerHTML = html;
    },

    openModal() {
        this.resetForm();
        this.editMode = false;
        this.changePassword = false;
        this.showModal = true;
    },

    async edit(id) {
        const user = this.users.find((u) => u.id === id);
        if (!user) return;

        this.form = {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role,
            password: "",
            password_confirmation: "",
        };
        this.editMode = true;
        this.changePassword = false;
        this.showModal = true;
    },

    async saveUser() {
        this.saving = true;

        try {
            const payload = { ...this.form };

            // Remove password fields if not changing password in edit mode
            if (this.editMode && !this.changePassword) {
                delete payload.password;
                delete payload.password_confirmation;
            }

            // Remove password fields if they're empty
            if (!payload.password) {
                delete payload.password;
                delete payload.password_confirmation;
            }

            if (this.editMode) {
                await api.updateUser(this.form.id, payload);
                showToast("User updated successfully", "success");
            } else {
                await api.createUser(payload);
                showToast("User created successfully", "success");
            }

            this.showModal = false;
            await this.loadUsers();
        } catch (error) {
            const message =
                error.response?.data?.message ||
                error.response?.data?.errors?.email?.[0] ||
                error.response?.data?.errors?.password?.[0] ||
                "Failed to save user";
            showToast(message, "error");
        } finally {
            this.saving = false;
        }
    },

    async deleteUser(id) {
        const user = this.users.find((u) => u.id === id);
        if (!user) return;

        if (
            !confirm(
                `Are you sure you want to delete user "${user.name}"?\n\nThis action cannot be undone.`,
            )
        )
            return;

        try {
            await api.deleteUser(id);
            showToast("User deleted successfully", "success");
            await this.loadUsers();
        } catch (error) {
            showToast(
                error.response?.data?.message || "Failed to delete user",
                "error",
            );
        }
    },

    resetForm() {
        this.form = {
            id: null,
            name: "",
            email: "",
            role: "",
            password: "",
            password_confirmation: "",
        };
    },
});
