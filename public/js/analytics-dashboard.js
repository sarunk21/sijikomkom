// Analytics Dashboard JavaScript untuk Python FastAPI
class AnalyticsDashboard {
    constructor() {
        this.charts = {};
        console.log('AnalyticsDashboard initialized');
        this.init();
    }

    init() {
        console.log('Initializing analytics dashboard...');
        console.log('ANALYTICS_CONFIG:', ANALYTICS_CONFIG);

        this.loadSkemaTrend();
        this.loadStatistikKeberhasilan();
        this.loadSegmentasiDemografi();
        this.loadWorkloadAsesor();
        this.loadTrenPeminatSkema();

        // Auto refresh jika diaktifkan
        if (ANALYTICS_CONFIG.AUTO_REFRESH_INTERVAL > 0) {
            setInterval(() => {
                this.refreshAllCharts();
            }, ANALYTICS_CONFIG.AUTO_REFRESH_INTERVAL);
        }
    }

    // Load tren pendaftaran skema
    async loadSkemaTrend() {
        try {
            console.log('Loading skema trend...');
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 6);
            const endDate = new Date();

            const url = getApiUrl(ANALYTICS_CONFIG.ENDPOINTS.SKEMA_TREND, {
                start_date: startDate.toISOString().split('T')[0],
                end_date: endDate.toISOString().split('T')[0]
            });

            console.log('Skema trend URL:', url);
            const data = await fetchWithTimeout(url);
            console.log('Skema trend data:', data);
            this.renderSkemaTrendChart(data);
        } catch (error) {
            console.error('Error loading skema trend:', error);
            this.showError('Gagal memuat data tren skema');
        }
    }

    // Load statistik keberhasilan
    async loadStatistikKeberhasilan() {
        try {
            console.log('Loading statistik keberhasilan...');
            const url = getApiUrl(ANALYTICS_CONFIG.ENDPOINTS.STATISTIK_KEBERHASILAN);
            console.log('Statistik keberhasilan URL:', url);
            const data = await fetchWithTimeout(url);
            console.log('Statistik keberhasilan data:', data);
            this.renderStatistikKeberhasilanChart(data);
        } catch (error) {
            console.error('Error loading statistik keberhasilan:', error);
            this.showError('Gagal memuat data statistik keberhasilan');
        }
    }

    // Load segmentasi demografi
    async loadSegmentasiDemografi() {
        try {
            console.log('Loading segmentasi demografi...');
            const url = getApiUrl(ANALYTICS_CONFIG.ENDPOINTS.SEGMENTASI_DEMOGRAFI);
            console.log('Segmentasi demografi URL:', url);
            const data = await fetchWithTimeout(url);
            console.log('Segmentasi demografi data:', data);
            this.renderSegmentasiDemografiChart(data);
        } catch (error) {
            console.error('Error loading segmentasi demografi:', error);
            this.showError('Gagal memuat data segmentasi demografi');
        }
    }

    // Load workload asesor
    async loadWorkloadAsesor() {
        try {
            console.log('Loading workload asesor...');
            const url = getApiUrl(ANALYTICS_CONFIG.ENDPOINTS.WORKLOAD_ASESOR);
            console.log('Workload asesor URL:', url);
            const data = await fetchWithTimeout(url);
            console.log('Workload asesor data:', data);
            this.renderWorkloadAsesorChart(data);
        } catch (error) {
            console.error('Error loading workload asesor:', error);
            this.showError('Gagal memuat data workload asesor');
        }
    }

    // Load tren peminat skema
    async loadTrenPeminatSkema() {
        try {
            console.log('Loading tren peminat skema...');
            const url = getApiUrl(ANALYTICS_CONFIG.ENDPOINTS.TREN_PEMINAT_SKEMA, {
                interval: 'monthly'
            });
            console.log('Tren peminat skema URL:', url);
            const data = await fetchWithTimeout(url);
            console.log('Tren peminat skema data:', data);
            this.renderTrenPeminatSkemaChart(data);
        } catch (error) {
            console.error('Error loading tren peminat skema:', error);
            this.showError('Gagal memuat data tren peminat skema');
        }
    }

    // Refresh semua chart
    async refreshAllCharts() {
        console.log('Refreshing all charts...');
        await Promise.all([
            this.loadSkemaTrend(),
            this.loadStatistikKeberhasilan(),
            this.loadSegmentasiDemografi(),
            this.loadWorkloadAsesor(),
            this.loadTrenPeminatSkema()
        ]);
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

    // Show error message
    showError(message) {
        console.error(message);
    }
}

// Initialize analytics dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing analytics dashboard...');
    new AnalyticsDashboard();
});
