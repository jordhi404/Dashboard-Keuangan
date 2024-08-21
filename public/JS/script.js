// Fungsi menghitung waktu tunggu pasien sejak assign rencana pulang.
console.log('window.patients:', window.patients);

function updateTime() {
    if (window.patients && Object.keys(window.patients).length > 0) {
        console.log('Updating time...');
        for (let group in window.patients) {
            if (window.patients.hasOwnProperty(group)) {
                Object.values(window.patients[group]).forEach(patient => {
                    if (patient !== null) {
                        const waitTimeElementId = 'wait-time-' + patient.MedicalNo;
                        const progressBarElementId = 'progress-bar-' + patient.MedicalNo;

                        var waitTimeElement = document.getElementById(waitTimeElementId);
                        var progressBar = document.getElementById(progressBarElementId);

                        console.log(`Patient ID: ${patient.MedicalNo}`);
                        console.log(`Wait Time Element ID: ${waitTimeElementId}`);
                        console.log(`Progress Bar Element ID: ${progressBarElementId}`);

                        if (waitTimeElement && progressBar) {
                            var dischargeTime = new Date(patient.RencanaPulang).getTime();
                            var currentTime = new Date().getTime();
                            var waitTimeInSeconds = Math.floor((currentTime - dischargeTime) / 1000);

                            var hours = Math.floor(waitTimeInSeconds / 3600);
                            var minutes = Math.floor((waitTimeInSeconds % 3600) / 60);
                            var seconds = waitTimeInSeconds % 60;
                            var waitTimeFormatted = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
                            waitTimeElement.innerHTML = waitTimeFormatted;

                            var standardWaitTimeInSeconds = 3600; // 1 hour for testing
                            var progressPercentage = Math.min((waitTimeInSeconds / standardWaitTimeInSeconds) * 100, 100);
                            progressBar.style.width = progressPercentage + '%';

                            if (waitTimeInSeconds > standardWaitTimeInSeconds) {
                                progressBar.classList.add('progress-bar-red');
                            } else {
                                progressBar.classList.add('progress-bar-blue');
                            }
                        } else {
                            console.warn(`Element not found for ${patient.MedicalNo}`);
                        }
                    } else {
                        console.warn('Patient data is null.');
                    }
                });
            }
        }
    } else {
        console.warn('window.patients is empty or not defined.');
    }
}

setInterval(updateTime, 1000);


// Fungsi popover untuk note pasien.
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi semua popover
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});