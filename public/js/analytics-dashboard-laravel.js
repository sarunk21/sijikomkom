// Analytics Dashboard JavaScript untuk Laravel Controller
class AnalyticsDashboardLaravel {
    constructor() {
        this.charts = {};
        this.currentUserType = this.getCurrentUserType();
        this.currentFilters = {
            startDate: null,
            endDate: null,
            skemaId: null
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
        if (this.currentFilters.skemaId) {
            params.append('skema_id', this.currentFilters.skemaId);
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
        this.loadSkemaList(); // Load skema dropdown
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
        const skemaFilterInput = document.getElementById('skemaFilter');

        if (startDateInput && endDateInput) {
            this.currentFilters.startDate = startDateInput.value || null;
            this.currentFilters.endDate = endDateInput.value || null;
            this.currentFilters.skemaId = skemaFilterInput ? (skemaFilterInput.value || null) : null;

            // Validasi tanggal
            if (this.currentFilters.startDate && this.currentFilters.endDate) {
                if (new Date(this.currentFilters.startDate) > new Date(this.currentFilters.endDate)) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
                    return;
                }
            }

            console.log('Applying filters:', this.currentFilters);
            this.loadAllAnalyticsData();
        }
    }

    // Clear date filter
    clearDateFilter() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const skemaFilterInput = document.getElementById('skemaFilter');

        if (startDateInput && endDateInput) {
            startDateInput.value = '';
            endDateInput.value = '';
        }
        if (skemaFilterInput) {
            skemaFilterInput.value = '';
        }

        this.currentFilters.startDate = null;
        this.currentFilters.endDate = null;
        this.currentFilters.skemaId = null;

        console.log('Clearing all filters');
        this.loadAllAnalyticsData();
    }

    // Load skema list untuk populate dropdown
    async loadSkemaList() {
        try {
            const baseUrl = window.location.origin;
            const url = `${baseUrl}/api/skema`; // Endpoint API untuk get skema list

            const response = await fetch(url);
            if (!response.ok) {
                console.warn('Failed to load skema list');
                return;
            }

            const skemas = await response.json();
            const skemaFilterSelect = document.getElementById('skemaFilter');

            if (skemaFilterSelect && Array.isArray(skemas)) {
                // Clear existing options except the first "Semua Skema"
                skemaFilterSelect.innerHTML = '<option value="">Semua Skema</option>';

                // Populate dengan skema dari API
                skemas.forEach(skema => {
                    const option = document.createElement('option');
                    option.value = skema.id;
                    option.textContent = `${skema.kode} - ${skema.nama}`;
                    skemaFilterSelect.appendChild(option);
                });

                console.log('Skema list loaded successfully:', skemas.length, 'items');
            }
        } catch (error) {
            console.error('Error loading skema list:', error);
        }
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
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}, body: ${errorText}`);
            }

            // Check if response is HTML (redirect to login page)
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);

            if (!contentType || !contentType.includes('application/json')) {
                const htmlText = await response.text();
                console.error('Received HTML instead of JSON (first 500 chars):', htmlText.substring(0, 500));

                // Check if it's a login redirect
                if (htmlText.includes('login') || htmlText.includes('Login') || htmlText.includes('<!DOCTYPE')) {
                    console.error('AUTHENTICATION ERROR: User not logged in or session expired!');
                    console.error('Please login as pimpinan/kaprodi first, then refresh this page.');
                    throw new Error('AUTHENTICATION REQUIRED: Please login as pimpinan/kaprodi first.');
                }
                throw new Error('Server returned HTML instead of JSON. Response starts with: ' + htmlText.substring(0, 100));
            }

            const result = await response.json();
            console.log('Analytics response:', result);

            if (result.success) {
                console.log('Successfully received data, rendering charts...');
                this.renderAllCharts(result.data);
                this.updateSummaryCards(result.data);
            } else {
                console.error('Analytics API error:', result.message);
                this.showError('Gagal memuat data analytics: ' + result.message);
            }

        } catch (error) {
            console.error('Error loading analytics data:', error);
            console.error('Error stack:', error.stack);
            this.showError('Gagal memuat data analytics: ' + error.message);
        }
    }

    // Render semua chart
    renderAllCharts(data) {
        console.log('Rendering all charts with data:', data);

        // Render each chart with fallback to empty data
        this.renderSkemaTrendChart(data.skema_trend || []);
        this.renderKompetensiSkemaChart(data.kompetensi_skema || {});
        this.renderSegmentasiDemografiChart(data.segmentasi_demografi || {jenis_kelamin: {}});
        this.renderWorkloadAsesorChart(data.workload_asesor || []);
        this.renderTrenPeminatSkemaChart(data.tren_peminat_skema || []);
    }

    // Update summary cards
    updateSummaryCards(data) {
        console.log('Updating summary cards with data:', data);

        // Update total pendaftaran dari dashboard_summary
        const totalPendaftaran = data.dashboard_summary?.total_pendaftaran || 0;
        const totalPendaftaranEl = document.getElementById('totalPendaftaran');
        if (totalPendaftaranEl) {
            totalPendaftaranEl.textContent = totalPendaftaran.toLocaleString();
            console.log('Updated totalPendaftaran:', totalPendaftaran);
        }

        // Update total skema dari dashboard_summary
        const totalSkema = data.dashboard_summary?.total_skema || 0;
        const totalSkemaEl = document.getElementById('totalSkema');
        if (totalSkemaEl) {
            totalSkemaEl.textContent = totalSkema;
            console.log('Updated totalSkema:', totalSkema);
        }

        // Update tingkat keberhasilan - prioritize backend calculation
        let tingkatKeberhasilan = data.dashboard_summary?.tingkat_keberhasilan || 0;

        // Fallback to frontend calculation if not provided by backend
        if (!tingkatKeberhasilan && data.kompetensi_skema && Object.keys(data.kompetensi_skema).length > 0) {
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

            tingkatKeberhasilan = totalPendaftaranKompetensi > 0 ? (totalLulus / totalPendaftaranKompetensi) * 100 : 0;
        }

        const tingkatKeberhasilanEl = document.getElementById('tingkatKeberhasilan');
        const progressKeberhasilanEl = document.getElementById('progressKeberhasilan');

        if (tingkatKeberhasilanEl) {
            tingkatKeberhasilanEl.textContent = tingkatKeberhasilan.toFixed(1) + '%';
            console.log('Updated tingkatKeberhasilan:', tingkatKeberhasilan);
        }
        if (progressKeberhasilanEl) {
            progressKeberhasilanEl.style.width = tingkatKeberhasilan + '%';
            progressKeberhasilanEl.setAttribute('aria-valuenow', tingkatKeberhasilan);
        }

        // Update total asesor dari dashboard_summary
        const totalAsesor = data.dashboard_summary?.total_asesor || 0;
        const totalAsesorEl = document.getElementById('totalAsesor');
        if (totalAsesorEl) {
            totalAsesorEl.textContent = totalAsesor;
            console.log('Updated totalAsesor:', totalAsesor);
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
        const kompetenCounts = [];
        const tidakKompetenCounts = [];

        if (data && Object.keys(data).length > 0) {
            Object.entries(data).forEach(([skemaId, statusData]) => {
                // Skip internal _skema_info key
                if (skemaId.startsWith('_')) return;

                let totalKompeten = 0;
                let totalTidakKompeten = 0;
                let skemaLabel = `Skema ${skemaId}`;

                // Get skema name if available
                if (statusData._skema_info) {
                    skemaLabel = `${statusData._skema_info.kode} - ${statusData._skema_info.nama}`;
                }

                Object.entries(statusData).forEach(([status, jumlah]) => {
                    // Skip internal keys
                    if (status.startsWith('_')) return;

                    // Status 5 = Kompeten, Status 4 = Tidak Kompeten
                    if (status == '5') {
                        totalKompeten += jumlah;
                    } else if (status == '4') {
                        totalTidakKompeten += jumlah;
                    }
                });

                if (totalKompeten > 0 || totalTidakKompeten > 0) {
                    labels.push(skemaLabel);
                    kompetenCounts.push(totalKompeten);
                    tidakKompetenCounts.push(totalTidakKompeten);
                }
            });
        }

        // Jika tidak ada data, tampilkan chart kosong
        if (labels.length === 0) {
            labels.push('Tidak ada data');
            kompetenCounts.push(0);
            tidakKompetenCounts.push(0);
        }

        console.log('Statistik keberhasilan labels:', labels);
        console.log('Statistik keberhasilan kompeten:', kompetenCounts);
        console.log('Statistik keberhasilan tidak kompeten:', tidakKompetenCounts);

        if (this.charts.statistikKeberhasilan) {
            this.charts.statistikKeberhasilan.destroy();
        }

        this.charts.statistikKeberhasilan = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Kompeten', 'Tidak Kompeten'],
                datasets: [{
                    data: [
                        kompetenCounts.reduce((a, b) => a + b, 0),
                        tidakKompetenCounts.reduce((a, b) => a + b, 0)
                    ],
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.8)',  // Green for Kompeten
                        'rgba(231, 74, 59, 0.8)'    // Red for Tidak Kompeten
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
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

        // Jika tidak ada data, tampilkan pesan
        if (genderLabels.length === 0 || genderValues.reduce((a, b) => a + b, 0) === 0) {
            genderLabels.push('Tidak ada data');
            genderValues.push(1);
        }

        if (this.charts.segmentasiDemografi) {
            this.charts.segmentasiDemografi.destroy();
        }

        this.charts.segmentasiDemografi = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: genderLabels, // Label sudah di-mapping dari backend
                datasets: [{
                    data: genderValues,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',  // Biru untuk Laki-laki
                        'rgba(231, 74, 59, 0.8)',   // Merah untuk Perempuan
                        'rgba(133, 135, 150, 0.8)'  // Abu-abu untuk Lainnya
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
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
            type: 'bar',
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
                indexAxis: 'y', // This makes it horizontal
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
        console.error('=== DASHBOARD ERROR ===');
        console.error(message);

        // Show error in all summary card elements
        const elements = ['totalPendaftaran', 'totalSkema', 'tingkatKeberhasilan', 'totalAsesor'];
        elements.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = 'Error';
                el.style.color = '#e74a3b';
            }
        });

        // Show alert if it's authentication error
        if (message.includes('Session expired') || message.includes('not authenticated') || message.includes('AUTHENTICATION REQUIRED')) {
            const userType = window.location.pathname.includes('/pimpinan/') ? 'pimpinan' : 'kaprodi';

            console.error('========================================');
            console.error('SOLUSI:');
            console.error('1. Buka tab baru dan login sebagai ' + userType);
            console.error('2. Setelah login, kembali ke tab ini');
            console.error('3. Refresh halaman (F5 atau Ctrl+R)');
            console.error('========================================');

            alert('⚠️ AUTENTIKASI DIPERLUKAN\n\n' +
                  'Anda belum login atau session expired.\n\n' +
                  'Silakan:\n' +
                  '1. Login sebagai ' + userType + '\n' +
                  '2. Refresh halaman ini (F5)\n\n' +
                  'Dashboard akan otomatis load data setelah login.');
        }
    }
}

// Initialize analytics dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Laravel analytics dashboard...');
    new AnalyticsDashboardLaravel();
});
