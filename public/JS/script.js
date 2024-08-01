function updateTime() {
    for (let group in window.patients) {
        if (window.patients.hasOwnProperty(group)) {
            window.patients[group].forEach(patient => {
                var dischargeTime = new Date(patient.RencanaPulang).getTime();
                var currentTime = new Date().getTime();
                var waitTimeInSeconds = Math.floor((currentTime - dischargeTime) / 1000);

                if (waitTimeInSeconds >= 0) { // Check if the wait time has started
                    var hours = Math.floor(waitTimeInSeconds / 3600);
                    var minutes = Math.floor((waitTimeInSeconds % 3600) / 60);
                    var seconds = waitTimeInSeconds % 60;

                    var waitTimeFormatted = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
                    document.getElementById('wait-time-' + patient.MedicalNo).innerHTML = waitTimeFormatted;

                    // Set standard wait time based on patient category
                    var standardWaitTimeInSeconds = 7200; // Default 2 hours
                    if (patient.Keterangan == 'Tunggu Obat Farmasi') {
                        standardWaitTimeInSeconds = 3600; // 1 hour
                    } else if (patient.Keterangan == 'Penyelesaian Administrasi Pasien (Billing)') {
                        standardWaitTimeInSeconds = 900; // 15 minutes
                    } else if (patient.Keterangan == 'Tunggu Dokter' || patient.Keterangan == 'Observasi Pasien' || patient.Keterangan == 'Lain - Lain') {
                        standardWaitTimeInSeconds = 900; // 15 minutes
                    } else if (patient.Keterangan == 'Tunggu Hasil Pemeriksaan Penunjang') {
                        standardWaitTimeInSeconds = 900; // 15 minutes
                    }

                    var progressPercentage = Math.min((waitTimeInSeconds / standardWaitTimeInSeconds) * 100, 100);

                    var progressBar = document.getElementById('progress-bar-' + patient.MedicalNo);
                    if (progressBar) {
                        progressBar.style.width = progressPercentage + '%';
                        progressBar.className = 'progress-bar';

                        // Determine the color based on the wait time
                        if (waitTimeInSeconds > standardWaitTimeInSeconds) {
                            progressBar.classList.add('progress-bar-red');
                        } else {
                            progressBar.classList.add('progress-bar-blue');
                        }
                    }
                }
            });
        }
    }
}

setInterval(updateTime, 1000);

document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi semua popover
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

setInterval(function() {
    window.location.href = window.location.href.split('?')[0] + "?t=" + new Date().getTime();
}, 300000);