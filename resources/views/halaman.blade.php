<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberChess AI v1.2</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #060913;
            --panel-dark: #0f1626;
            --accent-neon: #00f2fe;
            --accent-purple: #9d4edd;
            --text-light: #f1f5f9;
            --text-muted: #64748b;
            /* Palet warna papan catur cyber premium */
            --board-light: #1e293b;
            --board-dark: #0f172a;
            --border-glow: rgba(0, 242, 254, 0.25);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: radial-gradient(circle at center, #131c35 0%, var(--bg-dark) 100%);
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

        header { margin-bottom: 25px; text-align: center; }
        header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.6rem;
            font-weight: 700;
            letter-spacing: 3px;
            background: linear-gradient(45deg, var(--accent-purple), var(--accent-neon));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(0, 242, 254, 0.2);
        }
        header p { color: var(--text-muted); font-size: 0.95rem; margin-top: 8px; font-weight: 300; }

        .game-container {
            display: flex;
            gap: 40px;
            max-width: 1100px;
            width: 100%;
            justify-content: center;
            align-items: center;
        }

        /* MATRIKS PAPAN CATUR MODERN */
        .board-container {
            width: 500px;
            height: 500px;
            max-width: 90vw;
            max-height: 90vw;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.6), 0 0 25px var(--border-glow);
            border: 8px solid var(--panel-dark);
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(8, 1fr);
            position: relative;
        }

        .square {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            position: relative;
        }

        /* Warna khusus bidak agar terlihat menyala di kegelapan */
        .square span {
            z-index: 2;
            transition: transform 0.2s ease;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        }
        .square:hover span {
            transform: scale(1.12);
        }

        @media (max-width: 500px) {
            .square { font-size: 2rem; }
        }

        .light-sq { background-color: var(--board-light); }
        .dark-sq { background-color: var(--board-dark); }
        
        /* Efek Hover Petak */
        .square:not(.is-ai-turn):hover {
            background-color: rgba(0, 242, 254, 0.15) !important;
        }

        /* State Terpilih & Terancam */
        .selected-sq { 
            background-color: rgba(157, 78, 221, 0.4) !important;
            box-shadow: inset 0 0 15px rgba(157, 78, 221, 0.6);
        }
        
        /* Penanda Langkah Legal (Dot di tengah petak) */
        .legal-move-dot {
            width: 14px;
            height: 14px;
            background-color: rgba(0, 242, 254, 0.5);
            border-radius: 50%;
            position: absolute;
            z-index: 1;
            pointer-events: none;
        }
        .legal-move-capture {
            width: 80%;
            height: 80%;
            border: 3px solid rgba(255, 0, 85, 0.4);
            border-radius: 50%;
            position: absolute;
            z-index: 1;
            pointer-events: none;
        }

        .is-ai-turn { pointer-events: none; opacity: 0.8; }

        /* PANEL KONTROL */
        .panel {
            flex: 1;
            background: var(--panel-dark);
            border-radius: 16px;
            padding: 30px;
            min-width: 320px;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            gap: 24px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .status-box {
            background: rgba(0, 0, 0, 0.3);
            padding: 18px;
            border-radius: 10px;
            border-left: 4px solid var(--accent-neon);
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.2);
        }
        .status-box h3 { 
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem; 
            color: var(--text-muted); 
            margin-bottom: 6px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        .status-box p { font-size: 1.15rem; font-weight: 600; letter-spacing: 0.5px; }

        /* TOMBOL NEON */
        .btn-btn {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-neon));
            color: #fff;
            font-weight: 700;
            border: none;
            padding: 16px;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 242, 254, 0.2);
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .btn-btn:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 25px rgba(0, 242, 254, 0.4);
            filter: brightness(1.15);
        }
        .btn-btn:active { transform: translateY(-1px); }
        
        .btn-secondary { 
            background: rgba(255, 255, 255, 0.03); 
            color: var(--text-light); 
            border: 1px solid rgba(255,255,255,0.08); 
            box-shadow: none;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(255,255,255,0.05);
            color: #fff;
        }

        @media (max-width: 850px) {
            .game-container { flex-direction: column; gap: 25px; }
            .panel { width: 100%; max-width: 500px; padding: 20px; }
        }
    </style>
</head>
<body>

    <header>
        <h1>CYBER CHESS AI</h1>
        <p>Engine Catur Ringan Berbasis Unicode Matriks</p>
    </header>

    <div class="game-container">
        <div id="htmlBoard" class="board-container"></div>

        <div class="panel">
            <div class="status-box">
                <h3>Sistem Status</h3>
                <p id="status">Menginisialisasi kode...</p>
            </div>

            <button class="btn-btn" id="resetBtn">RESTART MATRIKS</button>
            <button class="btn-btn btn-secondary" id="undoBtn">BATALKAN LANGKAH</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.3/chess.min.js"></script>

    <script>
        // Peta Emoji Bidak Catur Premium
        const piecesEmoji = {
            'p': '♟', 'r': '♜', 'n': '♞', 'b': '♝', 'q': '♛', 'k': '♚',
            'P': '♙', 'R': '♖', 'N': '♘', 'B': '♗', 'Q': '♕', 'K': '♔'
        };

        const files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        const ranks = ['8', '7', '6', '5', '4', '3', '2', '1'];
        
        let game = new Chess();
        let selectedSquare = null;
        let stockfish = null;

        try {
            stockfish = new Worker('https://cdnjs.cloudflare.com/ajax/libs/stockfish.js/10.0.2/stockfish.js');
        } catch (e) {
            console.log("Stockfish dialihkan ke mode simulasi lokal.");
        }

        // Fungsi menggambar papan
        function drawBoard() {
            const boardDiv = document.getElementById('htmlBoard');
            boardDiv.innerHTML = '';
            
            if (game.turn() === 'b') {
                boardDiv.classList.add('is-ai-turn');
            } else {
                boardDiv.classList.remove('is-ai-turn');
            }

            // Ambil seluruh daftar gerakan legal jika ada bidak yang dipilih
            let legalMovesFromSelected = [];
            if (selectedSquare) {
                legalMovesFromSelected = game.moves({ square: selectedSquare, verbose: true });
            }

            for (let r = 0; r < 8; r++) {
                for (let f = 0; f < 8; f++) {
                    const squareName = files[f] + ranks[r];
                    const squareEl = document.createElement('div');
                    
                    const isLight = (r + f) % 2 === 0;
                    squareEl.className = `square ${isLight ? 'light-sq' : 'dark-sq'}`;
                    squareEl.dataset.square = squareName;

                    const piece = game.get(squareName);
                    if (piece) {
                        const key = piece.color === 'w' ? piece.type.toUpperCase() : piece.type.toLowerCase();
                        const pieceSpan = document.createElement('span');
                        pieceSpan.textContent = piecesEmoji[key];
                        
                        // Sentuhan Estetika: Warna neon untuk Putih, warna Cyber-Dark gelap solid untuk Hitam
                        if (piece.color === 'w') {
                            pieceSpan.style.color = '#00f2fe';
                            pieceSpan.style.textShadow = '0 0 10px rgba(0, 242, 254, 0.6)';
                        } else {
                            pieceSpan.style.color = '#020408';
                            pieceSpan.style.webkitTextStroke = '1.5px #ff0055'; // Outline merah neon/magenta untuk AI
                        }
                        squareEl.appendChild(pieceSpan);
                    }

                    // Highlight Petak Terpilih
                    if (selectedSquare === squareName) {
                        squareEl.classList.add('selected-sq');
                    }

                    // Beri penanda rute gerakan legal (UX Booster)
                    const isLegalMove = legalMovesFromSelected.find(m => m.to === squareName);
                    if (isLegalMove) {
                        const marker = document.createElement('div');
                        // Jika petak target kosong beri titik, jika ada musuh beri lingkaran target serang
                        marker.className = isLegalMove.captured ? 'legal-move-capture' : 'legal-move-dot';
                        squareEl.appendChild(marker);
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
                if (piece && piece.color === 'w') {
                    selectedSquare = square;
                    drawBoard();
                }
            } else {
                const move = game.move({
                    from: selectedSquare,
                    to: square,
                    promotion: 'q'
                });

                selectedSquare = null;
                drawBoard();

                if (move !== null) {
                    setTimeout(makeAiMove, 400);
                } else {
                    // Jika mengklik sesama bidak sendiri lagi, ganti fokus seleksi ke bidak baru tersebut
                    if (piece && piece.color === 'w') {
                        selectedSquare = square;
                        drawBoard();
                    }
                }
            }
        }

        function makeAiMove() {
            if (game.game_over()) return;

            document.getElementById('status').innerHTML = '⚡ AI sedang menganalisis kode...';

            if (stockfish) {
                stockfish.postMessage('position fen ' + game.fen());
                stockfish.postMessage('go depth 10');

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
            let statusText = game.turn() === 'w' ? 'Giliran Kamu (User)' : 'Menunggu respons AI...';

            if (game.in_checkmate()) {
                statusText = 'TERMINATED! ' + (game.turn() === 'w' ? 'AI Menang (Skakmat)' : 'Kamu Menang! (Skakmat)');
            } else if (game.in_draw()) {
                statusText = 'SYSTEM DRAW! Hasil Remis';
            } else if (game.in_check()) {
                statusText = game.turn() === 'w' ? '⚠️ Sinyal Bahaya - Kamu Di-Skak!' : '⚡ Bagus! AI Terbawa Skak!';
            }

            statusEl.textContent = statusText;
        }

        document.getElementById('resetBtn').addEventListener('click', () => {
            game.reset();
            selectedSquare = null;
            drawBoard();
        });

        document.getElementById('undoBtn').addEventListener('click', () => {
            game.undo();
            game.undo();
            selectedSquare = null;
            drawBoard();
        });

        // Eksekusi Papan Utama
        drawBoard();
    </script>
</body>
</html>