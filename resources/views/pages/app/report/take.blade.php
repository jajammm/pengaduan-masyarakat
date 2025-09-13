@extends('layouts.no-nav')

@section('title', 'Ambil Foto')

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center">
        <video autoplay="true" id="video-webcam">
            Browsermu tidak mendukung bro, upgrade donk!
        </video>

        <div class="d-flex justify-content-center mt-3 position-absolute bottom-0">
            <button class="btn btn-primary btn-snap mb-3 " onclick="takeSnapshot()">
                <i class="fas fa-camera"></i>
            </button>
        </div>
        <canvas id="canvas" style="display:none;"></canvas>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let webcamStream = null;
            startWebcam();
            function startWebcam() {
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
            }
            function stopWebcam() {
                const video = document.getElementById('video-webcam');
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                }
                if (webcamStream) {
                    webcamStream.getTracks().forEach(track => track.stop());
                    webcamStream = null;
                }
            }
            window.takeSnapshot = function () {
                const video = document.getElementById('video-webcam');
                const canvas = document.getElementById('canvas');
                if (!video.srcObject) {
                    alert('Kamera belum siap. Pastikan Anda sudah memberi izin.');
                    return;
                }
                if (video.readyState !== 4) {
                    alert('Kamera belum siap. Silakan tunggu beberapa detik.');
                    return;
                }
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                try {
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    // Simpan base64 ke sessionStorage, lalu redirect setelah benar-benar tersimpan
                    const dataUrl = canvas.toDataURL('image/png');
                    try {
                        sessionStorage.setItem('report_image', dataUrl);
                    } catch (e) {
                        alert('Gagal menyimpan gambar ke sessionStorage: ' + e.message);
                        return;
                    }
                    // Validasi simpan
                    if (sessionStorage.getItem('report_image')) {
                        window.location.href = "{{ route('report.create') }}?from=take";
                    } else {
                        alert('Gagal menyimpan gambar. Coba ulangi.');
                    }
                } catch (e) {
                    alert('Terjadi error saat mengambil gambar: ' + e.message);
                }
            }
        });
    </script>
@endpush