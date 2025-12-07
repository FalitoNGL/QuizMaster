<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        .correct { background-color: #10b981 !important; border-color: #059669 !important; color: white !important; }
        .wrong { background-color: #ef4444 !important; border-color: #dc2626 !important; color: white !important; opacity: 0.9; }
        .selected { border: 2px solid #3b82f6 !important; background-color: rgba(59, 130, 246, 0.2) !important; }
        .correct-indicator { border: 2px solid #10b981 !important; position: relative; }
        .correct-indicator::after { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; right: 15px; color: #10b981; }
        .disabled-opt { pointer-events: none; opacity: 0.8; }
        button:disabled { cursor: not-allowed; opacity: 0.5; filter: grayscale(100%); }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-up { animation: fadeUp 0.5s ease-out; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen flex items-center justify-center font-sans p-4 transition-colors duration-300">
    
    <div class="w-full max-w-3xl relative z-10">
        
        <div class="flex justify-between items-center mb-6 glass p-4 rounded-full shadow-lg bg-white/80 dark:bg-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center border border-slate-300 dark:border-slate-500">
                    @if($timer > 0) <i class="fas fa-clock text-yellow-500 dark:text-yellow-400"></i> @else <i class="fas fa-mug-hot text-green-500"></i> @endif
                </div>
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">WAKTU</div>
                    <div id="timer-display" class="font-mono text-xl font-bold text-slate-700 dark:text-white">{{ $timer > 0 ? $timer : '∞' }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs text-slate-500 dark:text-slate-400">SKOR</div>
                <div id="score-display" class="font-mono text-xl font-bold text-blue-600 dark:text-blue-400">0</div>
            </div>
        </div>

        <div id="quiz-card" class="glass rounded-3xl p-8 shadow-2xl relative overflow-hidden bg-white/80 dark:bg-slate-800/90">
            <div class="mb-6">
                <span class="inline-block px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-300 text-xs font-bold border border-blue-200 dark:border-blue-500/30 mb-4">
                    SOAL <span id="q-number">1</span> / {{ $questions->count() }}
                </span>
                
                <div id="media-container" class="hidden mb-4">
                    <img id="q-image" class="w-full h-48 object-cover rounded-xl border border-slate-300 dark:border-slate-600 mb-2 hidden shadow-md">
                    <audio id="q-audio" controls class="w-full hidden"></audio>
                </div>

                <h2 id="question-text" class="text-xl md:text-2xl font-bold leading-relaxed text-slate-800 dark:text-white">Loading...</h2>
                <p id="instruction-text" class="text-sm text-slate-500 dark:text-slate-400 mt-2 italic border-l-4 border-yellow-500 pl-3"></p>
            </div>

            <div id="options-container" class="grid gap-3"></div>

            <button id="btn-confirm" onclick="submitComplexAnswer()" disabled class="hidden w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition shadow-lg transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                JAWAB SEKARANG <i class="fas fa-check ml-2"></i>
            </button>

            <div id="feedback-area" class="hidden mt-6 p-5 bg-slate-100 dark:bg-slate-900 rounded-xl border-l-4 border-yellow-500 animate-fade-up shadow-inner">
                <h4 class="font-bold text-yellow-600 dark:text-yellow-400 mb-2 flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i> Pembahasan
                </h4>
                <p id="explanation-text" class="text-sm text-slate-600 dark:text-slate-300 mb-3 leading-relaxed"></p>
                <div class="text-xs text-slate-500 italic border-t border-slate-300 dark:border-slate-700 pt-2" id="reference-text"></div>
                <button onclick="nextQuestion()" class="w-full mt-4 bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-lg font-bold transition shadow-lg flex items-center justify-center gap-2">
                    Lanjut Soal Berikutnya <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <div id="result-card" class="hidden glass rounded-3xl p-10 text-center shadow-2xl bg-white/95 dark:bg-slate-800">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-3xl font-bold mb-2 text-slate-800 dark:text-white">Selesai!</h2>
            <div class="text-5xl font-bold text-blue-600 dark:text-blue-500 mb-8" id="final-score">0</div>
            <div id="save-section">
                <input type="text" id="player-name" placeholder="Ketik Nama Kamu" class="w-full bg-slate-100 dark:bg-slate-700 px-4 py-3 rounded-xl mb-4 text-center text-slate-800 dark:text-white border border-slate-300 dark:border-slate-600 focus:border-blue-500 outline-none">
                <button onclick="saveScore()" id="btn-save" class="w-full bg-green-600 hover:bg-green-500 py-3 rounded-xl font-bold text-white shadow-lg mb-3 transition">Simpan Skor</button>
            </div>
            <div id="after-save-actions" class="hidden flex flex-col gap-3">
                <a href="{{ route('menu') }}" class="w-full bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-white py-3 rounded-xl font-bold transition text-center">Kembali ke Menu</a>
            </div>
        </div>
    </div>

    <audio id="sfx-correct"><source src="https://assets.mixkit.co/active_storage/sfx/2000/2000-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-wrong"><source src="https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-finish"><source src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" type="audio/mpeg"></audio>

    <script>
        const htmlTag = document.documentElement;
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlTag.classList.add('dark');
        }
        function toggleTheme() {
            if (htmlTag.classList.contains('dark')) {
                htmlTag.classList.remove('dark'); localStorage.setItem('theme', 'light');
            } else {
                htmlTag.classList.add('dark'); localStorage.setItem('theme', 'dark');
            }
        }

        const questions = @json($questions);
        const configTimer = {{ $timer }};
        let currentIdx = 0;
        let score = 0;
        let timeLeft = configTimer;
        let timerInterval;
        let isAnswered = false;

        const sfxCorrect = document.getElementById('sfx-correct');
        const sfxWrong = document.getElementById('sfx-wrong');
        const sfxFinish = document.getElementById('sfx-finish');
        const isSfxOn = localStorage.getItem('qm_sfx') !== 'false';
        function playSound(el) { if (isSfxOn) { el.currentTime = 0; el.play().catch(()=>{}); } }

        if(questions.length > 0) loadQuestion();
        else document.getElementById('question-text').innerText = "Tidak ada soal.";

        function loadQuestion() {
            if (currentIdx >= questions.length) { finishQuiz(); return; }
            const q = questions[currentIdx];
            isAnswered = false;
            
            document.getElementById('question-text').innerText = q.question_text;
            document.getElementById('q-number').innerText = currentIdx + 1;
            document.getElementById('feedback-area').classList.add('hidden');
            document.getElementById('options-container').classList.remove('disabled-opt');
            document.getElementById('instruction-text').innerText = "";

            // Reset Media
            const imgEl = document.getElementById('q-image');
            const audioEl = document.getElementById('q-audio');
            const mediaCont = document.getElementById('media-container');
            imgEl.classList.add('hidden'); audioEl.classList.add('hidden'); mediaCont.classList.add('hidden');
            if(q.image_path) { imgEl.src = "/storage/" + q.image_path; imgEl.classList.remove('hidden'); mediaCont.classList.remove('hidden'); }
            if(q.audio_path) { audioEl.src = "/storage/" + q.audio_path; audioEl.classList.remove('hidden'); mediaCont.classList.remove('hidden'); }

            // Timer
            if (configTimer > 0) {
                timeLeft = configTimer;
                document.getElementById('timer-display').innerText = timeLeft;
                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    timeLeft--;
                    document.getElementById('timer-display').innerText = timeLeft;
                    if (timeLeft <= 0) { clearInterval(timerInterval); handleTimeUp(); }
                }, 1000);
            } else {
                document.getElementById('timer-display').innerText = "∞";
                clearInterval(timerInterval);
            }

            const container = document.getElementById('options-container');
            container.innerHTML = '';
            const btnConfirm = document.getElementById('btn-confirm');
            btnConfirm.classList.add('hidden');
            btnConfirm.disabled = true;
            
            const options = [...q.options];

            // --- A. SINGLE CHOICE ---
            if (q.type === 'single') {
                document.getElementById('instruction-text').innerText = "Pilih satu jawaban yang benar.";
                options.sort(() => Math.random() - 0.5).forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left p-4 rounded-xl bg-white dark:bg-slate-700 hover:bg-blue-50 dark:hover:bg-slate-600 border-2 border-transparent transition font-medium text-lg flex justify-between items-center group option-btn text-slate-800 dark:text-white shadow-sm';
                    btn.dataset.correct = opt.is_correct;
                    btn.innerHTML = `<span>${opt.option_text}</span>`;
                    btn.onclick = () => submitSingle(opt, btn);
                    container.appendChild(btn);
                });
            }
            
            // --- B. MULTIPLE CHOICE ---
            else if (q.type === 'multiple') {
                document.getElementById('instruction-text').innerText = "Pilih SEMUA jawaban benar, lalu klik 'JAWAB SEKARANG'.";
                btnConfirm.classList.remove('hidden');
                
                options.sort(() => Math.random() - 0.5).forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'option-item w-full text-left p-4 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800/50 hover:bg-blue-50 dark:hover:bg-slate-700 transition mb-2 cursor-pointer flex items-center gap-3 text-gray-800 dark:text-gray-200 shadow-sm';
                    div.innerHTML = `<div class="w-6 h-6 border-2 border-gray-400 dark:border-slate-400 rounded flex items-center justify-center"><i class="fas fa-check text-transparent transition check-icon"></i></div> <span>${opt.option_text}</span>`;
                    div.dataset.id = opt.id; div.dataset.correct = opt.is_correct;
                    div.onclick = () => {
                        div.classList.toggle('selected');
                        div.querySelector('.check-icon').classList.toggle('text-transparent');
                        div.querySelector('.check-icon').classList.toggle('text-blue-500');
                        validateComplexAnswer();
                    };
                    container.appendChild(div);
                });
            }

            // --- C. ORDERING ---
            else if (q.type === 'ordering') {
                document.getElementById('instruction-text').innerText = "Urutkan jawaban dengan panah ▲ ▼, lalu klik 'JAWAB SEKARANG'.";
                btnConfirm.classList.remove('hidden');
                // Ordering bisa langsung submit kapan saja karena item sudah ada semua,
                // tapi kita disable dulu biar user 'sadar' harus cek urutan.
                // Atau enable langsung: btnConfirm.disabled = false;
                // Disini saya enable langsung karena urutan awal pun sebuah jawaban.
                btnConfirm.disabled = false;

                options.sort(() => Math.random() - 0.5).forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'ordering-item w-full p-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800/50 mb-2 flex justify-between items-center text-gray-800 dark:text-gray-200 shadow-sm';
                    div.dataset.id = opt.id; div.dataset.order = opt.correct_order;
                    div.innerHTML = `<span>${opt.option_text}</span><div class="flex flex-col gap-1"><button onclick="moveItem(this, -1)" class="p-1 bg-gray-200 dark:bg-slate-700 rounded hover:bg-gray-300 dark:hover:bg-slate-600 text-xs">▲</button><button onclick="moveItem(this, 1)" class="p-1 bg-gray-200 dark:bg-slate-700 rounded hover:bg-gray-300 dark:hover:bg-slate-600 text-xs">▼</button></div>`;
                    container.appendChild(div);
                });
            }

            // --- D. MATCHING ---
            else if (q.type === 'matching') {
                document.getElementById('instruction-text').innerText = "Pasangkan sisi kiri dengan kanan, lalu klik 'JAWAB SEKARANG'.";
                btnConfirm.classList.remove('hidden');

                const rightOptions = options.map(o => ({id: o.id, text: o.matching_pair})).sort(() => Math.random() - 0.5);
                options.forEach(opt => {
                    const row = document.createElement('div');
                    row.className = 'p-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800/50 mb-2 shadow-sm text-gray-800 dark:text-gray-200';
                    let selectHtml = `<select class="matching-select w-full mt-2 bg-gray-100 dark:bg-slate-700 p-2 rounded border border-gray-300 dark:border-slate-500 focus:outline-none text-slate-800 dark:text-white" data-left-id="${opt.id}" data-correct-pair="${opt.matching_pair}" onchange="validateComplexAnswer()"><option value="">-- Pilih --</option>`;
                    rightOptions.forEach(ro => { selectHtml += `<option value="${ro.text}">${ro.text}</option>`; });
                    selectHtml += `</select>`;
                    row.innerHTML = `<div class="font-bold mb-1 border-b border-gray-200 dark:border-gray-700 pb-1">${opt.option_text}</div> ${selectHtml}`;
                    container.appendChild(row);
                });
            }
        }

        // --- VALIDASI TOMBOL JAWAB ---
        function validateComplexAnswer() {
            const q = questions[currentIdx];
            const btn = document.getElementById('btn-confirm');
            let isValid = false;

            if (q.type === 'multiple') {
                // Minimal pilih 1
                if (document.querySelectorAll('.option-item.selected').length > 0) isValid = true;
            } else if (q.type === 'matching') {
                // Semua dropdown harus dipilih (value != "")
                const selects = document.querySelectorAll('.matching-select');
                isValid = Array.from(selects).every(sel => sel.value !== "");
            } else if (q.type === 'ordering') {
                isValid = true;
            }
            btn.disabled = !isValid;
        }

        function moveItem(btn, direction) {
            const item = btn.closest('.ordering-item');
            const parent = item.parentNode;
            if (direction === -1 && item.previousElementSibling) parent.insertBefore(item, item.previousElementSibling);
            if (direction === 1 && item.nextElementSibling) parent.insertBefore(item.nextElementSibling, item);
        }

        // --- SUBMIT ---
        function submitSingle(opt, btn) {
            if(isAnswered) return;
            isAnswered = true;
            clearInterval(timerInterval);
            
            const isCorrect = opt.is_correct == 1;
            if (isCorrect) { btn.classList.add('correct'); score += 100 + (configTimer > 0 ? timeLeft : 0); playSound(sfxCorrect); }
            else { btn.classList.add('wrong'); playSound(sfxWrong); document.querySelectorAll('.option-btn').forEach(b => { if(b.dataset.correct == "1") b.classList.add('correct-indicator'); }); }
            
            finishTurn();
        }

        function submitComplexAnswer() {
            if(isAnswered) return;
            isAnswered = true;
            clearInterval(timerInterval);

            const q = questions[currentIdx];
            let isCorrect = false;

            if (q.type === 'multiple') {
                const selected = document.querySelectorAll('.option-item.selected');
                const selectedIds = Array.from(selected).map(d => parseInt(d.dataset.id));
                const correctIds = q.options.filter(o => o.is_correct).map(o => o.id);
                if (selectedIds.length === correctIds.length && selectedIds.every(id => correctIds.includes(id))) isCorrect = true;

                document.querySelectorAll('.option-item').forEach(div => {
                    const id = parseInt(div.dataset.id);
                    const isKey = correctIds.includes(id);
                    const isSelected = selectedIds.includes(id);
                    if(isKey) div.classList.add('correct-indicator');
                    if(isSelected && !isKey) div.classList.add('wrong');
                    if(isSelected && isKey) div.classList.add('correct');
                });
            } else if (q.type === 'ordering') {
                const items = document.querySelectorAll('.ordering-item');
                const userOrder = Array.from(items).map(d => parseInt(d.dataset.id));
                const correctOrder = [...q.options].sort((a,b) => a.correct_order - b.correct_order).map(o => o.id);
                if (JSON.stringify(userOrder) === JSON.stringify(correctOrder)) {
                    isCorrect = true;
                    items.forEach(div => div.classList.add('correct'));
                } else {
                    items.forEach(div => div.classList.add('wrong'));
                }
            } else if (q.type === 'matching') {
                const selects = document.querySelectorAll('.matching-select');
                let allCorrect = true;
                selects.forEach(sel => {
                    if (sel.value === sel.dataset.correctPair) {
                        sel.parentElement.classList.add('correct');
                    } else {
                        sel.parentElement.classList.add('wrong');
                        allCorrect = false;
                        const hint = document.createElement('span');
                        hint.className = "text-green-500 font-bold text-xs ml-2";
                        hint.innerText = "✔ " + sel.dataset.correctPair;
                        sel.parentElement.appendChild(hint);
                    }
                });
                if (allCorrect) isCorrect = true;
            }

            if (isCorrect) { score += 150 + (configTimer > 0 ? timeLeft : 0); playSound(sfxCorrect); }
            else { playSound(sfxWrong); }
            
            finishTurn();
        }

        function finishTurn() {
            document.getElementById('score-display').innerText = score;
            document.getElementById('options-container').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');

            const q = questions[currentIdx];
            document.getElementById('explanation-text').innerText = q.explanation || "Tidak ada pembahasan.";
            document.getElementById('reference-text').innerText = q.reference ? "📚 Sumber: " + q.reference : "";
            document.getElementById('feedback-area').classList.remove('hidden');
            document.getElementById('feedback-area').scrollIntoView({ behavior: 'smooth' });
        }

        function handleTimeUp() {
            isAnswered = true;
            playSound(sfxWrong);
            document.getElementById('options-container').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');
            const q = questions[currentIdx];
            document.getElementById('explanation-text').innerText = "Waktu Habis!";
            document.getElementById('feedback-area').classList.remove('hidden');
        }

        function nextQuestion() { currentIdx++; loadQuestion(); }
        function finishQuiz() { document.getElementById('quiz-card').classList.add('hidden'); document.getElementById('result-card').classList.remove('hidden'); document.getElementById('final-score').innerText = score; playSound(sfxFinish); }
        function saveScore() {
            const name = document.getElementById('player-name').value;
            if(!name) { alert("Isi nama dulu!"); return; }
            fetch("{{ route('quiz.submit') }}", {
                method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ category_id: {{ $category->id }}, player_name: name, score: score, correct: 0, total: questions.length, answers: [] })
            }).then(res => res.json()).then(data => {
                document.getElementById('save-section').classList.add('hidden');
                document.getElementById('after-save-actions').classList.remove('hidden');
                document.getElementById('btn-review-link').href = "/review/" + data.result_id;
            });
        }
        function shareToWa() { window.open(`https://wa.me/?text=Skor saya: ${score}`, '_blank'); }
    </script>
</body>
</html>