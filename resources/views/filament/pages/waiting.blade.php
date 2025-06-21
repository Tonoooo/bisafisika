<x-filament::page>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-center">Tunggu Verifikasi</h2>
        <p class="text-center mb-4">Akun Anda sedang menunggu verifikasi oleh super admin.</p>
         @php
            $message = "Kepada Yth. Saya bermaksud untuk meminta verifikasi atas nama " . urlencode(auth()->user()->name);
            if (auth()->user()->school) {
                $message .= " dari " . urlencode(auth()->user()->school->name);
            }

            $whatsappLink = "https://wa.me/6281356704272?text=" . $message;
        @endphp
        <a href="{{ $whatsappLink }}" class="block w-full text-center bg-green-500 text-white p-2 rounded hover:bg-green-600">Hubungi Admin untuk Meminta Verifikasi</a> {{-- Ubah teks tombol jika perlu --}}
    </div>
    <script>
        setInterval(() => {
            fetch('/api/check-verification')
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        window.location.href = '/dashboard';
                    }
                });
        }, 5000);
    </script>
</x-filament::page>