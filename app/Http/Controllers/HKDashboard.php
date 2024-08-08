<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HKDashboard extends Controller
{
    public function index()
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

        return view('CS.cs', compact('beds'));
    }
}
