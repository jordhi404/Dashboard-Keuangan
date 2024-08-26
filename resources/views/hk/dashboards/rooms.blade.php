@extends('hk.layouts.main')
@section('container')
    <div class="container-fluid mt-2">
        <div class="row my-2">
            <div class="custom-inline-form">
                <form id="form-order" action="{{ route('hk') }}" method="GET" class="col-12 col-md-12">
                    <div class="input-group flex-column flex-md-row justify-content-start">
                        <div class="col-12 col-md-2 mb-2">
                            <label for="service_unit" class="form-label">Bangsal</label>
                            <select class="form-select" id="service_unit" name="service_unit"
                                aria-label="Default select example" onchange="this.form.submit()">
                                <option value="0">Semua</option>
                                @foreach ($serviceUnitNames as $serviceUnit)
                                    <option value="{{ $serviceUnit->ServiceUnitCode }}"
                                        {{ request('service_unit') == $serviceUnit->ServiceUnitCode ? 'selected' : '' }}>
                                        {{ $serviceUnit->ServiceUnitName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 mb-2">
                            <label for="room" class="form-label">Semua</label>
                            <select class="form-select" id="room" name="room" aria-label="Default select example"
                                onchange="this.form.submit()">
                                <option value="0">Semua</option>
                                @foreach ($roomNames as $room)
                                    <option value="{{ $room->RoomID }}"
                                        {{ request('room') == $room->RoomID ? 'selected' : '' }}>
                                        {{ $room->RoomCode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-stripped text-center table-custom-border">
                <thead>
                    <tr>
                        <th scope="col" class="align-middle">No</th>
                        <th scope="col" class="align-middle">Bangsal</th>
                        <th scope="col" class="align-middle">Kode Ruangan</th>
                        <th scope="col" class="align-middle">Kode Bed</th>
                        <th scope="col-md-12" style="width: 20%" class="align-middle">
                            <a href="@sortableCustom('hk', 'DurationOrder')" style="color: black" class="btn text-dark"><strong>Progress <i
                                        class="fa-solid fa-sort dark"></i></strong>
                            </a>
                        </th>
                        <th scope="col" class="align-middle">Kelas</th>
                        <th scope="col" class="align-middle">Status</th>
                        <th scope="col" class="align-middle">
                            <a href="@sortableCustom('hk', 'LastUnoccupiedDate')" style="color: black" class="btn text-dark"><strong>Jam Kosong <i
                                        class="fa-solid fa-sort dark"></i></strong>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse ($rooms as $key => $room)
                        @php
                            $page = request('page') ?? 1;
                            $numRow = ($page - 1) * $perPage + $key;
                        @endphp
                        <tr>
                            <td scope="row" class="fw-semibold">{{ ++$numRow }}</td>
                            <td scope="row" class="fw-semibold">{{ $room->ServiceUnitName }}</td>
                            <td scope="row" class="fw-semibold">{{ $room->RoomCode }}</td>
                            <td scope="row" class="fw-semibold">{{ $room->BedCode }}</td>
                            <td scope="row" class="fw-semibold">
                                <div class="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100" style="height: 25px;background-color:rgb(102, 99, 99)">
                                    <div id="progress-bar{{ ++$key }}"
                                        class="progress-bar bg-primary overflow-visible text-white"
                                        style="font-size: 15px;width: 0%; font-weight: bold;text-align: center;">
                                    </div>
                                </div>
                                <strong id="time-running{{ $key }}"></strong>
                            </td>
                            <td scope="row" class="fw-semibold">{{ $room->ClassName }}</td>
                            <td scope="row" class="fw-semibold">{{ $room->BedStatus }}</td>
                            <td scope="row" class="fw-semibold">
                                {{ \Carbon\Carbon::parse($room->LastUnoccupiedDate)->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">
                                <div class="alert alert-danger">
                                    Tidak ada kamar yang sudah selesai dipakai.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            <form action="{{ route('hk') }}" method="GET" class="mx-2">
                <!-- Include all existing query parameters as hidden fields in pagination -->
                <div class="input-group flex-column flex-md-row">
                    @foreach (request()->query() as $key => $value)
                        @if ($key != 'perPage')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    {{-- {{ $foundStatusOrder }} --}}
                    <select class="form-select" id="per_page" name="per_page" aria-label="Default select example"
                        onchange="this.form.submit()">
                        @foreach ($pages as $page)
                            <option value="{{ $page }}" {{ $perPage == $page ? 'selected' : '' }}>
                                {{ $page }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="mx-2">
                {{ $rooms->appends(request()->query())->links() }}
            </div>
            <div class="mt-2">
                Show {{ $rooms->count() }} of {{ $rooms->total() }} orders
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('#service_unit').select2({
                theme: 'bootstrap-5'
            });
        });

        $(document).ready(function() {
            $('#room').select2({
                theme: 'bootstrap-5'
            });
        });

        function getTimeString(currentTime, startTime, diffTimeRunning) {
            let timeRunning, hours, minutes, seconds, timeString;
            if (diffTimeRunning === 0) {
                timeRunning = Math.floor((currentTime - startTime) / 1000); // Konversi ke detik
            } else {
                timeRunning = Math.floor(diffTimeRunning / 1000); // Konversi ke detik
            }
            hours = Math.floor(timeRunning / 3600);
            minutes = Math.floor((timeRunning % 3600) / 60);
            seconds = timeRunning % 60;
            timeString = `${hours.toString().padStart(2, "0")}:${minutes
        .toString()
        .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
            return timeString;
        }

        let dataFromPHP = @json($rooms);

        function updateOrderProgressBar(data, index) {
            let i = index + 1;
            const standarTime = 1800000; //Standard waktu pembuatan order resep
            let startTime = null; // Waktu mulai order
            let endTime = null; // Waktu order selesai
            let elapsedTime = 0; // Waktu yang sudah berjalan
            let currentTime = new Date(); // Waktu live saat ini
            let percentageElapsedTime = 0; // Persentase waktu yang sudah berjalan
            //cek apakah data send_order tidak kosong
            if (data.LastUnoccupiedDate !== null) {
                //set start time dengan data LastUnoccupiedDate
                startTime = new Date(data.LastUnoccupiedDate);
            }

            // Ambil elemen dengan id "time-running" ke-i
            const textTimeRunning = document.getElementById(`time-running${i}`);

            // Jika semua waktu masih kosong, maka set waktu berjalan = 00:00:00
            if (data.LastUnoccupiedDate === null) {
                textTimeRunning.innerText = "00:00:00";
            }
            // Jika date time closed order sudah terisi, maka hitung waktu berjalan dari waktu start ke closed order
            else {
                // Hitung waktu berjalan dari waktu start ke waktu sekarang, jika closed order belum terisi
                elapsedTime = currentTime - startTime;
                textTimeRunning.innerText = getTimeString(currentTime, startTime, 0);
            }


            // Ambil elemen dengan ID "progress-bar" ke-i
            const progressBar = document.getElementById(`progress-bar${i}`);

            //Jika setiap waktu masing-masing data masih kosong, maka set progress bar = 0%
            if (data.LastUnoccupiedDate === null) {
                progressBar.style.display = "none";
                progressBar.innerText = "0%";
            }
            // Jika sudah ada waktu terisi, maka hitung persentase waktu yang sudah berjalan
            else {
                // Hitung persentase waktu yang sudah berjalan
                percentageElapsedTime = (elapsedTime / standarTime) * 100;
                //Tampilkan presentase waktu yang sudah berjalan tapi style width tidak melebih 100 %
                progressBar.style.width = Math.min(percentageElapsedTime, 100) + "%";
                //Tambahkan text persentase waktu yang sudah berjalan
                progressBar.innerText = percentageElapsedTime.toFixed(2) + "%";
            }

            // Periksa jika nilai persentase selisih waktu lebih dari 100
            if (percentageElapsedTime > 100) {
                // Jika ya, ubah warna bar menjadi merah
                progressBar.className = "progress-bar bg-danger";
                // Periksa jika nilai persentase selisih waktu = 100
            } else if (percentageElapsedTime === 100) {
                // Jika ya, ubah warna bar menjadi hijau
                progressBar.className = "progress-bar bg-success";
            }

            // Jika date time closed order masih kosong, maka update function progressBar setiap 1 detik
            setTimeout(() => updateOrderProgressBar(data, index), 1000);
        }

        dataFromPHP.data.forEach((data, index) => {
            updateOrderProgressBar(data, index);
        })
    </script>
@endsection

