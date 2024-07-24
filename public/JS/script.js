function updateTime() {
    for (let group in window.patients) {
        if (window.patients.hasOwnProperty(group)) {
            window.patients[group].forEach(patient => {
                var dischargeTime = new Date(patient.RencanaPulang).getTime();
                var currentTime = new Date().getTime();
                var waitTimeInSeconds = Math.floor((currentTime - dischargeTime) / 1000);

                if (waitTimeInSeconds > 0) {
                    var hours = Math.floor(waitTimeInSeconds / 3600);
                    var minutes = Math.floor((waitTimeInSeconds % 3600) / 60);
                    var seconds = waitTimeInSeconds % 60;

                    var waitTimeFormatted = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
                    console.log("Wait time for patient", patient.MedicalNo, ":", waitTimeFormatted); // Check calculated wait time
                    document.getElementById('wait-time-' + patient.MedicalNo).innerHTML = waitTimeFormatted;

                    // Update real-time progress bar.
                    var progressPercentage = Math.min((waitTimeInSeconds / (2 * 3600)) *100, 100);
                    var progressBar = document.getElementById('progress-bar-' + patient.MedicalNo);
                    if (progressBar) {
                        progressBar.style.width = progressPercentage + '%';
                        progressBar.className = 'progress-bar';
                        if(waitTimeInSeconds > 2 * 3600) {
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

setInterval(function() {
    window.location.href = window.location.href.split('?')[0] + "?t=" + new Date().getTime();
}, 300000);
