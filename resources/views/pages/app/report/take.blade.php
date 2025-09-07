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
        let webcamStream = null;
        window.onload = function () {
            startWebcam();
        };
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
        function takeSnapshot() {
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
                canvas.toBlob(function (blob) {
                    if (!blob) {
                        alert('Gagal mengambil gambar.');
                        return;
                    }
                    sessionStorage.setItem('report_image', canvas.toDataURL('image/png'));
                    window.location.href = "{{ route('report.create') }}?from=take";
                }, 'image/png');
            } catch (e) {
                alert('Terjadi error saat mengambil gambar: ' + e.message);
            }
        }
    </script>
@endpush