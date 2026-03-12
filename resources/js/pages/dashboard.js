import { isLoggedIn, getUser } from "../auth.js";
import api from "../api.js";
import { formatCurrency, formatPercent } from "../utils.js";
import { Chart, registerables } from "chart.js";

// Register Chart.js components
Chart.register(...registerables);

Chart.defaults.font.family =
    "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif";
Chart.defaults.color = "#64748b";
Chart.defaults.scale.grid.color = "rgba(148, 163, 184, 0.06)";
Chart.defaults.scale.grid.lineWidth = 1;
Chart.defaults.plugins.tooltip.backgroundColor = "rgba(255, 255, 255, 0.98)";
Chart.defaults.plugins.tooltip.titleColor = "#1e293b";
Chart.defaults.plugins.tooltip.bodyColor = "#475569";
Chart.defaults.plugins.tooltip.borderColor = "#e2e8f0";
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.boxPadding = 6;
Chart.defaults.plugins.tooltip.usePointStyle = true;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.caretSize = 6;
Chart.defaults.plugins.tooltip.displayColors = true;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.padding = 16;
Chart.defaults.plugins.legend.labels.font = { size: 12, weight: "500" };

if (!isLoggedIn()) {
    window.location.replace("/login");
}

let budgetOverviewChart = null;
let utilizationChart = null;
let categoryChart = null;

document.addEventListener("DOMContentLoaded", async () => {
    const user = getUser();

    if (user) {
        document.getElementById("user-name").textContent = user.name;
        document.getElementById("user-name-full").textContent = user.name;
        document.getElementById("user-email").textContent = user.email;
        document.getElementById("user-role-badge").textContent =
            user.role.replace(/-/g, " ");

        const initials = user.name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .slice(0, 2);
        document.getElementById("user-initials").textContent = initials;
    }

    await loadDashboardData();
});

async function loadDashboardData() {
    const loadingEl = document.getElementById("dashboard-loading");
    const contentEl = document.getElementById("dashboard-content");
    const errorEl = document.getElementById("dashboard-error");

    try {
        loadingEl?.classList.remove("hidden");
        contentEl?.classList.add("hidden");
        errorEl?.classList.add("hidden");

        const { data: summaryResponse } = await api.getOverallSummary();
        const summary = summaryResponse.data || summaryResponse;

        if (!summary) throw new Error("No active fiscal year found");

        animateValue("total-allocated", 0, summary.total_allocated, 1000);
        animateValue("total-spent", 0, summary.total_spent, 1000);
        animateValue("remaining-budget", 0, summary.remaining_budget, 1000);

        document.getElementById("utilization-text").textContent = formatPercent(
            summary.utilization_rate,
        );

        document.getElementById("chart-allocated").textContent = formatCurrency(
            summary.total_allocated,
        );
        document.getElementById("chart-spent").textContent = formatCurrency(
            summary.total_spent,
        );
        document.getElementById("chart-remaining").textContent = formatCurrency(
            summary.remaining_budget,
        );

        setTimeout(() => {
            createBudgetOverviewChart(summary);
            createUtilizationGauge(summary.utilization_rate);
            createCategoryChart(summary.spending_by_category);
        }, 100);

        // Fetch recent budgets
        const { data: budgetsData } = await api.getBudgets({ per_page: 5 });
        renderRecentBudgets(budgetsData.data || []);

        loadingEl?.classList.add("hidden");
        contentEl?.classList.remove("hidden");
    } catch (error) {
        console.error("Dashboard error:", error);
        loadingEl?.classList.add("hidden");

        const errorMessage =
            error.response?.data?.message ||
            error.message ||
            "Failed to load dashboard data";
        document.getElementById("error-message").textContent = errorMessage;
        errorEl?.classList.remove("hidden");
    }
}

// Animate number counting
function animateValue(id, start, end, duration) {
    const element = document.getElementById(id);
    if (!element) return;
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    const timer = setInterval(() => {
        current += increment;
        if (
            (increment > 0 && current >= end) ||
            (increment < 0 && current <= end)
        ) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = formatCurrency(current);
    }, 16);
}

// Doughnut Chart
function createBudgetOverviewChart(summary) {
    const ctx = document.getElementById("budget-overview-chart");
    if (!ctx) return;
    if (budgetOverviewChart) budgetOverviewChart.destroy();

    budgetOverviewChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Spent", "Remaining"],
            datasets: [
                {
                    data: [summary.total_spent, summary.remaining_budget],
                    backgroundColor: ["#94a3b8", "#128a43"],
                    hoverBackgroundColor: ["#64748b", "#0f7236"],
                    borderWidth: 0,
                    hoverOffset: 6,
                    borderRadius: 3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            cutout: "72%",
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const value = formatCurrency(context.parsed);
                            const total =
                                summary.total_spent + summary.remaining_budget;
                            const percent =
                                total > 0
                                    ? ((context.parsed / total) * 100).toFixed(
                                          1,
                                      )
                                    : 0;
                            return ` ${context.label}: ${value} (${percent}%)`;
                        },
                    },
                },
            },
        },
    });
}

// Gauge
function createUtilizationGauge(utilizationRate) {
    const ctx = document.getElementById("utilization-chart");
    if (!ctx) return;
    if (utilizationChart) utilizationChart.destroy();

    const rate = parseFloat(utilizationRate);
    const remaining = Math.max(0, 100 - rate);

    const getGaugeColor = (r) => {
        if (r >= 95) return "#f97316";
        if (r >= 85) return "#f59e0b";
        if (r >= 70) return "#22c55e";
        if (r >= 50) return "#16a34a";
        return "#128a43";
    };

    utilizationChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            datasets: [
                {
                    data: [rate, remaining],
                    backgroundColor: [getGaugeColor(rate), "#f1f5f9"],
                    borderWidth: 0,
                    borderRadius: 4,
                    circumference: 180,
                    rotation: 270,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            cutout: "78%",
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
            },
        },
    });
}

// Bar Chart
function createCategoryChart(categories) {
    const ctx = document.getElementById("category-chart");
    if (!ctx) return;
    if (categoryChart) categoryChart.destroy();

    if (!categories || Object.keys(categories).length === 0) {
        ctx.parentElement.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-center py-8">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">No spending data available</p>
            </div>`;
        return;
    }

    const labels = Object.keys(categories);
    const data = Object.values(categories);

    categoryChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Amount Spent",
                    data,
                    backgroundColor: "#94a3b8",
                    hoverBackgroundColor: "#64748b",
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: "flex",
                    maxBarThickness: 32,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { top: 10, bottom: 0 },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => items[0].label,
                        label: (ctx) =>
                            ` Spent: ${formatCurrency(ctx.parsed.y)}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: "#94a3b8",
                        font: { size: 11, weight: "400" },
                        maxRotation: 0,
                        minRotation: 0,
                        callback: function (value) {
                            let label = this.getLabelForValue(value) || "";
                            return label.length > 12
                                ? label.substring(0, 12) + "..."
                                : label;
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: {
                        color: "rgba(148, 163, 184, 0.06)",
                        drawBorder: false,
                    },
                    ticks: {
                        maxTicksLimit: 6,
                        color: "#94a3b8",
                        font: { size: 11, weight: "400" },
                        callback: (value) =>
                            "₱" +
                            (value >= 1000000
                                ? (value / 1000000).toFixed(1) + "M"
                                : value >= 1000
                                  ? (value / 1000).toFixed(0) + "K"
                                  : value),
                    },
                },
            },
        },
    });
}

function renderRecentBudgets(budgets) {
    const container = document.getElementById("recent-budgets");
    if (!container) return;

    if (budgets.length === 0) {
        container.innerHTML = `<div class="flex flex-col items-center justify-center py-12 px-4"><p class="text-sm font-medium text-gray-500">No budgets found</p></div>`;
        return;
    }

    const html = budgets
        .map(
            (b) => `
        <div class="px-6 py-4 border-b border-gray-50 hover:bg-gray-50/80 transition-colors cursor-pointer group" onclick="window.location.href='/budgets/${b.id}'">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-[#128a43] transition-colors">${b.name}</p>
                    <p class="text-xs text-gray-500 mt-1">${b.government_unit?.name || "N/A"} • FY ${b.fiscal_year?.year || "N/A"}</p>
                </div>
                <div class="ml-4 flex-shrink-0 text-right">
                    <p class="text-sm font-semibold text-gray-900">${formatCurrency(b.total_amount)}</p>
                </div>
            </div>
        </div>
    `,
        )
        .join("");
    container.innerHTML = html;
}
