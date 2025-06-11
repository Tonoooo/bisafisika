<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BelajarFisika - Kuasai Fisika dengan Mudah</title>
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        /* Mendefinisikan palet warna kustom dan font utama */
        :root {
            --bg-main: #f6f0f0; /* Latar belakang utama yang sangat terang */
            --brand-secondary: #f2e2b1; /* Aksen warna krem/pasir */
            --brand-primary: #d5c7a3; /* Warna utama yang lebih gelap untuk kontras */
            --text-dark: #4a4137; /* Warna teks gelap yang serasi dengan palet */
            --text-light: #796e60;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
        }

        /* Kustomisasi warna untuk tombol dan elemen utama */
        .bg-brand-primary { background-color: var(--brand-primary); }
        .hover\:bg-brand-primary-dark:hover { background-color: #c1b38e; } /* Warna hover yang sedikit lebih gelap */
        .bg-brand-secondary { background-color: var(--brand-secondary); }
        .text-brand-primary { color: var(--brand-primary); }
        .text-dark { color: var(--text-dark); }
        .text-light { color: var(--text-light); }
        .border-brand-primary { border-color: var(--brand-primary); }

        /* Transisi halus untuk elemen interaktif */
        .transition-all-smooth {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="antialiased">

    <header class="py-6">
        <div class="container mx-auto px-6 lg:px-16 flex justify-between items-center">
            <div class="text-3xl font-bold text-dark">BelajarFisika</div>
            <nav>
                <a href="{{ url('/admin') }}" class="bg-brand-primary hover:bg-brand-primary-dark text-white font-bold py-2 px-6 rounded-full transition-all-smooth">
                    Masuk
                </a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-6 lg:px-16 mt-4 lg:mt-12">

        <section class="flex flex-col lg:flex-row items-center lg:justify-between py-12">
            <div class="lg:w-1/2 text-center lg:text-left mb-12 lg:mb-0">
                <h1 class="text-4xl lg:text-6xl font-extrabold text-dark mb-4 leading-tight">
                    Kuasai Fisika, <br class="hidden lg:block"> Taklukkan Setiap Rumus.
                </h1>
                <p class="text-lg lg:text-xl text-light mb-8 max-w-lg mx-auto lg:mx-0">
                    Platform belajar interaktif yang mengubah konsep sulit menjadi pemahaman yang mudah dan menyenangkan.
                </p>
                <a href="{{ url('/admin') }}" class="inline-block bg-brand-primary hover:bg-brand-primary-dark text-white font-bold py-4 px-10 rounded-full text-lg transition-all-smooth shadow-lg hover:shadow-xl">
                    Mulai Belajar Sekarang
                </a>
            </div>

            <div class="lg:w-1/2 flex justify-center lg:justify-end relative">
                <div class="absolute w-4/5 h-4/5 bg-brand-secondary rounded-full -z-10 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-60"></div>
                <img src="{{ asset('images/einstein.png') }}" alt="Gambar einstein BelajarFisika" class="w-4/5 max-w-sm lg:max-w-md h-auto drop-shadow-xl">
            </div>
        </section>

        <section class="py-20 lg:py-28">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-dark">Mengapa Belajar di BelajarFisika?</h2>
                <p class="text-lg text-light mt-2">Tiga alasan utama untuk meningkatkan skormu bersama kami.</p>
            </div>
             
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all-smooth text-center">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center bg-brand-secondary rounded-full mb-6">
                        <svg class="w-10 h-10 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0l-.07.002z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-dark mb-2">Beragam Bab Fisika yang Menarik</h3>
                    <p class="text-light leading-relaxed">Terdapat banyak bab fisika yang dapat kamu gunakan untuk belatih.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all-smooth text-center">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center bg-brand-secondary rounded-full mb-6">
                        <svg class="w-10 h-10 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-dark mb-2">Level soal </h3>
                    <p class="text-light leading-relaxed">Tantang dirimu dalam ujian dengan level soal yang beragam</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all-smooth text-center">
                     <div class="mx-auto w-20 h-20 flex items-center justify-center bg-brand-secondary rounded-full mb-6">
                        <svg class="w-10 h-10 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.211 1.012l-2.23 3.716c-.27.45-.72 1.01-1.254 1.01H4.5M9.75 3.104c.134-.02.272-.034.414-.034h4.5c.142 0 .28.014.414.034M9.75 3.104m0 0c-.227.054-.448.12-.663.197M9.75 15.75c1.333 0 2.57-.333 3.665-1.002m-5.33 1.002c-1.095-.67-2.332-1.002-3.665-1.002m12.66 0c.134.02.272.034.414.034h.023c.142 0 .28-.014.414-.034M12 21.75a2.25 2.25 0 002.25-2.25H9.75A2.25 2.25 0 0012 21.75z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-dark mb-2">Bank Soal Bervariasi</h3>
                    <p class="text-light leading-relaxed">Latihan soal dari berbagai topik untuk menguji dan memperdalam pemahamanmu.</p>
                </div>
                
            </div>
        </section>

    </main>

    <footer class="mt-12 py-8 bg-brand-primary text-center">
        <p class="text-white font-medium">&copy; {{ date('Y') }} Politeknik Negeri Bandung. All rights reserved.</p>
    </footer>

</body>
</html>