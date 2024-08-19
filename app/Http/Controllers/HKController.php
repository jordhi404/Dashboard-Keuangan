<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HKController extends Controller
{
    private function getRoomData()
    {
        $beds = DB::connection('sqlsrv')
            ->table('vBed')
            ->select('BedID', 'BedCode', 'RoomID', 'RoomCode', 'RoomName', 'GCBedStatus', 'BedStatus', 'ServiceUnitCode', 'ServiceUnitName', 'ClassID', 'ClassCode', 'ClassName', 'GCClassRL', 'LastUnoccupiedDate')
            ->where('IsDeleted', 0)
            ->whereIn('GCBedStatus', ['0116^H'])
            ->whereNotNull('ServiceUnitCode')
            ->orderBy('ServiceUnitCode')
            ->orderBy('LastUnoccupiedDate')
            ->orderBy('BedCode')
            ->take(100)
            ->get();

        return $beds;
    }

    public function showHKDashboard() {
        $rooms = $this->getRoomData();
        $currentTime = Carbon::now();

        foreach ($rooms as $room) {
            // Mengambil waktu pembersihan.
            $lastUnoccupiedTime = Carbon::parse($room->LastUnoccupiedDate);

            // Menghitung waktu pembersihan.
            $cleaningDurationHours = 24;
            $standardCleaningDuration = Carbon::now()->subHours($cleaningDurationHours);

            if ($lastUnoccupiedTime->gt($standardCleaningDuration)) {
                // Waktu kamar kosong di masa depan.
                $cleanTime = '00:00:00';
            } else {
                // Menghitung selisih waktu.
                $cleanTimeInSeconds = $lastUnoccupiedTime->diffInSeconds($currentTime);

                // Format waktu pembersihan.
                $hours = floor($cleanTimeInSeconds / 3600);
                $minutes = floor(($cleanTimeInSeconds % 3600) / 60);
                $seconds = $cleanTimeInSeconds % 60;
                $cleanTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            $room->clean_time = $cleanTime;
        }
          
        // Mengelompokkan kamar per gedung.
        // $groupedRooms = [
        //     'gd. ASA' => [],
        //     'gd. TJANT' => [],
        //     'gd. TJANB' => [],
        //     'gd. KWEE' => [],
        //     'gd. IBU' => [],
        //     'gd. ANAK' => [],
        //     'gd. KEBIDANAN' => [],
        //     'ICU' => [],
        //     'PICU/NICU' => [],
        // ];

        return view('CS.cs', ['rooms' => $rooms]);
    }
}
