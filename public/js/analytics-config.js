// Konfigurasi Analytics Dashboard untuk Python FastAPI
const ANALYTICS_CONFIG = {
    // URL Python FastAPI - Sesuaikan dengan URL server Python Anda
    PYTHON_API_BASE: 'http://127.0.0.1:3000',

    // Timeout untuk request API (dalam milidetik)
    REQUEST_TIMEOUT: 10000,

    // Interval refresh data (dalam milidetik) - 0 untuk tidak auto-refresh
    AUTO_REFRESH_INTERVAL: 0,

    // Konfigurasi chart
    CHART_CONFIG: {
        // Warna untuk chart
        colors: [
            'rgba(78, 115, 223, 0.8)',   // Primary Blue
            'rgba(28, 200, 138, 0.8)',   // Success Green
            'rgba(246, 194, 62, 0.8)',   // Warning Yellow
            'rgba(231, 74, 59, 0.8)',    // Danger Red
            'rgba(133, 135, 150, 0.8)',  // Secondary Gray
            'rgba(54, 185, 204, 0.8)',   // Info Cyan
            'rgba(156, 39, 176, 0.8)',   // Purple
            'rgba(255, 152, 0, 0.8)'     // Orange
        ],

        // Responsive options
        responsive: true,
        maintainAspectRatio: false,

        // Animation options
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
        }
    },

    // Endpoint mapping sesuai dokumentasi Python API
    ENDPOINTS: {
        SKEMA_TREND: '/analytics/skema-trend',
        STATISTIK_KEBERHASILAN: '/analytics/statistik-keberhasilan',
        SEGMENTASI_DEMOGRAFI: '/analytics/segmentasi-demografi',
        WORKLOAD_ASESOR: '/analytics/workload-asesor',
        TREN_PEMINAT_SKEMA: '/analytics/tren-peminat-skema'
    }
};

// Helper function untuk mendapatkan URL lengkap
function getApiUrl(endpoint, params = {}) {
    const url = new URL(ANALYTICS_CONFIG.PYTHON_API_BASE + endpoint);

    // Add query parameters
    Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
            url.searchParams.append(key, params[key]);
        }
    });

    return url.toString();
}

// Helper function untuk membuat request dengan timeout
async function fetchWithTimeout(url, options = {}) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), ANALYTICS_CONFIG.REQUEST_TIMEOUT);

    try {
        const response = await fetch(url, {
            ...options,
            signal: controller.signal
        });
        clearTimeout(timeoutId);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        clearTimeout(timeoutId);
        throw error;
    }
}
