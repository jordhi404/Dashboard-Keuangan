<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class KeuanganController extends Controller
{
    /* CONNECTION KE DATABASE SQLSRV UNTUK MENGAMBIL DATA PASIEN. */
    private function getPatientData()
    {
        $cacheKey = 'patientRanap';

        return Cache::remember($cacheKey, 300, function() {
            return DB::connection('sqlsrv')
                -> select("
                WITH Dashboard_CTE AS (
                    SELECT DISTINCT 
                        a.RegistrationNo,
                        r.ServiceUnitName,
                        a.BedCode,
                        a.MedicalNo,
                        a.PatientName,
                        r.CustomerType,
                        r.ChargeClassName,
                        RencanaPulang = 
                            CASE 
                                WHEN cv.PlanDischargeTime IS NULL
                                    THEN CAST(cv.PlanDischargeDate AS VARCHAR) + ' ' + CAST(cv.PlanDischargeTime AS VARCHAR)
                                ELSE CAST(cv.PlanDischargeDate AS VARCHAR) + ' ' + CAST(cv.PlanDischargeTime AS VARCHAR)
                            END,
                        Keperawatan =
                            (SELECT TOP 1 TransactionNo 
                            FROM PatientChargesHD
                            WHERE VisitID=cv.VisitID 
                            AND GCTransactionStatus<>'X121^999' 
                            AND GCTransactionStatus IN ('X121^001','X121^002','X121^003')
                            AND HealthcareServiceUnitID NOT IN (82,83,99,138,140,101,137)
                            ORDER BY TestOrderID ASC),
                        TungguJangdik = 
                            (SELECT TOP 1 TransactionNo 
                            FROM PatientChargesHD
                            WHERE VisitID=cv.VisitID 
                            AND GCTransactionStatus<>'X121^999' 
                            AND GCTransactionStatus IN ('X121^001','X121^002','X121^003')
                            AND HealthcareServiceUnitID IN (82,83,99,138,140)
                            ORDER BY TestOrderID ASC),
                        TungguFarmasi = 
                            (SELECT TOP 1 TransactionNo 
                            FROM PatientChargesHD
                            WHERE VisitID=cv.VisitID 
                            AND GCTransactionStatus<>'X121^999' 
                            AND GCTransactionStatus IN ('X121^001','X121^002','X121^003')
                            AND HealthcareServiceUnitID IN (101,137)
                            ORDER BY TestOrderID ASC),
                        RegistrationStatus = 
                            (SELECT TOP 1 IsLockDownNEW
                            FROM RegistrationStatusLog 
                            WHERE RegistrationID = a.RegistrationID 
                            ORDER BY ID DESC),
                        OutStanding =
                            (SELECT DISTINCT COUNT(GCTransactionStatus) 
                            FROM PatientChargesHD 
                            WHERE VisitID=cv.VisitID 
                            AND GCTransactionStatus IN ('X121^001','X121^002','X121^003')),
                        SelesaiBilling = 
                            (SELECT TOP 1 PrintedDate 
                            FROM ReportPrintLog 
                            WHERE ReportID=7012 
                            AND ReportParameter = CONCAT('RegistrationID = ',r.RegistrationID) 
                            ORDER BY PrintedDate DESC),
                        Keterangan =
                        CASE 
                            WHEN sc.StandardCodeName = '' OR sc.StandardCodeName IS NULL
                                THEN ''
                            ELSE sc.StandardCodeName
                        END,
                        pvn.NoteText
                    FROM vBed a
                    LEFT JOIN vPatient p ON p.MRN = a.MRN
                    LEFT JOIN PatientNotes pn ON pn.MRN = a.MRN
                    LEFT JOIN vRegistration r ON r.RegistrationID = a.RegistrationID
                    LEFT JOIN ConsultVisit cv ON cv.VisitID = r.VisitID
                    LEFT JOIN StandardCode sc ON sc.StandardCodeID = cv.GCPlanDischargeNotesType
                    LEFT JOIN PatientVisitNote pvn ON pvn.VisitID = cv.VisitID 
                        AND pvn.GCNoteType IN ('X312^001', 'X312^002', 'X312^003', 'X312^004', 'X312^005', 'X312^006')
                    WHERE a.IsDeleted = 0 
                    AND a.RegistrationID IS NOT NULL
                    AND cv.PlanDischargeDate IS NOT NULL
                    AND r.GCRegistrationStatus <> 'X020^006' -- Pendaftaran Tidak DiBatalkan
                )
                SELECT 
                    RegistrationNo,
                    ServiceUnitName,
                    BedCode,
                    MedicalNo,
                    PatientName,
                    CustomerType,
                    ChargeClassName,
                    RencanaPulang,
                    NoteText,
                    CASE
                        WHEN Keperawatan IS NOT NULL THEN 'TungguKeperawatan'
                        WHEN TungguJangdik IS NOT NULL THEN 'TungguJangdik'
                        WHEN TungguFarmasi IS NOT NULL THEN 'TungguFarmasi'
                        WHEN RegistrationStatus = 0 AND OutStanding > 0 AND SelesaiBilling IS NULL THEN 'TungguKasir'
                        WHEN RegistrationStatus = 1 AND OutStanding = 0 AND SelesaiBilling IS NULL THEN 'TungguKasir'
                        WHEN RegistrationStatus = 1 AND OutStanding = 0 AND SelesaiBilling IS NOT NULL THEN 'SelesaiKasir'
                    END AS Keterangan
                FROM Dashboard_CTE
            ");
        });
    }

    /* FUNCTION UNTUK MENAMPILKAN DATA DI DASHBOARD RANAP. */
    public function showdashboardKeuangan() {

        /* MENGAMBIL DATA PASIEN UNTUK DITAMPILKAN. */
            $patients = $this->getPatientData();
            $currentTime = Carbon::now();

            foreach ($patients as $patient) {
                // Patient's short note.
                $patient->short_note = $patient->NoteText ? Str::limit($patient->NoteText, 10) : '-';

                // Mengambil waktu rencana pulang
                $dischargeTime = Carbon::parse($patient->RencanaPulang);

                // Menghitung waktu tunggu
                if ($dischargeTime->gt($currentTime)) {
                    // Jika waktu rencana pulang di masa depan
                    $waitTime = '00:00:00'; // Waktu tunggu belum dimulai
                    $waitTimeInSeconds = 0; // Inisialisasi waitTimeInSeconds sebagai 0.
                } else {
                    // Menghitung selisih waktu
                    $waitTimeInSeconds = $dischargeTime->diffInSeconds($currentTime);

                    // Format waktu tunggu dalam format hh:mm:ss
                    $hours = floor($waitTimeInSeconds / 3600);
                    $minutes = floor(($waitTimeInSeconds % 3600) / 60);
                    $seconds = $waitTimeInSeconds % 60;
                    $waitTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                }    
                $patient->wait_time = $waitTime;

                $standardWaitTimeInSeconds = 7200; // 2 hours
                if ($patient->Keterangan == 'Tunggu Obat Farmasi') {
                    $standardWaitTimeInSeconds = 3600; // 1 hour
                } else if ($patient->Keterangan == 'Penyelesaian Administrasi Pasien (Billing)') {
                    $standardWaitTimeInSeconds = 900; // 15 minutes
                }

                $progressPercentage = min(($waitTimeInSeconds / $standardWaitTimeInSeconds) * 100, 100);
                $patient->progress_percentage = $progressPercentage;
            }

        /* MEMBUAT URUTAN UNTUK TAMPILAN KOLOM KETERANGAN.*/
            // Mapping data ke dalam array untuk diteruskan ke view
            $groupedData = [];
            foreach ($patients as $data) {
                $groupedData[$data->Keterangan][] = $data;
            }

            // Urutan kustom untuk 'keterangan'
            $order = [
                'Tunggu Penunjang Medik',
                'TungguKeperawatan',
                'TungguFarmasi',
                'TungguKasir',
                'SelesaiKasir'
            ];

            // Ambil data yang sudah dikelompokkan (groupedData)
            $groupedData = collect($groupedData)->sortBy(function($patients, $keterangan) use ($order) {
                return array_search($keterangan, $order);
            })->toArray();

            $allPatients = [];
            foreach ($groupedData as $patients) {
                foreach ($patients as $patient) {
                    $allPatients[] = $patient;
                }
            }

        /* WARNA HEADER KARTU BERDASARKAN customerType (PENJAMIN BAYAR). */
        $customerTypeColors = [
            'Rekanan' => 'orange',
            'Perusahaan' => 'pink',
            'Yayasan' => 'lime',
            'Karyawan - FASKES' => 'green',
            'Karyawan - PTGJ' => 'lightgreen',
            'Pemerintah' => 'red',
            'Rumah Sakit' => 'aqua',
            'BPJS - Kemenkes' => 'yellow',
            'Pribadi' => 'lightblue',
        ];

        

        /* MENGIRIM DATA KE VIEW. */
        return view('Keuangan.keuangan', compact('groupedData', 'allPatients','customerTypeColors'));
    }
}