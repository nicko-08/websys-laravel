import { isLoggedIn } from "../../auth.js";
import api from "../../api.js";
import { formatCurrency as formatCurrencyUtil } from "../../utils.js";
import { Chart, registerables } from "chart.js";

// Register Chart.js components
Chart.register(...registerables);

// eALLOC THEME GLOBAL CHART.JS OVERRIDES
Chart.defaults.font.family =
    "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif";
Chart.defaults.color = "#6B7280";
Chart.defaults.scale.grid.color = "#F3F4F6";
Chart.defaults.plugins.tooltip.backgroundColor = "#FFFFFF";
Chart.defaults.plugins.tooltip.titleColor = "#111827";
Chart.defaults.plugins.tooltip.bodyColor = "#4B5563";
Chart.defaults.plugins.tooltip.borderColor = "#E5E7EB";
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.boxPadding = 4;
Chart.defaults.plugins.tooltip.usePointStyle = true;

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

        // eAlloc Brand Palette for Charts
        brandColors: [
            "#0d6efd",
            "#128a43",
            "#f59e0b",
            "#6366f1",
            "#ec4899",
            "#14b8a6",
            "#f43f5e",
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

        // Doughnut
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
                            hoverOffset: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "75%",
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: { usePointStyle: true, padding: 20 },
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
                                    return ` ${formatCurrencyUtil(context.parsed)} (${percent}%)`;
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
            gradient.addColorStop(0, "rgba(13, 110, 253, 0.15)");
            gradient.addColorStop(1, "rgba(13, 110, 253, 0)");

            const labels = this.barangays.map(
                (b) => b.barangay_name || "Unknown",
            );
            const data = this.barangays.map(
                (b) => parseFloat(b.utilization_rate) || 0,
            );

            // Find max utilization to set appropriate scale
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
                            borderColor: "#0d6efd",
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: "#ffffff",
                            pointBorderColor: "#0d6efd",
                            pointBorderWidth: 2.5,
                            pointHoverBackgroundColor: "#0d6efd",
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
                            backgroundColor: "rgba(255, 255, 255, 0.95)",
                            titleColor: "#111827",
                            bodyColor: "#4B5563",
                            borderColor: "#E5E7EB",
                            borderWidth: 1,
                            padding: 12,
                            titleFont: {
                                size: 13,
                                weight: "600",
                            },
                            bodyFont: {
                                size: 12,
                            },
                            displayColors: false,
                            callbacks: {
                                label: (context) =>
                                    ` ${context.parsed.y.toFixed(2)}% utilized`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: "#6B7280",
                                font: {
                                    size: 12,
                                    weight: "500",
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            max: yMax,
                            grid: {
                                color: "#F3F4F6",
                                drawBorder: false,
                            },
                            ticks: {
                                callback: (value) => value + "%",
                                maxTicksLimit: 6,
                                color: "#6B7280",
                                font: {
                                    size: 11,
                                },
                            },
                            border: { display: false },
                        },
                    },
                },
            });
        },

        // Radar
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
                    borderColor: this.brandColors[i],
                    backgroundColor: this.brandColors[i] + "1A",
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: "#FFFFFF",
                    pointBorderColor: this.brandColors[i],
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: this.brandColors[i],
                    pointHoverBorderColor: "#FFFFFF",
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
                                pointStyle: "circle",
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: "500",
                                    family: "Inter, sans-serif",
                                },
                                color: "#374151",
                            },
                        },
                        tooltip: {
                            backgroundColor: "rgba(255, 255, 255, 0.95)",
                            titleColor: "#111827",
                            bodyColor: "#4B5563",
                            borderColor: "#E5E7EB",
                            borderWidth: 1,
                            padding: 12,
                            bodySpacing: 6,
                            titleFont: {
                                size: 13,
                                weight: "600",
                            },
                            bodyFont: {
                                size: 12,
                                weight: "400",
                            },
                            displayColors: true,
                            boxWidth: 10,
                            boxHeight: 10,
                            boxPadding: 6,
                            usePointStyle: true,
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
                                stepSize: 25,
                                backdropColor: "transparent",
                                color: "#9CA3AF",
                                font: {
                                    size: 11,
                                    weight: "400",
                                },
                                callback: (value) => value,
                            },
                            grid: {
                                color: "#E5E7EB",
                                circular: true,
                                lineWidth: 1,
                            },
                            angleLines: {
                                color: "#E5E7EB",
                                lineWidth: 1,
                            },
                            pointLabels: {
                                font: {
                                    size: 13,
                                    weight: "600",
                                    family: "Inter, sans-serif",
                                },
                                color: "#374151",
                                padding: 8,
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

        // Horizontal Bar
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
                            backgroundColor: "#f59e0b",
                            borderRadius: 4,
                            borderSkipped: false,
                            barThickness: 24,
                        },
                    ],
                },
                options: {
                    indexAxis: "y",
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) =>
                                    ` ${formatCurrencyUtil(ctx.parsed.x)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], drawBorder: false },
                            border: { display: false },
                            ticks: {
                                maxTicksLimit: 5,
                                callback: (v) =>
                                    "₱" + (v / 1000000).toFixed(1) + "M",
                            },
                        },
                        y: {
                            grid: { display: false },
                            border: { display: false },
                        },
                    },
                },
            });
        },

        // Comparison Bar
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
                            backgroundColor: "#0d6efd",
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: "flex",
                            maxBarThickness: 40,
                        },
                        {
                            label: "Spent",
                            data: this.barangays.map(
                                (b) => parseFloat(b.total_spent) || 0,
                            ),
                            backgroundColor: "#128a43",
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: "flex",
                            maxBarThickness: 40,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                usePointStyle: true,
                                pointStyle: "circle",
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: "500",
                                },
                                color: "#374151",
                            },
                        },
                        tooltip: {
                            backgroundColor: "rgba(255, 255, 255, 0.95)",
                            titleColor: "#111827",
                            bodyColor: "#4B5563",
                            borderColor: "#E5E7EB",
                            borderWidth: 1,
                            padding: 12,
                            bodySpacing: 6,
                            titleFont: {
                                size: 13,
                                weight: "600",
                            },
                            bodyFont: {
                                size: 12,
                            },
                            displayColors: true,
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
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
                                color: "#6B7280",
                                font: {
                                    size: 12,
                                    weight: "500",
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "#F3F4F6",
                                drawBorder: false,
                            },
                            border: { display: false },
                            ticks: {
                                maxTicksLimit: 6,
                                color: "#6B7280",
                                font: {
                                    size: 11,
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
