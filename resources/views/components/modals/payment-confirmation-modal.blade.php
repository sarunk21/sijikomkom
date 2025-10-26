<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="paymentConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="paymentConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Pendaftaran Kedua
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informasi Pendaftaran Kedua</h6>
                            <p class="mb-0">Anda sudah pernah mendaftar sebelumnya. Untuk pendaftaran kedua, silakan perhatikan hal-hal berikut:</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="text-primary"><i class="fas fa-history"></i> Riwayat Pendaftaran</h6>
                                <div id="registrationHistory">
                                    <!-- Data akan diisi via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="text-success"><i class="fas fa-credit-card"></i> Informasi Pembayaran</h6>
                                <div id="paymentInfo">
                                    <!-- Data akan diisi via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Ketentuan Pendaftaran Kedua</h6>
                                <ul class="mb-0">
                                    <li>Biaya pendaftaran kedua akan dikenakan sesuai ketentuan yang berlaku</li>
                                    <li>Pembayaran harus dilakukan sebelum batas waktu yang ditentukan</li>
                                    <li>Bukti pembayaran harus diupload untuk verifikasi</li>
                                    <li>Status pembayaran akan dikonfirmasi oleh admin dalam 1-3 hari kerja</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="confirmationCheckbox">
                                <input type="checkbox" id="confirmationCheckbox" class="mr-2">
                                Saya telah membaca dan menyetujui ketentuan pendaftaran kedua
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-warning" id="confirmSecondRegistration" disabled>
                    <i class="fas fa-check"></i> Lanjutkan Pendaftaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1" role="dialog" aria-labelledby="paymentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" id="paymentStatusHeader">
                <h5 class="modal-title" id="paymentStatusModalLabel">
                    <i class="fas fa-credit-card"></i> Status Pembayaran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="paymentStatusContent">
                    <!-- Content akan diisi via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle checkbox untuk konfirmasi
    $('#confirmationCheckbox').change(function() {
        $('#confirmSecondRegistration').prop('disabled', !this.checked);
    });

    // Handle konfirmasi pendaftaran kedua
    $('#confirmSecondRegistration').click(function() {
        // Redirect ke halaman pendaftaran dengan parameter khusus
        window.location.href = '{{ route("asesi.daftar-ujikom.index") }}?second_registration=true';
    });

    // Tampilkan modal jika ada session flash
    @if(session('show_payment_popup'))
        showPaymentConfirmationModal();
    @endif

    // Tampilkan modal status pembayaran jika ada
    @if(session('payment_status_message'))
        showPaymentStatusModal('{{ session("payment_status_message") }}', '{{ session("payment_status_type", "info") }}');
    @endif
});

function showPaymentConfirmationModal() {
    // Load data via AJAX
    $.ajax({
        url: '{{ route("asesi.registration-info") }}',
        method: 'GET',
        success: function(data) {
            // Update registration history
            let historyHtml = '<p class="mb-1"><strong>Status:</strong> ' + (data.has_previous_registration ? 'Sudah pernah mendaftar' : 'Belum pernah mendaftar') + '</p>';
            if (data.last_payment) {
                historyHtml += '<p class="mb-1"><strong>Pembayaran Terakhir:</strong> ' + data.last_payment.status_text + '</p>';
                historyHtml += '<p class="mb-0"><strong>Tanggal:</strong> ' + data.last_payment.created_at + '</p>';
            }
            $('#registrationHistory').html(historyHtml);

            // Update payment info
            let paymentHtml = '<p class="mb-1"><strong>Status:</strong> ' + (data.can_register_again ? 'Dapat mendaftar lagi' : 'Tidak dapat mendaftar') + '</p>';
            if (data.last_payment && data.last_payment.keterangan) {
                paymentHtml += '<p class="mb-0"><strong>Keterangan:</strong> ' + data.last_payment.keterangan + '</p>';
            }
            $('#paymentInfo').html(paymentHtml);

            // Show modal
            $('#paymentConfirmationModal').modal('show');
        },
        error: function() {
            // Fallback jika AJAX gagal
            $('#paymentConfirmationModal').modal('show');
        }
    });
}

function showPaymentStatusModal(message, type = 'info') {
    let headerClass = 'bg-info text-white';
    let icon = 'fas fa-info-circle';

    switch(type) {
        case 'success':
            headerClass = 'bg-success text-white';
            icon = 'fas fa-check-circle';
            break;
        case 'warning':
            headerClass = 'bg-warning text-white';
            icon = 'fas fa-exclamation-triangle';
            break;
        case 'danger':
            headerClass = 'bg-danger text-white';
            icon = 'fas fa-times-circle';
            break;
    }

    $('#paymentStatusHeader').removeClass().addClass('modal-header ' + headerClass);
    $('#paymentStatusModalLabel').html('<i class="' + icon + '"></i> Status Pembayaran');
    $('#paymentStatusContent').html('<div class="alert alert-' + type + '">' + message + '</div>');

    $('#paymentStatusModal').modal('show');
}
</script>
