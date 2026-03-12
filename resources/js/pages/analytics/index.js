import { isLoggedIn } from "../../auth.js";
import api from "../../api.js";
import { formatCurrency as formatCurrencyUtil } from "../../utils.js";
import { Chart, registerables } from "chart.js";

// Register Chart.js components
Chart.register(...registerables);

// GLOBAL CHART THEME
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

window.analyticsPage = function () {
    return {
        isLoading: true,
        hasError: false,
        isEmpty: false,
        errorMessage: "",
        barangays: [],
        summary: null,
        stats: {
            totalAllocated: 0,
            totalSpent: 0,
            avgUtilization: 0,
            barangayCount: 0,
        },
        charts: {
            allocation: null,
            utilization: null,
            radar: null,
            category: null,
            comparison: null,
        },

        // Palette
        brandColors: [
            "#128a43",
            "#16a34a",
            "#22c55e",
            "#4ade80",
            "#86efac",
            "#bbf7d0",
            "#d1fae5",
        ],

        init() {
            this.loadAnalyticsData();
            document.addEventListener("visibilitychange", () => {
                if (!document.hidden) this.loadAnalyticsData();
            });
            this.pollInterval = setInterval(() => {
                if (!document.hidden && !this.isLoading)
                    this.loadAnalyticsData();
            }, 30000);
        },

        formatCurrency(amount) {
            return formatCurrencyUtil(amount || 0);
        },

        retryLoad() {
            this.hasError = false;
            this.errorMessage = "";
            this.loadAnalyticsData();
        },

        async loadAnalyticsData() {
            this.isLoading = true;
            this.hasError = false;
            this.isEmpty = false;

            try {
                const [barangaysResponse, summaryResponse] = await Promise.all([
                    api.getBarangayList(),
                    api.getOverallSummary(),
                ]);

                if (!barangaysResponse?.data || !summaryResponse?.data)
                    throw new Error("Invalid response from server");

                this.barangays = Array.isArray(barangaysResponse.data)
                    ? barangaysResponse.data
                    : barangaysResponse.data.data || [];
                this.summary =
                    summaryResponse.data.data || summaryResponse.data;

                if (this.barangays.length === 0) {
                    this.isEmpty = true;
                    this.isLoading = false;
                    return;
                }

                this.calculateStats();
                await this.$nextTick();
                setTimeout(() => this.renderAllCharts(), 100);
                this.isLoading = false;
            } catch (error) {
                this.hasError = true;
                this.errorMessage =
                    error.response?.data?.message ||
                    "Failed to load analytics data. Please try again.";
                this.isLoading = false;
            }
        },

        calculateStats() {
            this.stats = {
                totalAllocated: parseFloat(this.summary?.total_allocated || 0),
                totalSpent: parseFloat(this.summary?.total_spent || 0),
                avgUtilization:
                    this.barangays.length > 0
                        ? this.barangays.reduce(
                              (sum, b) =>
                                  sum + (parseFloat(b.utilization_rate) || 0),
                              0,
                          ) / this.barangays.length
                        : 0,
                barangayCount: this.barangays.length,
            };
        },

        renderAllCharts() {
            try {
                this.createAllocationDoughnut();
                this.createUtilizationLine();
                this.createPerformanceRadar();
                this.createCategoryHorizontal();
                this.createComparisonChart();
            } catch (error) {
                console.error("Chart rendering error:", error);
            }
        },

        destroyChart(chartKey) {
            if (this.charts[chartKey]) {
                this.charts[chartKey].destroy();
                this.charts[chartKey] = null;
            }
        },

        getCanvas(id) {
            return document.getElementById(id);
        },

        // Doughnut Chart
        createAllocationDoughnut() {
            const canvas = this.getCanvas("allocation-doughnut");
            if (!canvas) return;
            this.destroyChart("allocation");

            const labels = this.barangays.map(
                (b) => b.barangay_name || "Unknown",
            );
            const data = this.barangays.map(
                (b) => parseFloat(b.total_allocated) || 0,
            );

            this.charts.allocation = new Chart(canvas, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: data,
                            backgroundColor: this.brandColors,
                            borderWidth: 0,
                            hoverOffset: 6,
                            borderRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "72%",
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                usePointStyle: true,
                                pointStyle: "circle",
                                padding: 16,
                                font: {
                                    size: 12,
                                    weight: "500",
                                    family: "Inter, sans-serif",
                                },
                                color: "#64748b",
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const total = context.dataset.data.reduce(
                                        (a, b) => a + b,
                                        0,
                                    );
                                    const percent =
                                        total > 0
                                            ? (
                                                  (context.parsed / total) *
                                                  100
                                              ).toFixed(1)
                                            : 0;
                                    return ` ${context.label}: ${formatCurrencyUtil(context.parsed)} (${percent}%)`;
                                },
                            },
                        },
                    },
                },
            });
        },

        // Line Chart
        createUtilizationLine() {
            const canvas = this.getCanvas("utilization-line");
            if (!canvas) return;
            this.destroyChart("utilization");

            const ctx = canvas.getContext("2d");
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, "rgba(18, 138, 67, 0.08)");
            gradient.addColorStop(1, "rgba(18, 138, 67, 0)");

            const labels = this.barangays.map(
                (b) => b.barangay_name || "Unknown",
            );
            const data = this.barangays.map(
                (b) => parseFloat(b.utilization_rate) || 0,
            );

            const maxUtil = Math.max(...data);
            const yMax = maxUtil > 100 ? Math.ceil(maxUtil / 100) * 100 : 100;

            this.charts.utilization = new Chart(canvas, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Utilization Rate",
                            data: data,
                            borderColor: "#128a43",
                            backgroundColor: gradient,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointBackgroundColor: "#ffffff",
                            pointBorderColor: "#128a43",
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: "#128a43",
                            pointHoverBorderColor: "#ffffff",
                            pointHoverBorderWidth: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: "index",
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                label: (context) =>
                                    `${context.parsed.y.toFixed(2)}% utilized`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: "#94a3b8",
                                font: {
                                    size: 11,
                                    weight: "400",
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            max: yMax,
                            grid: {
                                color: "rgba(148, 163, 184, 0.06)",
                                drawBorder: false,
                            },
                            ticks: {
                                callback: (value) => value + "%",
                                maxTicksLimit: 6,
                                color: "#94a3b8",
                                font: {
                                    size: 11,
                                    weight: "400",
                                },
                            },
                            border: { display: false },
                        },
                    },
                },
            });
        },

        // Radar Chart
        createPerformanceRadar() {
            const canvas = this.getCanvas("performance-radar");
            if (!canvas) return;
            this.destroyChart("radar");

            const maxAllocated = Math.max(
                ...this.barangays.map(
                    (b) => parseFloat(b.total_allocated) || 0,
                ),
            );
            const maxSpent = Math.max(
                ...this.barangays.map((b) => parseFloat(b.total_spent) || 0),
            );

            if (maxAllocated === 0 && maxSpent === 0) return;

            const classicColors = [
                { border: "rgb(54, 162, 235)", bg: "rgba(54, 162, 235, 0.2)" },
                { border: "rgb(255, 99, 132)", bg: "rgba(255, 99, 132, 0.2)" },
                { border: "rgb(75, 192, 192)", bg: "rgba(75, 192, 192, 0.2)" },
            ];

            const datasets = this.barangays.slice(0, 3).map((b, i) => {
                const utilization = parseFloat(b.utilization_rate) || 0;

                let budgetAdherence;
                if (utilization <= 100) {
                    budgetAdherence = utilization;
                } else {
                    budgetAdherence = Math.max(
                        0,
                        100 - Math.pow((utilization - 100) / 100, 0.8) * 50,
                    );
                }

                const colorTheme = classicColors[i % classicColors.length];

                return {
                    label: b.barangay_name || "Unknown",
                    data: [
                        maxAllocated > 0
                            ? ((parseFloat(b.total_allocated) || 0) /
                                  maxAllocated) *
                              100
                            : 0,
                        maxSpent > 0
                            ? ((parseFloat(b.total_spent) || 0) / maxSpent) *
                              100
                            : 0,
                        budgetAdherence,
                        b.status === "Under Budget"
                            ? 100
                            : b.status === "At Limit"
                              ? 80
                              : b.status === "Near Limit"
                                ? 50
                                : 20,
                    ],
                    borderColor: colorTheme.border,
                    backgroundColor: colorTheme.bg,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: colorTheme.border,
                    pointBorderColor: "#FFFFFF",
                    pointBorderWidth: 1,
                    pointHoverBackgroundColor: "#FFFFFF",
                    pointHoverBorderColor: colorTheme.border,
                    pointHoverBorderWidth: 2,
                };
            });

            this.charts.radar = new Chart(canvas, {
                type: "radar",
                data: {
                    labels: [
                        "Budget Size",
                        "Spending",
                        "Budget Adherence",
                        "Health",
                    ],
                    datasets: datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: "500",
                                },
                                color: "#64748b",
                            },
                        },
                        tooltip: {
                            callbacks: {
                                title: (context) => context[0].dataset.label,
                                label: (context) => {
                                    const labels = [
                                        "Budget Size",
                                        "Spending",
                                        "Budget Adherence",
                                        "Health",
                                    ];
                                    const value = context.parsed.r.toFixed(0);
                                    return ` ${labels[context.dataIndex]}: ${value}/100`;
                                },
                            },
                        },
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            ticks: {
                                display: true,
                                stepSize: 20,
                                backdropColor: "transparent",
                                color: "#94a3b8",
                                font: {
                                    size: 10,
                                },
                            },
                            grid: {
                                color: "rgba(148, 163, 184, 0.15)",

                                lineWidth: 1,
                            },
                            angleLines: {
                                color: "rgba(148, 163, 184, 0.15)",
                                lineWidth: 1,
                            },
                            pointLabels: {
                                font: {
                                    size: 12,
                                    weight: "500",
                                },
                                color: "#475569",
                                padding: 10,
                            },
                        },
                    },
                    interaction: {
                        mode: "point",
                        intersect: true,
                    },
                },
            });
        },

        // Horizontal Bar Chart
        createCategoryHorizontal() {
            const canvas = this.getCanvas("category-horizontal");
            if (!canvas) return;
            this.destroyChart("category");

            const categoryData = this.summary?.spending_by_category || {};
            let parsed =
                typeof categoryData === "string"
                    ? JSON.parse(categoryData)
                    : categoryData;

            let entries = Array.isArray(parsed)
                ? parsed.map((item) => [
                      item.name || "Unknown",
                      parseFloat(item.total_spent) || 0,
                  ])
                : Object.entries(parsed).map(([name, value]) => [
                      name,
                      parseFloat(value) || 0,
                  ]);

            entries = entries
                .filter(([, v]) => v > 0)
                .sort((a, b) => b[1] - a[1]);
            if (entries.length === 0) return;

            this.charts.category = new Chart(canvas, {
                type: "bar",
                data: {
                    labels: entries.map(([name]) => name),
                    datasets: [
                        {
                            label: "Spending",
                            data: entries.map(([, value]) => value),
                            backgroundColor: "#94a3b8",
                            hoverBackgroundColor: "#64748b",
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 22,
                        },
                    ],
                },
                options: {
                    indexAxis: "y",
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 0,
                            right: 30,
                            top: 10,
                            bottom: 10,
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                label: (ctx) =>
                                    `${formatCurrencyUtil(ctx.parsed.x)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(148, 163, 184, 0.06)",
                                drawBorder: false,
                            },
                            border: { display: false },
                            ticks: {
                                maxTicksLimit: 5,
                                color: "#94a3b8",
                                font: { size: 11, weight: "400" },
                                callback: (v) =>
                                    "₱" + (v / 1000000).toFixed(1) + "M",
                            },
                        },
                        y: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: "#64748b",
                                font: {
                                    size: 12,
                                    weight: "500",
                                },
                                callback: function (value) {
                                    let label =
                                        this.getLabelForValue(value) || "";
                                    return label.length > 22
                                        ? label.substring(0, 22) + "..."
                                        : label;
                                },
                            },
                        },
                    },
                },
            });
        },

        // Comparison Bar Chart
        createComparisonChart() {
            const canvas = this.getCanvas("comparison-chart");
            if (!canvas) return;
            this.destroyChart("comparison");

            this.charts.comparison = new Chart(canvas, {
                type: "bar",
                data: {
                    labels: this.barangays.map(
                        (b) => b.barangay_name || "Unknown",
                    ),
                    datasets: [
                        {
                            label: "Allocated",
                            data: this.barangays.map(
                                (b) => parseFloat(b.total_allocated) || 0,
                            ),
                            backgroundColor: "#128a43",
                            hoverBackgroundColor: "#0f7236",
                            borderRadius: 8,
                            borderSkipped: false,
                            barThickness: "flex",
                            maxBarThickness: 32,
                        },
                        {
                            label: "Spent",
                            data: this.barangays.map(
                                (b) => parseFloat(b.total_spent) || 0,
                            ),
                            backgroundColor: "#94a3b8",
                            hoverBackgroundColor: "#64748b",
                            borderRadius: 8,
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
                        padding: {
                            left: 10,
                            right: 10,
                            top: 20,
                            bottom: 0,
                        },
                    },
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                usePointStyle: true,
                                pointStyle: "circle",
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: "500",
                                },
                                color: "#64748b",
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) =>
                                    ` ${ctx.dataset.label}: ${formatCurrencyUtil(ctx.parsed.y)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: "#94a3b8",
                                font: {
                                    size: 11,
                                    weight: "400",
                                },
                                maxRotation: 0,
                                minRotation: 0,
                                callback: function (value) {
                                    let label =
                                        this.getLabelForValue(value) || "";
                                    return label.length > 12
                                        ? label.substring(0, 12) + "..."
                                        : label;
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(148, 163, 184, 0.06)",
                                drawBorder: false,
                            },
                            border: { display: false },
                            ticks: {
                                maxTicksLimit: 6,
                                color: "#94a3b8",
                                font: {
                                    size: 11,
                                    weight: "400",
                                },
                                callback: (v) => {
                                    if (v >= 1000000)
                                        return (
                                            "₱" + (v / 1000000).toFixed(1) + "M"
                                        );
                                    if (v >= 1000)
                                        return (
                                            "₱" + (v / 1000).toFixed(0) + "K"
                                        );
                                    return "₱" + v;
                                },
                            },
                        },
                    },
                },
            });
        },
    };
};
