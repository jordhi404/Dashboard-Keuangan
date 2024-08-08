<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class trialCardController extends Controller
{
    // Method to get data based on query.
    private function getPatientData($registrationID = null)
    {
        $query = Bed::select(
            'a.RegistrationNo',
            'a.MedicalNo',
            'a.PatientName',
            'r.CustomerType',
            'r.ChargeClassName',
            'a.BedCode',
            'a.BedStatus',
            'a.ParamedicName',
            DB::raw('
                CASE 
                    WHEN cv.PlanDischargeTime IS NULL
                    THEN CAST(cv.PlanDischargeDate AS VARCHAR) + CAST(cv.PlanDischargeTime AS VARCHAR)
                    ELSE CAST(cv.PlanDischargeDate AS DATETIME) + CAST(cv.PlanDischargeTime AS TIME)
                END AS RencanaPulang
            '),
            'cv.GCPlanDischargeNotesType',
            DB::raw("
                COALESCE(sc.StandardCodeName, '') AS Keterangan
            "),
            'pvn.GCNoteType',
	        'pvn.NoteText',
            'a.RegistrationID'
        )
        ->from('vBed as a')
        ->leftJoin('vPatient as p', 'p.MRN', '=', 'a.MRN')
        ->leftJoin('PatientNotes as pn', 'pn.MRN', '=', 'a.MRN')
        ->leftJoin('vRegistration as r', 'r.RegistrationID', '=', 'a.RegistrationID')
        ->leftJoin('ConsultVisit as cv', 'cv.RegistrationID', '=', 'r.RegistrationID')
        ->leftJoin('StandardCode as sc', 'sc.StandardCodeID', '=', 'cv.GCPlanDischargeNotesType')
        ->leftJoin('PatientVisitNote as pvn', function($join) {
            $join->on('pvn.VisitID', '=', 'cv.VisitID')
                 ->where('pvn.GCNoteType', '=', 'X312^003');
        }) 
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
            'Jangdik' => [],
            'Farmasi' => [],
            'Kasir' => []
        ];
    
        foreach ($patients as $group => $patientGroup) {
            foreach ($patientGroup as $patient) {
                // Patient's short note.
                $patient->short_note = $patient->NoteText ? Str::limit($patient->NoteText, 10) : '-';
                $patient->full_note = $patient->NoteText ? $patient->NoteText : '-';

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

                if (in_array($patient->Keterangan, ['Tunggu Dokter', 'Observasi Pasien', 'Lain - Lain'])) {
                    $groupedPatients['Ruangan'][] = $patient;
                } elseif ($patient->Keterangan == 'Tunggu Hasil Pemeriksaan Penunjang') {
                    $groupedPatients['Jangdik'][] = $patient;
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
            'Jangdik' => count($groupedPatients['Jangdik']),
            'Farmasi' => count($groupedPatients['Farmasi']),
            'Kasir' => count($groupedPatients['Kasir']),
        ];

        // Color by CustomerType.
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

        // Total pasien rencana pulang.
        $totalPatient = array_sum($patientCounts);

        return view('trial.cardsViewTrial', compact('patients', 'groupedPatients', 'patientCounts', 'totalPatient', 'customerTypeColors'));
    }
}