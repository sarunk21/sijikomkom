// Analytics Dashboard JavaScript untuk Laravel Controller
class AnalyticsDashboardLaravel {
    constructor() {
        this.charts = {};
        this.currentUserType = this.getCurrentUserType();
        console.log('AnalyticsDashboardLaravel initialized for user type:', this.currentUserType);
        this.init();
    }

    getCurrentUserType() {
        // Ambil user type dari URL atau data attribute
        const path = window.location.pathname;
        if (path.includes('/admin/')) return 'admin';
        if (path.includes('/kaprodi/')) return 'kaprodi';
        if (path.includes('/pimpinan/')) return 'pimpinan';
        return 'admin'; // default
    }

    getAnalyticsUrl() {
        const baseUrl = window.location.origin;
        return `${baseUrl}/${this.currentUserType}/analytics/dashboard-data`;
    }

    getClearCacheUrl() {
        const baseUrl = window.location.origin;
        return `${baseUrl}/${this.currentUserType}/analytics/clear-cache`;
    }

    init() {
        console.log('Initializing Laravel analytics dashboard...');
        this.loadAllAnalyticsData();

        // Auto refresh setiap 5 menit
        setInterval(() => {
            this.loadAllAnalyticsData();
        }, 300000); // 5 menit
    }

    // Load semua data analytics sekaligus
    async loadAllAnalyticsData() {
        try {
            console.log('Loading all analytics data from Laravel controller...');
            const url = this.getAnalyticsUrl();
            console.log('Analytics URL:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Analytics response:', result);

            if (result.success) {
                this.renderAllCharts(result.data);
                this.updateSummaryCards(result.data);
            } else {
                console.error('Analytics API error:', result.message);
                this.showError('Gagal memuat data analytics: ' + result.message);
            }

        } catch (error) {
            console.error('Error loading analytics data:', error);
            this.showError('Gagal memuat data analytics');
        }
    }

    // Render semua chart
    renderAllCharts(data) {
        this.renderSkemaTrendChart(data.skema_trend);
        this.renderStatistikKeberhasilanChart(data.statistik_keberhasilan);
        this.renderSegmentasiDemografiChart(data.segmentasi_demografi);
        this.renderWorkloadAsesorChart(data.workload_asesor);
        this.renderTrenPeminatSkemaChart(data.tren_peminat_skema);
    }

    // Update summary cards
    updateSummaryCards(data) {
        // Update total pendaftaran
        const totalPendaftaran = data.skema_trend.reduce((sum, item) => sum + item.total_registrations, 0);
        const totalPendaftaranEl = document.getElementById('totalPendaftaran');
        if (totalPendaftaranEl) {
            totalPendaftaranEl.textContent = totalPendaftaran.toLocaleString();
        }

        // Update total skema
        const totalSkema = data.skema_trend.length;
        const totalSkemaEl = document.getElementById('totalSkema');
        if (totalSkemaEl) {
            totalSkemaEl.textContent = totalSkema;
        }

        // Update tingkat keberhasilan
        if (data.statistik_keberhasilan.length > 0) {
            const avgPassRate = data.statistik_keberhasilan.reduce((sum, item) => sum + item.pass_rate, 0) / data.statistik_keberhasilan.length;
            const tingkatKeberhasilanEl = document.getElementById('tingkatKeberhasilan');
            const progressKeberhasilanEl = document.getElementById('progressKeberhasilan');

            if (tingkatKeberhasilanEl) {
                tingkatKeberhasilanEl.textContent = avgPassRate.toFixed(1) + '%';
            }
            if (progressKeberhasilanEl) {
                progressKeberhasilanEl.style.width = avgPassRate + '%';
                progressKeberhasilanEl.setAttribute('aria-valuenow', avgPassRate);
            }
        }

        // Update total asesor
        const totalAsesor = data.workload_asesor.length;
        const totalAsesorEl = document.getElementById('totalAsesor');
        if (totalAsesorEl) {
            totalAsesorEl.textContent = totalAsesor;
        }
    }

    // Render chart tren skema
    renderSkemaTrendChart(data) {
        console.log('Rendering skema trend chart...');
        const ctx = document.getElementById('skemaTrendChart');
        if (!ctx) {
            console.error('Canvas skemaTrendChart not found');
            return;
        }

        const labels = data.map(item => item.skema_name);
        const values = data.map(item => item.total_registrations);

        console.log('Skema trend labels:', labels);
        console.log('Skema trend values:', values);

        if (this.charts.skemaTrend) {
            this.charts.skemaTrend.destroy();
        }

        this.charts.skemaTrend = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pendaftaran',
                    data: values,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Render chart statistik keberhasilan
    renderStatistikKeberhasilanChart(data) {
        console.log('Rendering statistik keberhasilan chart...');
        const ctx = document.getElementById('statistikKeberhasilanChart');
        if (!ctx) {
            console.error('Canvas statistikKeberhasilanChart not found');
            return;
        }

        const labels = data.map(item => item.skema_name);
        const passRates = data.map(item => item.pass_rate);

        console.log('Statistik keberhasilan labels:', labels);
        console.log('Statistik keberhasilan pass rates:', passRates);

        if (this.charts.statistikKeberhasilan) {
            this.charts.statistikKeberhasilan.destroy();
        }

        this.charts.statistikKeberhasilan = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: passRates,
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(133, 135, 150, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Render chart segmentasi demografi
    renderSegmentasiDemografiChart(data) {
        console.log('Rendering segmentasi demografi chart...');
        const ctx = document.getElementById('segmentasiDemografiChart');
        if (!ctx) {
            console.error('Canvas segmentasiDemografiChart not found');
            return;
        }

        const genderLabels = Object.keys(data.gender_distribution);
        const genderValues = Object.values(data.gender_distribution);

        console.log('Segmentasi demografi labels:', genderLabels);
        console.log('Segmentasi demografi values:', genderValues);

        if (this.charts.segmentasiDemografi) {
            this.charts.segmentasiDemografi.destroy();
        }

        this.charts.segmentasiDemografi = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: genderLabels.map(label => label === 'male' ? 'Laki-laki' : label === 'female' ? 'Perempuan' : 'Lainnya'),
                datasets: [{
                    data: genderValues,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(133, 135, 150, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Render chart workload asesor
    renderWorkloadAsesorChart(data) {
        console.log('Rendering workload asesor chart...');
        const ctx = document.getElementById('workloadAsesorChart');
        if (!ctx) {
            console.error('Canvas workloadAsesorChart not found');
            return;
        }

        const labels = data.map(item => item.asesor_name);
        const values = data.map(item => item.total_reports_handled);

        console.log('Workload asesor labels:', labels);
        console.log('Workload asesor values:', values);

        if (this.charts.workloadAsesor) {
            this.charts.workloadAsesor.destroy();
        }

        this.charts.workloadAsesor = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Laporan Ditangani',
                    data: values,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Render chart tren peminat skema
    renderTrenPeminatSkemaChart(data) {
        console.log('Rendering tren peminat skema chart...');
        const ctx = document.getElementById('trenPeminatSkemaChart');
        if (!ctx) {
            console.error('Canvas trenPeminatSkemaChart not found');
            return;
        }

        if (data.length === 0) {
            console.log('No data for tren peminat skema');
            return;
        }

        const firstSkema = data[0];
        const labels = firstSkema.trend.map(item => item.period);
        const datasets = data.map((skema, index) => ({
            label: skema.skema_name,
            data: skema.trend.map(item => item.registrations),
            borderColor: this.getColor(index),
            backgroundColor: this.getColor(index, 0.1),
            borderWidth: 2,
            fill: false
        }));

        console.log('Tren peminat skema labels:', labels);
        console.log('Tren peminat skema datasets:', datasets);

        if (this.charts.trenPeminatSkema) {
            this.charts.trenPeminatSkema.destroy();
        }

        this.charts.trenPeminatSkema = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Helper untuk mendapatkan warna
    getColor(index, alpha = 1) {
        const colors = [
            'rgba(78, 115, 223, ' + alpha + ')',
            'rgba(28, 200, 138, ' + alpha + ')',
            'rgba(246, 194, 62, ' + alpha + ')',
            'rgba(231, 74, 59, ' + alpha + ')',
            'rgba(133, 135, 150, ' + alpha + ')'
        ];
        return colors[index % colors.length];
    }

    // Clear cache
    async clearCache() {
        try {
            const url = this.getClearCacheUrl();
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const result = await response.json();
            if (result.success) {
                console.log('Cache cleared successfully');
                // Reload data after clearing cache
                this.loadAllAnalyticsData();
            } else {
                console.error('Failed to clear cache:', result.message);
            }
        } catch (error) {
            console.error('Error clearing cache:', error);
        }
    }

    // Show error message
    showError(message) {
        console.error(message);
        // Bisa ditambahkan toast notification di sini
    }
}

// Initialize analytics dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Laravel analytics dashboard...');
    new AnalyticsDashboardLaravel();
});
