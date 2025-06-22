<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        body {
            background-color: #ffedcc;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }

        .verification-container {
            max-width: 500px;
            margin: 20px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
            text-align: center;
            border-top: 5px solid #ffac40;
        }

        .icon-container {
            margin: 0 auto 20px auto;
            width: 80px;
            height: 80px;
            background-color: #ffb95e; 
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-container svg {
            width: 40px;
            height: 40px;
            stroke: #994a00; 
            animation: spin 2s linear infinite; 
        }

        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .verification-container h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #2c3e50; 
        }

        .verification-container p {
            font-size: 16px;
            color: #7f8c8d; 
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        @php
            $message = "Kepada Admin. Saya bermaksud untuk meminta verifikasi atas nama " . urlencode(auth()->user()->name);

            if (auth()->user()->school) {
                $message .= " dari " . urlencode(auth()->user()->school->name);
            }

            $whatsappLink = "https://wa.me/6285221867597?text=" . $message;
        @endphp

        .whatsapp-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 20px;
            background-color: #25D366; 
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .whatsapp-button img {
            width: 24px;
            height: 24px;
            margin-right: 12px; 
        }

        .whatsapp-button:hover {
            background-color: #1EBE57;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
        }

    </style>
</head>
<body>

    <div class="verification-container">
        <div class="icon-container">
            <!-- Ikon Jam (SVG) dengan animasi -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
               <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
               <path d="M12 12l0 -3.5"></path>
               <path d="M12 12l5 2.5"></path>
            </svg>
        </div>
        
        <h2>Tunggu Verifikasi</h2>
        <p>Akun Anda sedang dalam proses peninjauan. Silakan hubungi Admin untuk mempercepat proses verifikasi.</p>
        
        <a href="{{ $whatsappLink }}" class="whatsapp-button" target="_blank">
            <!-- Pastikan path ke gambar ini benar -->
            <img src="{{ asset('images/wa_icon.png') }}" alt="WhatsApp Icon">
            Hubungi Admin via WhatsApp
        </a>
    </div>

    <script>
        // Script Anda tidak perlu diubah, tetap berfungsi seperti sebelumnya
        setInterval(() => {
            fetch('/api/check-verification')
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        window.location.href = '/admin';
                    }
                });
        }, 5000); // Cek setiap 5 detik
    </script>

</body>
</html>