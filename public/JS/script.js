// Fungsi menghitung waktu tunggu pasien sejak assign rencana pulang.
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

// Fungsi popover untuk note pasien.
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi semua popover
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Fungsi filter pasien.
document.addEventListener("DOMContentLoaded", function() {
    const customerTypeSelect = document.getElementById('customer_type');
    const patientCards = document.querySelectorAll('.card');
    
    // Mengambil semua jenis customer yang unik dari kartu yang ada di halaman
    const customerTypes = [...new Set([...patientCards].map(card => card.getAttribute('data-customer-type')))];
    
    // Tambahkan opsi ke dropdown berdasarkan customerTypes
    customerTypes.forEach(type => {
        let option = document.createElement('option');
        option.value = type;
        option.text = type;
        customerTypeSelect.add(option);
    });

    customerTypeSelect.addEventListener('change', function() {
        filterPatientCards();
    });

    function filterPatientCards() {
        const selectedCustomerType = customerTypeSelect.value;
        let filteredCount = 0;
        
        patientCards.forEach(card => {
            const cardCustomerType = card.getAttribute('data-customer-type');
            
            if (selectedCustomerType === "" || selectedCustomerType === cardCustomerType) {
                card.style.display = "block";
                filteredCount++;
            } else {
                card.style.display = "none";
            }
        });
    
        // Update jumlah pasien terfilter di navbar
        const filteredPatientCountElement = document.getElementById('filteredPatientCount');
        if (filteredPatientCountElement) {
            filteredPatientCountElement.innerText = filteredCount;
        } else {
            console.error('Element dengan ID filteredPatientCount tidak ditemukan.');
        }
    
        // Update keterangan filter di navbar
        const filterDescriptionElement = document.getElementById('filterDescription');
        if (filterDescriptionElement) {
            const filterDescription = selectedCustomerType ? `Filter: ${selectedCustomerType}` : 'Tidak ada filter';
            filterDescriptionElement.innerText = filterDescription;
        } else {
            console.error('Element dengan ID filterDescription tidak ditemukan.');
        }
    
        updatePatientCounts();
    }

    function updatePatientCounts() {
        const customerTypes = [...new Set([...patientCards].map(card => card.getAttribute('data-customer-type')))];
        const patientCounts = customerTypes.reduce((acc, type) => {
            acc[type] = document.querySelectorAll(`.card[data-customer-type="${type}"]:not([style*="display: none"])`).length;
            return acc;
        }, {});

        const countsContainer = document.querySelector('.patient-counts');
        countsContainer.innerHTML = '';
        for (const type in patientCounts) {
            countsContainer.innerHTML += `<div>${type}: ${patientCounts[type]}</div>`;
        }
    }
});