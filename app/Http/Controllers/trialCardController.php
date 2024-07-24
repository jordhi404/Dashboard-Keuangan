<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class trialCardController extends Controller
{
    // Method to get data based on query.
    private function getPatientData($registrationID = null)
    {
        $query = Bed::select(
            'a.RegistrationNo',
            'a.MedicalNo',
            'a.PatientName',
            'p.HomeAddress',
            'a.BusinessPartnerName',
            'r.ChargeClassName',
            'a.BedCode',
            'a.BedStatus',
            'a.ParamedicName',
            'sc.StandardCodeID',
            DB::raw('
                CASE 
                    WHEN cv.PlanDischargeTime IS NULL
                    THEN CAST(cv.PlanDischargeDate AS VARCHAR) + CAST(cv.PlanDischargeTime AS VARCHAR)
                    ELSE CAST(cv.PlanDischargeDate AS DATETIME) + CAST(cv.PlanDischargeTime AS TIME)
                END AS RencanaPulang
            '),
            DB::raw("
                COALESCE(sc.StandardCodeName, '') AS Keterangan
            "),
            'cv.GCPlanDischargeNotesType',
            'a.RegistrationID'
        )
        ->from('vBed as a')
        ->leftJoin('vPatient as p', 'p.MRN', '=', 'a.MRN')
        ->leftJoin('PatientNotes as pn', 'pn.MRN', '=', 'a.MRN')
        ->leftJoin('vRegistration as r', 'r.RegistrationID', '=', 'a.RegistrationID')
        ->leftJoin('ConsultVisit as cv', 'cv.RegistrationID', '=', 'r.RegistrationID')
        ->leftJoin('StandardCode as sc', 'sc.StandardCodeID', '=', 'cv.GCPlanDischargeNotesType')
        ->where('a.IsDeleted', 0)
        ->whereNotNull('a.RegistrationID')
        ->whereNotNull('cv.PlanDischargeDate')
        ->where('r.GCRegistrationStatus', '<>', 'X020^006')
        ->orderBy('a.BedCode')
        ->distinct();
        
        if($registrationID)
        {
            $query->where('a.RegistrationID', $registrationID);
            return $query->first();
        } 
        else 
        {
            return $query->get()->groupBy('Keterangan');
        }
    }

    // Controller method to show patient cards.
    public function showPatientCards() {
        $patients = $this->getPatientData();
        $currentTime = Carbon::now();
    
        // Menambahkan waktu sekarang ke Debugbar
        Debugbar::info('Current Time: ' . $currentTime->format('Y-m-d H:i:s'));

        // Mengelompokkan pasien berdasarkan keterangan
        $groupedPatients = [
            'Ruangan' => [],
            'Jarsdik' => [],
            'Farmasi' => [],
            'Kasir' => []
        ];
    
        foreach ($patients as $group => $patientGroup) {
            foreach ($patientGroup as $patient) {
                // Mengambil waktu rencana pulang
                $dischargeTime = Carbon::parse($patient->RencanaPulang);
    
                // Menghitung waktu tunggu
                if ($dischargeTime->gt($currentTime)) {
                    // Jika waktu rencana pulang di masa depan
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
    
                // Hitung persentase kemajuan
                $patient->progress_percentage = min(($dischargeTime->diffInMinutes($currentTime) / 120) * 100, 100);

                // Mengelompokkan berdasarkan Keterangan
                if (in_array($patient->Keterangan, ['Tunggu Dokter', 'Observasi Pasien', 'Lain - Lain'])) {
                    $groupedPatients['Ruangan'][] = $patient;
                } elseif ($patient->Keterangan == 'Tunggu Hasil Pemeriksaan Penunjang') {
                    $groupedPatients['Jarsdik'][] = $patient;
                } elseif ($patient->Keterangan == 'Tunggu Obat Farmasi') {
                    $groupedPatients['Farmasi'][] = $patient;
                } elseif ($patient->Keterangan == 'Penyelesaian Administrasi Pasien (Billing)') {
                    $groupedPatients['Kasir'][] = $patient;
                }
            }
        }
        
        // Hitung jumlah kartu tiap kolom.
        $patientCounts = [
            'Ruangan' => count($groupedPatients['Ruangan']),
            'Jarsdik' => count($groupedPatients['Jarsdik']),
            'Farmasi' => count($groupedPatients['Farmasi']),
            'Kasir' => count($groupedPatients['Kasir']),
        ];

        return view('trial.cardsViewTrial', compact('patients', 'groupedPatients', 'patientCounts'));
    }

    // Controller method to show patient's details.
    public function showPatientDetails($id) {
        $patient = $this->getPatientData($id);
        $currentTime = Carbon::now();
        $dischargeTime = Carbon::parse($patient->RencanaPulang);

        Debugbar::info('current time: '.$currentTime->format('Y-m-d H:i:s'));
        Debugbar::info('discharge time: '.$dischargeTime);
        return view('trial.detailPasien', compact('patient'));
    }
}
