<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class KeuanganController extends Controller
{
    /* CONNECTION KE DATABASE SQLSRV UNTUK MENGAMBIL DATA PASIEN. */
    private function getPatientData()
    {
        $cacheKey = 'keuanganRanap';

        return Cache::remember($cacheKey, 240, function() {
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
                        WHEN Keperawatan IS NOT NULL AND TungguJangdik IS NULL AND TungguFarmasi IS NOT NULL THEN 'Tunggu Keperawatan'
                        WHEN TungguJangdik IS NOT NULL AND Keperawatan IS NOT NULL AND TungguFarmasi IS NOT NULL THEN 'Tunggu Jangdik'
                        WHEN TungguFarmasi IS NOT NULL AND Keperawatan IS NULL AND TungguJangdik IS NULL THEN 'Tunggu Farmasi'
                        WHEN RegistrationStatus = 0 AND OutStanding > 0 AND SelesaiBilling IS NULL THEN 'Tunggu Kasir'
                        WHEN RegistrationStatus = 1 AND OutStanding = 0 AND SelesaiBilling IS NULL THEN 'Tunggu Kasir'
                        WHEN RegistrationStatus = 1 AND OutStanding = 0 AND SelesaiBilling IS NOT NULL THEN 'Selesai Kasir'
                    END AS Keterangan
                FROM Dashboard_CTE
            ");
        });
    }

    /* FUNCTION UNTUK MENAMPILKAN DATA DI DASHBOARD RANAP. */
    public function showDashboardKeuangan() {

        /* MENGAMBIL DATA PASIEN UNTUK DITAMPILKAN. */
            $patients = $this->getPatientData();
            $currentTime = Carbon::now();

            // Mengelompokkan pasien berdasarkan keterangan
            $groupedPatients = [
                'Tunggu Keperawatan' => [],
                'Tunggu Jangdik' => [],
                'Tunggu Farmasi' => [],
                'Tunggu Kasir' => [],
                'Selesai Kasir' => []
            ];

            foreach ($patients as $patient) {
                // Patient's short note.
                $patient->short_note = $patient->NoteText ? Str::limit($patient->NoteText, 10) : '-';

                // Mengambil waktu rencana pulang
                $dischargeTime = Carbon::parse($patient->RencanaPulang);

                // Menghitung waktu tunggu
                if ($dischargeTime->gt($currentTime)) {
                    // Jika waktu rencana pulang di masa depan
                    $waitTimeInSeconds = 0; // Inisialisasi waitTimeInSeconds sebagai 0.
                    $waitTime = '00:00:00'; // Waktu tunggu belum dimulai
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

                //$standardWaitTimeInSeconds = 7200; // 2 hours
                if ($patient->Keterangan == 'Tunggu Obat Farmasi') {
                    $standardWaitTimeInSeconds = 3600; // 1 hour
                } else{
                    $standardWaitTimeInSeconds = 900; // 15 minutes
                }

                $progressPercentage = min(($waitTimeInSeconds / $standardWaitTimeInSeconds) * 100, 100);
                $patient->progress_percentage = $progressPercentage;

                if (in_array($patient->Keterangan, ['Tunggu Keperawatan'])) {
                    $groupedPatients['Tunggu Keperawatan'][] = $patient;
                } elseif ($patient->Keterangan == 'Tunggu Jangdik') {
                    $groupedPatients['Tunggu Jangdik'][] = $patient;
                } elseif ($patient->Keterangan == 'Tunggu Farmasi') {
                    $groupedPatients['Tunggu Farmasi'][] = $patient;
                } elseif ($patient->Keterangan == 'Tunggu Kasir') {
                    $groupedPatients['Tunggu Kasir'][] = $patient;
                } elseif ($patient->Keterangan == 'Selesai Kasir') {
                    $groupedPatients['Selesai Kasir'][] = $patient;
                }
            }

        /* MEMBUAT URUTAN UNTUK TAMPILAN KOLOM KETERANGAN.*/
            // Mapping data ke dalam array untuk diteruskan ke view
            $groupedData = [];
            foreach ($patients as $data) {
                $groupedData[$data->Keterangan][] = $data;
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
        return view('Keuangan.keuangan', compact('patients', 'customerTypeColors', 'groupedPatients'));
    }
}