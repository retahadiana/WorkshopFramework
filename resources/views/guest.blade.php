<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen relative overflow-hidden text-slate-800">
    <!-- Animated Background Blobs -->
    <div class="absolute top-0 left-10 w-72 h-72 bg-violet-300 rounded-full mix-blend-multiply filter blur-2xl opacity-50 animate-blob"></div>
    <div class="absolute top-0 right-10 w-72 h-72 bg-fuchsia-300 rounded-full mix-blend-multiply filter blur-2xl opacity-50 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-40 w-72 h-72 bg-cyan-300 rounded-full mix-blend-multiply filter blur-2xl opacity-50 animate-blob animation-delay-4000"></div>

    <!-- Registration Section -->
    <div class="glass p-8 md:p-12 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/60 w-full max-w-md relative z-10 transition-all duration-700 transform" id="registration-section">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-violet-600 to-fuchsia-500 text-white shadow-lg shadow-violet-200 mb-6 transform rotate-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 -rotate-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-violet-800 to-fuchsia-800 mb-2">Ambil Antrian</h2>
            <p class="text-slate-500 font-medium">Silakan masukkan nama Anda untuk mendaftar antrian hari ini.</p>
        </div>
        <form id="formGuest" class="space-y-6">
            <div>
                <label for="nama" class="block text-slate-700 font-bold mb-2 ml-1">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="w-full px-5 py-4 bg-white/70 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 focus:bg-white transition-all shadow-sm font-medium text-lg placeholder-slate-400" placeholder="Contoh: Budi Santoso" required>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold py-4 px-6 rounded-2xl hover:from-violet-700 hover:to-fuchsia-700 active:scale-[0.98] transition-all duration-300 shadow-xl shadow-violet-200/50 flex items-center justify-center group">
                <span>Daftar Sekarang</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>
    </div>

    <!-- Ticket Section -->
    <div class="glass p-10 md:p-14 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-white/60 w-full max-w-md text-center hidden transform scale-95 opacity-0 transition-all duration-700 relative z-10" id="ticket-section">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-green-500 rounded-full flex items-center justify-center shadow-lg shadow-green-200 border-4 border-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h2 class="text-slate-500 font-bold uppercase tracking-widest text-sm mb-4 mt-4">Nomor Antrian Anda</h2>
        <div class="text-[5rem] leading-none font-black text-transparent bg-clip-text bg-gradient-to-br from-violet-600 to-fuchsia-600 mb-2 drop-shadow-sm" id="ticket-number"></div>
        <div class="text-2xl text-slate-800 font-bold mb-8 capitalize" id="ticket-name"></div>
        
        <div class="bg-violet-50/80 border border-violet-100 text-violet-800 text-sm font-medium rounded-2xl p-5 mb-8 text-center leading-relaxed">
            Pendaftaran berhasil! Silakan tunggu di ruang tunggu hingga nomor Anda dipanggil pada layar monitor.
        </div>
        <button onclick="location.reload()" class="w-full bg-white border-2 border-slate-200 text-slate-600 font-bold py-4 px-6 rounded-2xl hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all duration-300 active:scale-[0.98]">
            Kembali ke Awal
        </button>
    </div>

    <script>
        document.getElementById('formGuest').addEventListener('submit', function(e) {
            e.preventDefault();
            const nama = document.getElementById('nama').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            submitBtn.disabled = true;

            fetch('/guest/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ nama: nama })
            })
            .then(response => response.json())
            .then(data => {
                const regSection = document.getElementById('registration-section');
                const ticketSection = document.getElementById('ticket-section');
                
                regSection.classList.add('-translate-y-10', 'opacity-0');
                
                setTimeout(() => {
                    regSection.classList.add('hidden');
                    ticketSection.classList.remove('hidden');
                    
                    // trigger reflow
                    void ticketSection.offsetWidth;
                    
                    ticketSection.classList.remove('scale-95', 'opacity-0');
                    ticketSection.classList.add('scale-100', 'opacity-100');
                    
                    document.getElementById('ticket-number').textContent = String(data.nomor_urut).padStart(3, '0');
                    document.getElementById('ticket-name').textContent = data.nama_guest;
                }, 400);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mendaftar antrian.');
                submitBtn.innerHTML = originalBtnHtml;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
