<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberChess AI v1.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #0a0e17;
            --panel-dark: #131a26;
            --accent-neon: #00f2fe;
            --accent-purple: #4facfe;
            --text-light: #e2e8f0;
            --text-muted: #64748b;
            --board-light: #737677;
            --board-dark: #3b597c;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: radial-gradient(circle at center, #1a233a 0%, var(--bg-dark) 100%);
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 20px;
        }

        header { margin-bottom: 20px; text-align: center; }
        header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            letter-spacing: 2px;
            background: linear-gradient(45deg, var(--accent-purple), var(--accent-neon));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        header p { color: var(--text-muted); font-size: 0.9rem; margin-top: 5px; }

        .game-container {
            display: flex;
            gap: 30px;
            max-width: 1100px;
            width: 100%;
            justify-content: center;
            align-items: flex-start;
        }

        /* MATRIKS PAPAN CATUR MURNI CSS */
        .board-container {
            width: 480px;
            height: 480px;
            max-width: 90vw;
            max-height: 90vw;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            border: 6px solid var(--panel-dark);
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(8, 1fr);
        }

        .square {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            cursor: pointer;
            user-select: none;
            transition: background 0.2s;
        }

        /* Ukuran font bidak di HP agar pas */
        @media (max-width: 500px) {
            .square { font-size: 1.8rem; }
        }

        .light-sq { background-color: var(--board-light); }
        .dark-sq { background-color: var(--board-dark); }
        
        .selected-sq { background-color: rgba(0, 242, 254, 0.5) !important; }
        .is-ai-turn { pointer-events: none; }

        .panel {
            flex: 1;
            background: var(--panel-dark);
            border-radius: 12px;
            padding: 25px;
            min-width: 320px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .status-box {
            background: rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--accent-neon);
        }
        .status-box h3 { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase; }
        .status-box p { font-size: 1.1rem; font-weight: 600; }

        .btn-btn {
            background: linear-gradient(45deg, var(--accent-purple), var(--accent-neon));
            color: #000;
            font-weight: 600;
            border: none;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            transition: all 0.3s ease;
        }
        .btn-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .btn-secondary { background: transparent; color: var(--text-light); border: 1px solid rgba(255,255,255,0.1); }

        @media (max-width: 850px) {
            .game-container { flex-direction: column; align-items: center; }
            .panel { width: 100%; }
        }
    </style>
</head>
<body>

    <header>
        <h1>CYBER CHESS AI</h1>
        <p>Lawan Engine Catur tanpa Loading Gambar Luar</p>
    </header>

    <div class="game-container">
        <!-- Papan Catur Buatan Sendiri -->
        <div id="htmlBoard" class="board-container"></div>

        <div class="panel">
            <div class="status-box">
                <h3>Status Game</h3>
                <p id="status">Giliran kamu (Putih)</p>
            </div>

            <button class="btn-btn" id="resetBtn">RESTART GAME</button>
            <button class="btn-btn btn-secondary" id="undoBtn">UNDO MOVE</button>
        </div>
    </div>

    <!-- Hanya butuh SATU library logika catur (Aman dari SRI/Bloking) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.3/chess.min.js"></script>

    <script>
        // Peta Unicode Emoji untuk Bidak Catur
        const piecesEmoji = {
            'p': '♟', 'r': '♜', 'n': '♞', 'b': '♝', 'q': '♛', 'k': '♚',
            'P': '♙', 'R': '♖', 'N': '♘', 'B': '♗', 'Q': '♕', 'K': '♔'
        };

        const files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        const ranks = ['8', '7', '6', '5', '4', '3', '2', '1'];
        
        let game = new Chess();
        let selectedSquare = null;
        let stockfish = null;

        // Coba muat Stockfish AI
        try {
            stockfish = new Worker('https://cdnjs.cloudflare.com/ajax/libs/stockfish.js/10.0.2/stockfish.js');
        } catch (e) {
            console.log("Stockfish AI dinonaktifkan browser, beralih ke bot simulasi.");
        }

        // Fungsi menggambar papan secara manual ke HTML
        function drawBoard() {
            const boardDiv = document.getElementById('htmlBoard');
            boardDiv.innerHTML = '';
            
            // Kunci papan kalau giliran AI
            if (game.turn() === 'b') {
                boardDiv.classList.add('is-ai-turn');
            } else {
                boardDiv.classList.remove('is-ai-turn');
            }

            for (let r = 0; r < 8; r++) {
                for (let f = 0; f < 8; f++) {
                    const squareName = files[f] + ranks[r];
                    const squareEl = document.createElement('div');
                    
                    // Warna petak selang-seling
                    const isLight = (r + f) % 2 === 0;
                    squareEl.className = `square ${isLight ? 'light-sq' : 'dark-sq'}`;
                    squareEl.dataset.square = squareName;

                    // Ambil info bidak dari chess.js
                    const piece = game.get(squareName);
                    if (piece) {
                        // Cari simbol emojinya (Huruf besar = Putih, Huruf kecil = Hitam)
                        const key = piece.color === 'w' ? piece.type.toUpperCase() : piece.type.toLowerCase();
                        squareEl.textContent = piecesEmoji[key];
                        // Beri warna teks khusus bidak hitam agar kontras
                        if (piece.color === 'b') squareEl.style.color = '#000000';
                    }

                    if (selectedSquare === squareName) {
                        squareEl.classList.add('selected-sq');
                    }

                    squareEl.addEventListener('click', () => handleSquareClick(squareName));
                    boardDiv.appendChild(squareEl);
                }
            }
            updateStatus();
        }

        function handleSquareClick(square) {
            const piece = game.get(square);

            if (selectedSquare === null) {
                // Klik pertama: pilih bidak putih kamu
                if (piece && piece.color === 'w') {
                    selectedSquare = square;
                    drawBoard();
                }
            } else {
                // Klik kedua: Eksekusi langkah
                const move = game.move({
                    from: selectedSquare,
                    to: square,
                    promotion: 'q' // otomatis ratu jika sampai ujung
                });

                selectedSquare = null;
                drawBoard();

                if (move !== null) {
                    // Beri jeda sebentar sebelum AI membalas
                    setTimeout(makeAiMove, 400);
                }
            }
        }

        function makeAiMove() {
            if (game.game_over()) return;

            document.getElementById('status').innerHTML = 'Stockfish sedang berpikir...';

            if (stockfish) {
                stockfish.postMessage('position fen ' + game.fen());
                stockfish.postMessage('go depth 10'); // Level menengah cepat

                stockfish.onmessage = function(event) {
                    const line = event.data;
                    if (line.indexOf('bestmove') > -1) {
                        const moveStr = line.split(' ')[1];
                        game.move({
                            from: moveStr.substring(0, 2),
                            to: moveStr.substring(2, 4),
                            promotion: moveStr.substring(4, 5) || undefined
                        });
                        drawBoard();
                    }
                };
            } else {
                // Bot Cadangan kalau stockfish diblokir browser
                const possibleMoves = game.moves();
                if (possibleMoves.length > 0) {
                    const randomMove = possibleMoves[Math.floor(Math.random() * possibleMoves.length)];
                    game.move(randomMove);
                    drawBoard();
                }
            }
        }

        function updateStatus() {
            const statusEl = document.getElementById('status');
            let statusText = game.turn() === 'w' ? 'Giliran kamu (Putih)' : 'AI sedang melangkah...';

            if (game.in_checkmate()) {
                statusText = 'Game Over! ' + (game.turn() === 'w' ? 'AI Menang (Skakmat)' : 'Kamu Menang! (Skakmat)');
            } else if (game.in_draw()) {
                statusText = 'Game Over! Hasil Remis (Draw)';
            } else if (game.in_check()) {
                statusText += ' - Sedang Di-Skak!';
            }

            statusEl.textContent = statusText;
        }

        // Tombol-tombol kontrol
        document.getElementById('resetBtn').addEventListener('click', () => {
            game.reset();
            selectedSquare = null;
            drawBoard();
        });

        document.getElementById('undoBtn').addEventListener('click', () => {
            game.undo(); // Undo langkah AI
            game.undo(); // Undo langkah Kamu
            selectedSquare = null;
            drawBoard();
        });

        // Gambar papan pertama kali saat website dibuka
        drawBoard();
    </script>
</body>
</html><?php /**PATH C:\laragon\www\latihan-database2\resources\views/halaman.blade.php ENDPATH**/ ?>