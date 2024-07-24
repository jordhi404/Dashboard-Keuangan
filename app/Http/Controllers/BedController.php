<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BedController extends Controller
{
    public function index()
    {
        // Mengambil data dari database.
        $beds = Bed::select(
            'a.RegistrationNo',
            'a.MedicalNo',
            'a.PatientName',
            'p.HomeAddress',
            'a.BusinessPartnerName',
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
            DB::raw('
                CASE 
                    WHEN sc.StandardCodeName = \'\' OR sc.StandardCodeName IS NULL
                    THEN \'\'
                    ELSE sc.StandardCodeName
                END AS Keterangan
            '),
            'a.RegistrationID'
        )
        ->from('vBed as a')
        ->leftJoin('vPatient as p', 'p.MRN', '=', 'a.MRN')
        ->leftJoin('PatientNotes as pn', 'pn.MRN', '=', 'a.MRN')
        ->leftJoin('vRegistration as r', 'r.RegistrationID', '=', 'a.RegistrationID')
        ->leftJoin('ConsultVisit as cv', 'cv.RegistrationID', '=', 'r.RegistrationID')
        ->leftJoin('StandardCode as sc', 'sc.StandardCodeID', '=', 'cv.GCPlanDischargeNotesType')
        ->where('a.IsDeleted', 0)
        ->where('a.RegistrationID')
        ->where('cv.PlanDischargeDate')
        ->where('r.GCRegistrationStatus', '<>', 'X020^006')
        ->orderBy('a.BedCode')
        ->distinct()
        ->get();

        return view('Keuangan_lama.index', compact('beds'));
    }
}
