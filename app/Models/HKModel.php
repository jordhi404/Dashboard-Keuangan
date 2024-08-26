<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HKModel extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    public function getListRoom(
        $serviceUnit,
        $room,
        $perPage,
        $sortBy,
        $orderBy
    ) {
        $query = DB::connection('sqlsrv')
            ->table('vBed')
            ->select(
                'BedID',
                'BedCode',
                'RoomID',
                'RoomCode',
                'RoomName',
                'GCBedStatus',
                'BedStatus',
                'ServiceUnitCode',
                'ServiceUnitName',
                'ClassID',
                'ClassCode',
                'ClassName',
                'GCClassRL',
                'LastUnoccupiedDate'
            )
            ->addSelect(
                DB::raw("CASE
                    WHEN LastUnoccupiedDate IS NOT NULL
                        THEN DATEDIFF(SECOND, LastUnoccupiedDate, GETDATE())
                    ELSE
                        NULL
                    END
                AS DurationOrder"),
            )
            ->where('IsDeleted', 0)
            ->whereIn('GCBedStatus', ['0116^H'])
            ->whereNotNull('ServiceUnitCode');
        // ->where('ServiceUnitCode', $serviceUnit);

        if ($serviceUnit) {
            $query->where('ServiceUnitCode', $serviceUnit);
        }

        if ($room) {
            $query->where('RoomID', $room);
        }

        if ($sortBy && $orderBy) {
            $query->orderBy($orderBy, $sortBy);
        } else {
            $query->orderBy('LastUnoccupiedDate', 'asc');
        }

        $result =  $query
            ->paginate($perPage);

        return $result;
    }

    function getServiceUnitNames()
    {
        $serviceUnitNames = DB::connection('sqlsrv')
            ->table('vBed')
            ->select(
                'ServiceUnitCode',
                'ServiceUnitName',
            )
            ->where('IsDeleted', 0)
            ->whereIn('GCBedStatus', ['0116^H'])
            ->whereNotNull('ServiceUnitCode')
            ->distinct()
            ->get();
        return $serviceUnitNames;
    }

    function getRoomNames($serviceUnitId)
    {
        $roomNames = DB::connection('sqlsrv')
            ->table('vBed')
            ->select(
                'RoomID',
                'RoomCode',
            )
            ->where('IsDeleted', 0)
            ->whereIn('GCBedStatus', ['0116^H'])
            ->whereNotNull('ServiceUnitCode');

        if ($serviceUnitId) {
            $roomNames->where('ServiceUnitCode', $serviceUnitId);
        }

        $result =  $roomNames
            ->distinct()
            ->get();

        return $result;
    }
}