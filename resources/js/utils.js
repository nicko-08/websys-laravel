// Format currency
export function formatCurrency(amount) {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
    }).format(amount);
}

// Format number with commas
export function formatNumber(number) {
    return new Intl.NumberFormat("en-PH").format(number);
}

// Format percentage
export function formatPercent(value) {
    return `${parseFloat(value).toFixed(2)}%`;
}

// Format date
export function formatDate(dateString) {
    return new Intl.DateTimeFormat("en-PH", {
        year: "numeric",
        month: "short",
        day: "numeric",
    }).format(new Date(dateString));
}

// Show toast notification
export function showToast(message, type = "info") {
    const toast = document.createElement("div");

    const colors = {
        success: "bg-[#128a43]",
        error: "bg-red-600",
        warning: "bg-amber-500",
        info: "bg-[#0d6efd]",
    };

    toast.className = `fixed top-24 right-6 ${colors[type]} text-white text-sm font-medium px-6 py-3 rounded-sm shadow-md z-50 transition-opacity duration-300`;

    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
