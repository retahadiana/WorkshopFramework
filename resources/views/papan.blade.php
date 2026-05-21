<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian Publik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; } /* slate-50 */
        
        .fade-in { animation: fadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1); }
        
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .glass-light {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        /* Ambient glow animation */
        @keyframes ambientPulse {
            0% { opacity: 0.5; }
            50% { opacity: 0.8; }
            100% { opacity: 0.5; }
        }
        .ambient-glow {
            animation: ambientPulse 5s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col overflow-y-auto relative">
    
    <!-- Ambient Background Lighting -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-violet-300/40 blur-[120px] ambient-glow"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[60%] rounded-full bg-fuchsia-300/30 blur-[120px] ambient-glow" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[40%] rounded-full bg-cyan-300/30 blur-[120px] ambient-glow" style="animation-delay: 1s;"></div>
    </div>

    <!-- Audio Element for Dingdong -->
    <audio id="audio-notif" src="/dingdong.mp3" preload="auto"></audio>

    <!-- Header -->
    <header class="glass-light px-8 py-5 flex justify-between items-center z-20 border-b border-white/80 sticky top-0">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-gradient-to-tr from-violet-600 to-fuchsia-600 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">Sistem Antrian Terpadu</h1>
                <p class="text-violet-600 font-bold tracking-wide text-sm mt-1">Layanan Publik Interaktif</p>
            </div>
        </div>
        
        <div id="status-koneksi" class="flex items-center text-sm font-bold text-emerald-700 bg-emerald-50 px-5 py-2.5 rounded-full border border-emerald-200 shadow-sm">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full mr-3 animate-pulse"></span>
            Real-Time Aktif
        </div>
    </header>

    <!-- Overlay for User Gesture Policy -->
    <div id="gesture-overlay" class="fixed inset-0 bg-slate-900/40 z-50 flex flex-col items-center justify-center backdrop-blur-xl">
        <div class="glass-light p-12 rounded-[3rem] max-w-xl text-center shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-violet-500 to-fuchsia-500"></div>
            <div class="w-24 h-24 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-violet-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold mb-4 text-slate-800">Inisialisasi Sistem Audio</h2>
            <p class="text-slate-500 mb-10 text-lg leading-relaxed">Browser membutuhkan izin interaksi pengguna agar fitur pemanggilan suara otomatis dapat berjalan.</p>
            <button onclick="activateSystem()" class="w-full bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-700 hover:to-fuchsia-700 text-white font-bold py-5 px-10 rounded-2xl text-xl shadow-lg shadow-violet-200 transition-all transform hover:-translate-y-1 active:scale-95">
                Aktifkan & Mulai Layar
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-[1920px] mx-auto p-6 md:p-8 flex flex-col lg:flex-row gap-8 min-h-[700px] z-10">
        
        <!-- Left Panel: Current Called -->
        <div class="w-full lg:w-2/3 glass-light rounded-[2.5rem] p-10 flex flex-col items-center justify-center relative overflow-hidden shadow-xl min-h-[500px]">
            <!-- decorative background shapes -->
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-violet-300/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-cyan-300/20 rounded-full blur-3xl"></div>
            
            <div id="current-display" class="text-center w-full flex flex-col items-center justify-center h-full relative z-10">
                <div class="bg-violet-50 border border-violet-100 px-6 py-2 rounded-full mb-10 shadow-sm">
                    <h2 class="text-lg text-violet-700 font-bold uppercase tracking-[0.3em]">Nomor Antrian</h2>
                </div>
                
                <div class="text-[14rem] font-black leading-none tracking-tighter mb-6 bg-clip-text text-transparent bg-gradient-to-br from-slate-800 to-slate-600 drop-shadow-sm" id="display-number">
                    ---
                </div>
                
                <div class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-fuchsia-600 mb-12 capitalize" id="display-name">
                    Menunggu Panggilan
                </div>
                
                <div class="mt-auto pt-10 border-t border-slate-200 w-full max-w-lg mx-auto">
                    <p class="text-slate-500 text-2xl font-semibold" id="display-loket">Silakan bersiap-siap di ruang tunggu</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Info & Next in line -->
        <div class="w-full lg:w-1/3 flex flex-col gap-8">
            
            <!-- Information Card -->
            <div class="glass-light rounded-[2rem] flex-1 min-h-[250px] overflow-hidden relative shadow-xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-violet-600 to-fuchsia-600 opacity-95"></div>
                <div class="relative z-10 h-full flex flex-col items-center justify-center p-8 text-center text-white">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-5 border border-white/30 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 tracking-wide drop-shadow-md">Pusat Informasi</h3>
                    <p class="text-violet-100 text-lg leading-relaxed font-medium">Mohon siapkan dokumen yang diperlukan sebelum menuju loket pelayanan.</p>
                </div>
            </div>

            <!-- Menunggu List -->
            <div class="glass-light rounded-[2rem] flex-[1.5] min-h-[350px] p-8 shadow-xl flex flex-col">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
                    <h3 class="text-2xl font-bold text-slate-800 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-cyan-500 shadow-sm mr-3"></div>
                        Antrian Berikutnya
                    </h3>
                    <span id="next-count" class="bg-cyan-50 text-cyan-700 font-bold text-sm px-4 py-1.5 rounded-full border border-cyan-100">0</span>
                </div>
                
                <div id="next-list" class="flex-grow overflow-hidden flex flex-col gap-4">
                    <div class="flex flex-col items-center justify-center h-full text-slate-400">
                        <div class="w-12 h-12 border-2 border-slate-200 rounded-full flex items-center justify-center mb-3 bg-slate-50">
                            <span class="block w-2 h-2 bg-slate-300 rounded-full"></span>
                        </div>
                        <p class="text-lg font-medium">Belum ada antrian</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-5 px-8 text-center lg:text-left flex flex-col lg:flex-row justify-between items-center border-t border-slate-200/50 bg-white/50 backdrop-blur-md">
        <p class="text-slate-500 text-sm font-semibold mb-2 lg:mb-0">Powered by Sistem Antrian Real-Time &copy; 2026</p>
        <div id="clock" class="font-mono text-xl font-bold text-violet-700 bg-violet-50 px-4 py-1.5 rounded-xl border border-violet-100 tracking-wider shadow-sm"></div>
    </footer>

    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        let systemActivated = false;
        let lastSpokenTime = null;
        let eventSource = null;

        function activateSystem() {
            const audio = document.getElementById('audio-notif');
            audio.load();
            
            if ('speechSynthesis' in window) {
                const testMsg = new SpeechSynthesisUtterance('');
                testMsg.volume = 0;
                window.speechSynthesis.speak(testMsg);
            }
            
            document.getElementById('gesture-overlay').classList.add('hidden');
            systemActivated = true;
            
            connectSSE();
        }

        function connectSSE() {
            eventSource = new EventSource('/sse/antrian');
            
            eventSource.onopen = () => {
                const status = document.getElementById('status-koneksi');
                status.innerHTML = '<span class="w-2.5 h-2.5 bg-emerald-500 rounded-full mr-3 animate-pulse"></span>Real-Time Aktif';
                status.className = 'flex items-center text-sm font-bold text-emerald-700 bg-emerald-50 px-5 py-2.5 rounded-full border border-emerald-200 shadow-sm transition-all';
            };

            eventSource.onerror = () => {
                const status = document.getElementById('status-koneksi');
                status.innerHTML = '<span class="w-2.5 h-2.5 bg-rose-500 rounded-full mr-3"></span>Menghubungkan ulang...';
                status.className = 'flex items-center text-sm font-bold text-rose-700 bg-rose-50 px-5 py-2.5 rounded-full border border-rose-200 shadow-sm transition-all';
            };

            eventSource.addEventListener('queue-update', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    updateBoard(data);
                } catch(err) {
                    console.error("Error parsing data", err);
                }
            });
        }

        function updateBoard(data) {
            // Update Current Called
            if (data.current) {
                const paddedNum = String(data.current.nomor_urut).padStart(3, '0');
                
                if (data.current.call_time !== lastSpokenTime) {
                    lastSpokenTime = data.current.call_time;
                    
                    const displayNum = document.getElementById('display-number');
                    const displayName = document.getElementById('display-name');
                    
                    displayNum.classList.remove('fade-in');
                    displayName.classList.remove('fade-in');
                    
                    void displayNum.offsetWidth;
                    
                    displayNum.textContent = paddedNum;
                    displayName.textContent = data.current.nama_guest;
                    
                    displayNum.classList.add('fade-in');
                    displayName.classList.add('fade-in');
                    
                    playAudioNotification(data.current.nomor_urut, data.current.nama_guest);
                }
            } else {
                document.getElementById('display-number').textContent = '---';
                document.getElementById('display-name').textContent = 'Menunggu Panggilan...';
            }

            // Update Next List
            const activeQueues = data.queues.filter(q => q.status === 'active');
            document.getElementById('next-count').textContent = activeQueues.length;
            
            const nextList = document.getElementById('next-list');
            if (activeQueues.length === 0) {
                nextList.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-slate-400">
                        <div class="w-12 h-12 border-2 border-slate-200 rounded-full flex items-center justify-center mb-3 bg-slate-50">
                            <span class="block w-2 h-2 bg-slate-300 rounded-full"></span>
                        </div>
                        <p class="text-lg font-medium">Belum ada antrian</p>
                    </div>`;
            } else {
                nextList.innerHTML = activeQueues.slice(0, 4).map((q, index) => `
                    <div class="bg-white hover:bg-slate-50 rounded-2xl p-5 flex items-center shadow-sm border border-slate-200 transition-colors">
                        <div class="bg-slate-100 text-violet-700 font-extrabold text-2xl w-14 h-14 flex items-center justify-center rounded-xl mr-5 border border-slate-200">
                            ${String(q.nomor_urut).padStart(3, '0')}
                        </div>
                        <div>
                            <div class="text-slate-800 font-bold text-xl capitalize">${q.nama_guest}</div>
                            <div class="text-slate-500 text-sm font-semibold mt-1">Antrian ke-${index + 1}</div>
                        </div>
                    </div>
                `).join('');
            }
        }

        function playAudioNotification(nomor, nama) {
            if (!systemActivated) return;

            const audio = document.getElementById('audio-notif');
            
            const pesan = new SpeechSynthesisUtterance(
                `Nomor antrian, ${nomor}. Atas nama, ${nama}. Silakan menuju meja pelayanan.`
            );
            pesan.lang = 'id-ID';
            pesan.rate = 0.85; 
            pesan.pitch = 1.0;
            
            if (!('speechSynthesis' in window)) {
                console.warn('Browser tidak mendukung Web Speech API');
                return;
            }

            window.speechSynthesis.cancel();
            audio.currentTime = 0;
            
            const playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    audio.onended = function() { window.speechSynthesis.speak(pesan); };
                }).catch(error => {
                    console.log("Audio dingdong failed or missing, falling back to speech", error);
                    window.speechSynthesis.speak(pesan);
                });
            } else {
                window.speechSynthesis.speak(pesan);
            }
        }
    </script>
</body>
</html>
