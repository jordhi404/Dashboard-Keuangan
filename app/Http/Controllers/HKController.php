<?php

namespace App\Http\Controllers;

use App\Models\HKModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HKController extends Controller
{
    private HKModel $hkModel;
    private $serviceUnitNames, $roomNames = [];

    public function __construct(HKModel $hkModel)
    {
        $this->hkModel = $hkModel;
    }

    public function showDashboard(Request $request)
    {
        $pages = [7, 10, 25, 50, 100];

        $serviceUnitId = $request->input('service_unit');
        $roomId = $request->input('room');
        $orderBy = $request->input('order_by');
        $sortBy = $request->input('sort_by');
        $perPage = $request->input('per_page', 7);

        $this->serviceUnitNames = $this->hkModel->getServiceUnitNames();
        $this->roomNames = $this->hkModel->getRoomNames($serviceUnitId);
        // dd($sortBy, $orderBy);
        $rooms = $this->hkModel->getListRoom(
            $serviceUnitId,
            $roomId,
            $perPage,
            orderBy: $orderBy,
            sortBy: $sortBy
        );

        return view('hk.dashboards.rooms', [
            'rooms' => $rooms,
            'serviceUnitNames' => $this->serviceUnitNames,
            'roomNames' => $this->roomNames,
            'pages' => $pages,
            'perPage' => $perPage,
        ]);
    }
}