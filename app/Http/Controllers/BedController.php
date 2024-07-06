<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BedController extends Controller
{
    // Eloquent ORM X Eager Loading untuk keseimbangan efisiensi.
    public function index(Request $request)
    {
        // Aktifkan query log.
        DB::enableQueryLog();

        // Mengambil data dari database.
        $beds = Bed::with(['patient' => function($query) {
            $query->select('MRN', 'HomeAddress');
        }, 'patientNotes' => function($query) {
            $query->select('MRN', 'Notes');
        }])
            ->where('IsDeleted', 0)
            ->orderBy('BedID')
            ->get();

        // Menampilkan pencatatan eksekusi query.
        $queryLog = DB::getQueryLog(); 
        
        // Ambil waktu eksekusi dari query log pertama.
        $executionTime = isset($queryLog[0]['time']) ? $queryLog[0]['time'] : 'Tidak ada data';

        // Format query Log.
        Log::info('Eksekusi Query Log: ' . $executionTime . ' ms');

        // Menampilkan halaman dashboard.
        return view('Keuangan.index', compact('beds', 'executionTime'));
    }
}
