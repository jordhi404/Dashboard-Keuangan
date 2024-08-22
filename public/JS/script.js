// Fungsi menghitung waktu tunggu pasien sejak assign rencana pulang.
function updateTime() {
    if (window.patients && Array.isArray(window.patients) && window.patients.length > 0) {
        window.patients.forEach(patient => {
            if (patient != null) {
                const waitTimeElementId = 'wait-time-' + patient.MedicalNo;
                const progressBarElementId = 'progress-bar-' + patient.MedicalNo;

                var waitTimeElement = document.getElementById(waitTimeElementId);
                var progressBar = document.getElementById(progressBarElementId);

                if (waitTimeElement && progressBar) {
                    // Cek jika status pasien bukan 'SelesaiKasir'
                    if (patient.status !== 'SelesaiKasir') {
                        var dischargeTime = new Date(patient.RencanaPulang).getTime();
                        var currentTime = new Date().getTime();
                        var waitTimeInSeconds = Math.floor((currentTime - dischargeTime) / 1000);

                        var hours = Math.floor(waitTimeInSeconds / 3600);
                        var minutes = Math.floor((waitTimeInSeconds % 3600) / 60);
                        var seconds = waitTimeInSeconds % 60;
                        var waitTimeFormatted = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);

                        waitTimeElement.innerHTML = waitTimeFormatted;

                        var standardWaitTimeInSeconds = (patient.keterangan === 'TungguFarmasi') ? 3600 : 900; // 1 jam untuk TungguFarmasi, 15 menit untuk lainnya
                        var progressPercentage = Math.min((waitTimeInSeconds / standardWaitTimeInSeconds) * 100, 100);
                        progressBar.style.width = progressPercentage + '%';

                        // Reset class progress bar
                        progressBar.classList.remove('progress-bar-red', 'progress-bar-blue');
                        if (waitTimeInSeconds > standardWaitTimeInSeconds) {
                            progressBar.classList.add('progress-bar-red');
                        } else {
                            progressBar.classList.add('progress-bar-blue');
                        }
                    }
                }
            }
        });
    } else {
        console.warn('window.patients is empty or not defined.');
    }
}

document.addEventListener('DOMContentLoaded', (event) => {
    setInterval(updateTime, 1000);
});


// Fungsi popover untuk note pasien.
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi semua popover
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});