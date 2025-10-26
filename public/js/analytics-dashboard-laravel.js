// Analytics Dashboard JavaScript untuk Laravel Controller
class AnalyticsDashboardLaravel {
    constructor() {
        this.charts = {};
        this.currentUserType = this.getCurrentUserType();
        this.currentFilters = {
            startDate: null,
            endDate: null
        };
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
        let url = `${baseUrl}/${this.currentUserType}/analytics/dashboard-data`;

        // Tambahkan query parameters jika ada filter
        const params = new URLSearchParams();
        if (this.currentFilters.startDate) {
            params.append('start_date', this.currentFilters.startDate);
        }
        if (this.currentFilters.endDate) {
            params.append('end_date', this.currentFilters.endDate);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        return url;
    }

    getClearCacheUrl() {
        const baseUrl = window.location.origin;
        return `${baseUrl}/${this.currentUserType}/analytics/clear-cache`;
    }

    init() {
        console.log('Initializing Laravel analytics dashboard...');
        this.setupEventListeners();
        this.loadAllAnalyticsData();

        // Auto refresh setiap 5 menit
        setInterval(() => {
            this.loadAllAnalyticsData();
        }, 300000); // 5 menit
    }

    // Setup event listeners untuk filter
    setupEventListeners() {
        // Apply filter button
        const applyFilterBtn = document.getElementById('applyFilter');
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', () => {
                this.applyDateFilter();
            });
        }

        // Clear filter button
        const clearFilterBtn = document.getElementById('clearFilter');
        if (clearFilterBtn) {
            clearFilterBtn.addEventListener('click', () => {
                this.clearDateFilter();
            });
        }

        // Refresh data button
        const refreshDataBtn = document.getElementById('refreshData');
        if (refreshDataBtn) {
            refreshDataBtn.addEventListener('click', () => {
                this.loadAllAnalyticsData();
            });
        }

        // Set default date range (last 30 days)
        this.setDefaultDateRange();
    }

    // Set default date range
    setDefaultDateRange() {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 30);

        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        if (startDateInput && endDateInput) {
            startDateInput.value = startDate.toISOString().split('T')[0];
            endDateInput.value = endDate.toISOString().split('T')[0];
        }
    }

    // Apply date filter
    applyDateFilter() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        if (startDateInput && endDateInput) {
            this.currentFilters.startDate = startDateInput.value || null;
            this.currentFilters.endDate = endDateInput.value || null;

            // Validasi tanggal
            if (this.currentFilters.startDate && this.currentFilters.endDate) {
                if (new Date(this.currentFilters.startDate) > new Date(this.currentFilters.endDate)) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
                    return;
                }
            }

            console.log('Applying date filter:', this.currentFilters);
            this.loadAllAnalyticsData();
        }
    }

    // Clear date filter
    clearDateFilter() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        if (startDateInput && endDateInput) {
            startDateInput.value = '';
            endDateInput.value = '';
        }

        this.currentFilters.startDate = null;
        this.currentFilters.endDate = null;

        console.log('Clearing date filter');
        this.loadAllAnalyticsData();
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
        console.log('Rendering all charts with data:', data);
        this.renderSkemaTrendChart(data.skema_trend);
        this.renderKompetensiSkemaChart(data.kompetensi_skema);
        this.renderSegmentasiDemografiChart(data.segmentasi_demografi);
        this.renderWorkloadAsesorChart(data.workload_asesor);
        this.renderTrenPeminatSkemaChart(data.tren_peminat_skema);
    }

    // Update summary cards
    updateSummaryCards(data) {
        console.log('Updating summary cards with data:', data);

        // Update total pendaftaran dari dashboard_summary
        const totalPendaftaran = data.dashboard_summary?.total_pendaftaran || 0;
        const totalPendaftaranEl = document.getElementById('totalPendaftaran');
        if (totalPendaftaranEl) {
            totalPendaftaranEl.textContent = totalPendaftaran.toLocaleString();
        }

        // Update total skema dari dashboard_summary
        const totalSkema = data.dashboard_summary?.total_skema || 0;
        const totalSkemaEl = document.getElementById('totalSkema');
        if (totalSkemaEl) {
            totalSkemaEl.textContent = totalSkema;
        }

        // Update tingkat keberhasilan dari kompetensi_skema
        if (data.kompetensi_skema && Object.keys(data.kompetensi_skema).length > 0) {
            let totalPendaftaranKompetensi = 0;
            let totalLulus = 0;

            Object.values(data.kompetensi_skema).forEach(skema => {
                Object.entries(skema).forEach(([status, jumlah]) => {
                    totalPendaftaranKompetensi += jumlah;
                    if (status.toLowerCase().includes('lulus') || status.toLowerCase().includes('kompeten')) {
                        totalLulus += jumlah;
                    }
                });
            });

            const avgPassRate = totalPendaftaranKompetensi > 0 ? (totalLulus / totalPendaftaranKompetensi) * 100 : 0;
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

        // Update total asesor dari dashboard_summary
        const totalAsesor = data.dashboard_summary?.total_asesor || 0;
        const totalAsesorEl = document.getElementById('totalAsesor');
        if (totalAsesorEl) {
            totalAsesorEl.textContent = totalAsesor;
        }
    }

    // Render chart tren skema
    renderSkemaTrendChart(data) {
        console.log('Rendering skema trend chart with data:', data);
        const ctx = document.getElementById('skemaTrendChart');
        if (!ctx) {
            console.error('Canvas skemaTrendChart not found');
            return;
        }

        let labels, values;

        if (!data || data.length === 0) {
            labels = ['Tidak ada data'];
            values = [0];
        } else {
            labels = data.map(item => item.month);
            values = data.map(item => item.total_pendaftaran);
        }

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

    // Render chart kompetensi skema
    renderKompetensiSkemaChart(data) {
        console.log('Rendering kompetensi skema chart with data:', data);
        const ctx = document.getElementById('statistikKeberhasilanChart');
        if (!ctx) {
            console.error('Canvas statistikKeberhasilanChart not found');
            return;
        }

        // Konversi data kompetensi skema ke format yang bisa ditampilkan
        const labels = [];
        const passRates = [];

        if (data && Object.keys(data).length > 0) {
            Object.entries(data).forEach(([skemaId, statusData]) => {
                let totalPendaftaran = 0;
                let totalLulus = 0;

                Object.entries(statusData).forEach(([status, jumlah]) => {
                    totalPendaftaran += jumlah;
                    if (status.toLowerCase().includes('lulus') || status.toLowerCase().includes('kompeten')) {
                        totalLulus += jumlah;
                    }
                });

                if (totalPendaftaran > 0) {
                    labels.push(`Skema ${skemaId}`);
                    passRates.push((totalLulus / totalPendaftaran) * 100);
                }
            });
        }

        // Jika tidak ada data, tampilkan chart kosong
        if (labels.length === 0) {
            labels.push('Tidak ada data');
            passRates.push(0);
        }

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
        console.log('Rendering segmentasi demografi chart with data:', data);
        const ctx = document.getElementById('segmentasiDemografiChart');
        if (!ctx) {
            console.error('Canvas segmentasiDemografiChart not found');
            return;
        }

        const genderLabels = Object.keys(data.jenis_kelamin || {});
        const genderValues = Object.values(data.jenis_kelamin || {});

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
        console.log('Rendering workload asesor chart with data:', data);
        const ctx = document.getElementById('workloadAsesorChart');
        if (!ctx) {
            console.error('Canvas workloadAsesorChart not found');
            return;
        }

        const labels = data.map(item => item.asesor_name || 'Asesor Tidak Diketahui');
        const values = data.map(item => item.jumlah_laporan || 0);

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
        console.log('Rendering tren peminat skema chart with data:', data);
        const ctx = document.getElementById('trenPeminatSkemaChart');
        if (!ctx) {
            console.error('Canvas trenPeminatSkemaChart not found');
            return;
        }

        if (!data || data.length === 0) {
            console.log('No data for tren peminat skema');
            // Tampilkan chart kosong dengan pesan
            if (this.charts.trenPeminatSkema) {
                this.charts.trenPeminatSkema.destroy();
            }
            this.charts.trenPeminatSkema = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Tidak ada data'],
                    datasets: [{
                        label: 'Tidak ada data',
                        data: [0],
                        borderColor: 'rgba(133, 135, 150, 0.8)',
                        backgroundColor: 'rgba(133, 135, 150, 0.1)',
                        borderWidth: 2,
                        fill: false
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
            return;
        }

        // Cari skema dengan data terbanyak untuk mendapatkan labels
        let maxTrendLength = 0;
        let labels = [];

        data.forEach(skema => {
            if (skema.trend && skema.trend.length > maxTrendLength) {
                maxTrendLength = skema.trend.length;
                labels = skema.trend.map(item => item.period);
            }
        });

        const datasets = data.map((skema, index) => ({
            label: skema.skema_name || `Skema ${skema.skema_id}`,
            data: skema.trend ? skema.trend.map(item => item.registrations) : [],
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
