<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .card-shadow {
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col relative overflow-x-hidden">
    <!-- Subtle Background Elements -->
    <div class="fixed top-0 left-0 w-full h-96 bg-gradient-to-b from-violet-100/50 to-transparent -z-10 pointer-events-none"></div>

    <!-- Header -->
    <header class="glass-header sticky top-0 z-40 px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-violet-600 to-fuchsia-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-violet-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-violet-800 to-fuchsia-800">Manajemen Antrian</h1>
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-wider">Dashboard Real-Time</p>
                </div>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <div class="hidden md:flex items-center text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    SSE Connected
                </div>
                <button onclick="resetAntrian()" class="bg-white hover:bg-red-50 text-slate-600 hover:text-red-600 font-semibold px-4 py-2 rounded-xl transition-all border border-slate-200 hover:border-red-200 text-sm shadow-sm flex items-center group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Data
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto w-full px-6 flex-grow flex flex-col lg:flex-row gap-8 pb-10">
        
        <!-- Panggilan Saat Ini (Center Focus) -->
        <div class="w-full lg:w-1/3 flex flex-col">
            <div class="bg-gradient-to-br from-violet-900 via-indigo-900 to-slate-900 p-8 rounded-[2rem] shadow-2xl shadow-violet-900/20 text-center h-full flex flex-col relative overflow-hidden group">
                <!-- Decorative BG for card -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-fuchsia-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:bg-fuchsia-500/20 transition-all duration-700"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 group-hover:bg-cyan-500/20 transition-all duration-700"></div>
                
                <h2 class="text-xs font-bold text-violet-200 uppercase tracking-[0.2em] mb-8 relative z-10">Sedang Dipanggil</h2>
                
                <div id="current-queue" class="flex-grow flex flex-col items-center justify-center min-h-[250px] relative z-10">
                    <div class="text-white/40 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium tracking-wide">Belum ada aktif</span>
                    </div>
                </div>
                
                <div class="mt-8 relative z-10">
                    <button onclick="panggilBerikutnya()" class="w-full bg-white text-violet-900 hover:bg-violet-50 font-bold py-4 px-4 rounded-2xl shadow-lg transition-all transform active:scale-95 text-lg flex items-center justify-center border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        Panggil Berikutnya
                    </button>
                </div>
            </div>
        </div>

        <!-- Daftar Antrian (Active & Terlambat) -->
        <div class="w-full lg:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Menunggu -->
            <div class="bg-white p-6 md:p-8 rounded-[2rem] card-shadow border border-slate-100 flex flex-col h-[600px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-500 shadow-sm shadow-cyan-300"></div>
                        Menunggu
                    </h2>
                    <span id="active-count" class="bg-cyan-50 text-cyan-700 text-xs font-bold px-3 py-1.5 rounded-full border border-cyan-100">0</span>
                </div>
                <div class="w-full h-px bg-gradient-to-r from-slate-100 via-slate-200 to-transparent mb-6"></div>
                
                <div id="active-list" class="space-y-3 overflow-y-auto custom-scrollbar pr-2 flex-grow">
                    <!-- Items injected by JS -->
                    <div class="flex items-center justify-center h-full text-slate-400 text-sm font-medium">Memuat data...</div>
                </div>
            </div>

            <!-- Terlambat -->
            <div class="bg-white p-6 md:p-8 rounded-[2rem] card-shadow border border-slate-100 flex flex-col h-[600px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm shadow-rose-300"></div>
                        Terlewat
                    </h2>
                    <span id="terlambat-count" class="bg-rose-50 text-rose-700 text-xs font-bold px-3 py-1.5 rounded-full border border-rose-100">0</span>
                </div>
                <div class="w-full h-px bg-gradient-to-r from-slate-100 via-slate-200 to-transparent mb-6"></div>
                
                <div id="terlambat-list" class="space-y-3 overflow-y-auto custom-scrollbar pr-2 flex-grow">
                    <!-- Items injected by JS -->
                    <div class="flex items-center justify-center h-full text-slate-400 text-sm font-medium">Memuat data...</div>
                </div>
            </div>

        </div>
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const source = new EventSource('/sse/antrian');

        source.addEventListener('queue-update', function(e) {
            try {
                const data = JSON.parse(e.data);
                updateUI(data);
            } catch(err) {
                console.error("Error parsing SSE data", err);
            }
        });

        function updateUI(data) {
            // 1. Update Current Called
            const currentContainer = document.getElementById('current-queue');
            if (data.current) {
                currentContainer.innerHTML = `
                    <div class="text-xs font-bold text-fuchsia-300 bg-fuchsia-900/50 px-4 py-1.5 rounded-full mb-6 inline-block border border-fuchsia-500/30">Nomor Antrian</div>
                    <div class="text-8xl font-black text-white mb-2 tracking-tighter drop-shadow-xl">${String(data.current.nomor_urut).padStart(3, '0')}</div>
                    <div class="text-2xl font-semibold text-violet-200 capitalize">${data.current.nama_guest}</div>
                    
                    <button onclick="tandaiTerlambat('${data.current.id}')" class="mt-10 bg-white/10 hover:bg-rose-500/20 text-white/80 hover:text-rose-200 border border-white/20 hover:border-rose-400/50 px-6 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center backdrop-blur-sm group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tandai Tidak Hadir (Skip)
                    </button>
                `;
            } else {
                currentContainer.innerHTML = `
                    <div class="text-white/30 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium tracking-wide">Belum ada antrian aktif</span>
                    </div>
                `;
            }

            // 2. Update Active List
            const activeQueues = data.queues.filter(q => q.status === 'active');
            document.getElementById('active-count').textContent = activeQueues.length;
            const activeList = document.getElementById('active-list');
            
            if (activeQueues.length === 0) {
                activeList.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-slate-300 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Antrian kosong
                    </div>`;
            } else {
                activeList.innerHTML = activeQueues.map(q => `
                    <div class="p-4 bg-slate-50 hover:bg-indigo-50/50 border border-slate-100 hover:border-indigo-100 rounded-2xl flex justify-between items-center transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white text-indigo-600 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm border border-slate-100 group-hover:border-indigo-200 transition-colors">
                                ${String(q.nomor_urut).padStart(3, '0')}
                            </div>
                            <div>
                                <div class="font-bold text-slate-700 capitalize">${q.nama_guest}</div>
                                <div class="text-xs text-slate-500 font-medium flex items-center mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 mr-1.5"></span>
                                    Menunggu
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // 3. Update Terlambat List
            const terlambatQueues = data.queues.filter(q => q.status === 'terlambat');
            document.getElementById('terlambat-count').textContent = terlambatQueues.length;
            const terlambatList = document.getElementById('terlambat-list');
            
            if (terlambatQueues.length === 0) {
                terlambatList.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-slate-300 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tidak ada data
                    </div>`;
            } else {
                terlambatList.innerHTML = terlambatQueues.map(q => `
                    <div class="p-4 bg-white border border-rose-100 rounded-2xl flex justify-between items-center transition-all hover:shadow-md hover:border-rose-200 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-extrabold text-lg border border-rose-100">
                                ${String(q.nomor_urut).padStart(3, '0')}
                            </div>
                            <div>
                                <div class="font-bold text-slate-700 capitalize">${q.nama_guest}</div>
                                <div class="text-xs text-rose-500 font-medium flex items-center mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                    Terlewat
                                </div>
                            </div>
                        </div>
                        <button onclick="panggilUlang('${q.id}')" title="Klik untuk memanggil ulang" class="bg-white border border-slate-200 hover:bg-violet-600 hover:text-white hover:border-violet-600 text-slate-600 w-10 h-10 rounded-xl shadow-sm transition-all flex items-center justify-center active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </button>
                    </div>
                `).join('');
            }
        }

        // --- Actions ---
        function postAction(url, bodyData = {}) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bodyData)
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) console.error("Action failed");
            })
            .catch(console.error);
        }

        function panggilBerikutnya() { postAction('/admin/panggil'); }
        function tandaiTerlambat(id) { postAction('/admin/skip', { id: id }); }
        function panggilUlang(id) { postAction('/admin/recall', { id: id }); }
        
        function resetAntrian() {
            if(confirm("PERINGATAN: Anda yakin ingin menghapus SEMUA data antrian hari ini?")) {
                postAction('/admin/reset');
            }
        }
    </script>
</body>
</html>
