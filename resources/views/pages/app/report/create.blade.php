@extends('layouts.no-nav')

@section('title', 'Tambah Laporan')

@section('content')
    <h3 class="mb-3">Laporkan segera masalahmu di sini!</h3>

    <p class="text-description">Isi form dibawah ini dengan baik dan benar sehingga kami dapat memvalidasi dan
        menangani
        laporan anda
        secepatnya</p>

    <form action="{{ route('report.store') }}" method="POST" class="mt-4" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lng" name="longitude">

        <div class="mb-3">
            <label for="title" class="form-label">Judul Laporan</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                value="{{ old('title') }}">
            @error('title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="report_category_id" class="form-label">Kategori Laporan</label>
            <select class="form-select @error('report_category_id') is-invalid @enderror" id="report_category_id"
                name="report_category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if (old('report_category_id') == $category->id) selected @endif>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('report_category_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Bukti Laporan</label>
            <input type="file" accept="image/*" class="form-control @error('image') is-invalid @enderror" id="image"
                name="image" style="display:none;">
            <div class="d-grid gap-2 mb-2">
                <button type="button" id="btn-select-image" class="btn btn-outline-primary">
                    <i class="fa-solid fa-folder-open"></i> Pilih Gambar dari Perangkat
                </button>
                <button type="button" id="btn-take-photo" class="btn btn-outline-primary">
                    <i class="fa-solid fa-camera"></i> Ambil Foto
                </button>
                <button type="button" id="btn-remove-image" class="btn btn-outline-danger d-none">
                    <i class="fa-solid fa-trash"></i> Hapus Gambar
                </button>
            </div>
            <img alt="preview" id="image-preview" class="img-fluid rounded-2 mb-3 border d-none">
            @error('image')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <small class="text-muted">Format: JPG/PNG. Maks 2MB.</small>
        </div>

        <!-- Modal Kamera -->
        <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cameraModalLabel">Ambil Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <video id="video-webcam" autoplay playsinline style="width:100%;max-width:320px;"></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" id="btn-snap">Ambil Foto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Ceritakan Laporan Kamu</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                rows="5">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="map" class="form-label">Lokasi Laporan</label>
            <div id="map"></div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Alamat Lengkap</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                rows="3">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button class="btn btn-primary w-100 mt-2" type="submit" color="primary">
            Laporkan
        </button>
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentLat = null;
            let currentLng = null;
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const btnSelect = document.getElementById('btn-select-image');
            const btnRemove = document.getElementById('btn-remove-image');
            const btnTakePhoto = document.getElementById('btn-take-photo');
            let webcamStream = null;

            // Util: base64 -> Blob
            function base64ToBlob(base64) {
                try {
                    const parts = base64.split(',');
                    const mime = parts[0].match(/:(.*?);/)[1];
                    const byteString = atob(parts[1]);
                    const ab = new ArrayBuffer(byteString.length);
                    const ia = new Uint8Array(ab);
                    for (let i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
                    return new Blob([ab], { type: mime });
                } catch (e) {
                    alert('Gagal konversi gambar kamera: ' + e.message);
                    return null;
                }
            }

            function setPreview(file) {
                imagePreview.classList.remove('d-none');
                imagePreview.src = URL.createObjectURL(file);
                btnRemove.classList.remove('d-none');
            }

            function clearSelection() {
                imageInput.value = '';
                imagePreview.src = '';
                imagePreview.classList.add('d-none');
                btnRemove.classList.add('d-none');
            }

            btnSelect.addEventListener('click', () => imageInput.click());
            btnRemove.addEventListener('click', clearSelection);

            imageInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    setPreview(this.files[0]);
                }
            });

            // Kamera: buka modal dan aktifkan webcam
            btnTakePhoto.addEventListener('click', function () {
                // Ambil lokasi sebelum buka kamera
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        currentLat = pos.coords.latitude;
                        currentLng = pos.coords.longitude;
                    }, function (err) {
                        currentLat = null;
                        currentLng = null;
                        // Tidak perlu alert, user tetap bisa lanjut
                    });
                }
                const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
                modal.show();
                const video = document.getElementById('video-webcam');
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(function (stream) {
                            webcamStream = stream;
                            video.srcObject = stream;
                            video.play();
                        })
                        .catch(function (err) {
                            alert('Tidak dapat mengakses kamera: ' + err.message);
                        });
                } else {
                    alert('Browser tidak mendukung kamera.');
                }
            });

            // Ambil foto dari webcam
            document.getElementById('btn-snap').addEventListener('click', function () {
                const video = document.getElementById('video-webcam');
                const canvas = document.getElementById('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                const dataUrl = canvas.toDataURL('image/png');
                // Stop webcam
                if (webcamStream) {
                    webcamStream.getTracks().forEach(track => track.stop());
                    webcamStream = null;
                }
                // Tutup modal
                bootstrap.Modal.getInstance(document.getElementById('cameraModal')).hide();
                // Inject ke input file dan preview
                const blob = base64ToBlob(dataUrl);
                if (!blob) return;
                const file = new File([blob], 'camera.png', { type: 'image/png' });
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                setPreview(file);
                // Isi input lat/lng jika dapat lokasi
                if (currentLat && currentLng) {
                    document.getElementById('lat').value = currentLat;
                    document.getElementById('lng').value = currentLng;
                    // Fetch alamat otomatis
                    setTimeout(function () {
                        const lat = document.getElementById('lat').value;
                        const lng = document.getElementById('lng').value;
                        if (lat && lng) {
                            fetch(`/proxy/reverse-geocode?lat=${lat}&lon=${lng}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data && data.display_name) {
                                        document.getElementById('address').value = data.display_name;
                                    }
                                })
                                .catch(err => {
                                    console.error('Reverse geocoding gagal:', err);
                                });
                        }
                    }, 500);
                }
            });

            // Bersihkan webcam saat modal ditutup
            document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function () {
                const video = document.getElementById('video-webcam');
                if (video && video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                }
                if (webcamStream) {
                    webcamStream.getTracks().forEach(track => track.stop());
                    webcamStream = null;
                }
            });
        });
    </script>
@endsection