/**
 * WP Wordle - Frontend Game Controller
 */

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('wp-wordle-app');
    if (!container || !window.wpWordleConfig) return;

    const config = window.wpWordleConfig;

    let mode = config.mode || 'daily';
    let lang = config.lang || 'it';
    let token = null;
    let currentRow = 0;
    let currentTile = 0;
    let currentGuess = '';
    let isGameOver = false;
    let isSubmitting = false;
    let evaluationsHistory = [];

    const board = document.getElementById('wp-wordle-board');
    const keyboard = document.getElementById('wp-wordle-keyboard');
    const toastContainer = document.getElementById('wp-wordle-toast');
    const modalHelp = document.getElementById('wp-wordle-modal-help');
    const modalStats = document.getElementById('wp-wordle-modal-stats');
    const btnHelp = document.getElementById('wp-wordle-help-btn');
    const btnStats = document.getElementById('wp-wordle-stats-btn');
    const btnShare = document.getElementById('wp-wordle-btn-share');
    const btnNewGame = document.getElementById('wp-wordle-btn-new');
    const modeButtons = document.querySelectorAll('.wp-wordle-mode-btn');

    function getStats() {
        const defaultStats = { played: 0, wins: 0, currentStreak: 0, maxStreak: 0 };
        try {
            return JSON.parse(localStorage.getItem(`wp_wordle_stats_${lang}`)) || defaultStats;
        } catch (e) {
            return defaultStats;
        }
    }

    function saveStats(won) {
        const stats = getStats();
        stats.played++;
        if (won) {
            stats.wins++;
            stats.currentStreak++;
            if (stats.currentStreak > stats.maxStreak) stats.maxStreak = stats.currentStreak;
        } else {
            stats.currentStreak = 0;
        }
        localStorage.setItem(`wp_wordle_stats_${lang}`, JSON.stringify(stats));
        updateStatsModal(stats);
    }

    function updateStatsModal(stats) {
        document.getElementById('wp-wordle-stat-played').textContent = stats.played;
        const winrate = stats.played > 0 ? Math.round((stats.wins / stats.played) * 100) : 0;
        document.getElementById('wp-wordle-stat-winrate').textContent = `${winrate}%`;
        document.getElementById('wp-wordle-stat-streak').textContent = stats.currentStreak;
        document.getElementById('wp-wordle-stat-maxstreak').textContent = stats.maxStreak;
    }

    function showToast(message, duration = 2200) {
        const toast = document.createElement('div');
        toast.className = 'wp-wordle-toast';
        toast.textContent = message;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), duration);
    }

    async function startNewGame(newMode = mode) {
        mode = newMode;
        currentRow = 0;
        currentTile = 0;
        currentGuess = '';
        isGameOver = false;
        isSubmitting = false;
        evaluationsHistory = [];
        resetBoardUI();

        modeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });

        btnNewGame.style.display = mode === 'practice' ? 'inline-block' : 'none';

        try {
            const response = await fetch(`${config.apiUrl}start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': config.nonce
                },
                body: JSON.stringify({ mode, lang })
            });
            const data = await response.json();
            if (data.token) {
                token = data.token;
            }
        } catch (err) {
            console.error('Errore avvio partita:', err);
        }
    }

    function resetBoardUI() {
        const tiles = board.querySelectorAll('.wp-wordle-tile');
        tiles.forEach(tile => {
            tile.textContent = '';
            tile.className = 'wp-wordle-tile';
            tile.removeAttribute('data-state');
            tile.style.animationDelay = '';
        });
        const keys = keyboard.querySelectorAll('.wp-wordle-key');
        keys.forEach(k => {
            k.className = k.dataset.key.length > 1 ? 'wp-wordle-key wp-wordle-key-action' : 'wp-wordle-key';
        });

        document.getElementById('wp-wordle-solution-container').style.display = 'none';
        btnShare.style.display = 'none';
    }

    function handleKeyPress(key) {
        if (isGameOver || isSubmitting) return;

        key = key.toUpperCase();

        if (key === 'ENTER') {
            submitGuess();
        } else if (key === 'BACKSPACE' || key === '⌫') {
            removeLetter();
        } else if (/^[A-Z]$/.test(key)) {
            addLetter(key);
        }
    }

    function addLetter(letter) {
        if (currentTile >= 5) return;
        const row = board.querySelector(`.wp-wordle-row[data-row="${currentRow}"]`);
        const tile = row.querySelector(`.wp-wordle-tile[data-col="${currentTile}"]`);
        tile.textContent = letter;
        tile.setAttribute('data-state', 'active');
        currentGuess += letter;
        currentTile++;
    }

    function removeLetter() {
        if (currentTile <= 0) return;
        currentTile--;
        currentGuess = currentGuess.slice(0, -1);
        const row = board.querySelector(`.wp-wordle-row[data-row="${currentRow}"]`);
        const tile = row.querySelector(`.wp-wordle-tile[data-col="${currentTile}"]`);
        tile.textContent = '';
        tile.removeAttribute('data-state');
    }

    async function submitGuess() {
        if (currentGuess.length < 5) {
            shakeRow(currentRow);
            showToast(config.i18n.notEnoughLetters);
            return;
        }

        isSubmitting = true;

        try {
            const response = await fetch(`${config.apiUrl}guess`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': config.nonce
                },
                body: JSON.stringify({
                    mode,
                    lang,
                    guess: currentGuess,
                    token,
                    attempt: currentRow + 1
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                shakeRow(currentRow);
                showToast(data.message || config.i18n.invalidWord);
                isSubmitting = false;
                return;
            }

            evaluationsHistory.push(data.evaluation);
            animateRowReveal(currentRow, data.evaluation, () => {
                if (data.is_win) {
                    bounceWinRow(currentRow);
                    isGameOver = true;
                    saveStats(true);
                    setTimeout(() => {
                        showToast(config.i18n.winTitle, 3000);
                        btnShare.style.display = 'inline-block';
                        openModal(modalStats);
                    }, 1200);
                } else if (data.is_game_over) {
                    isGameOver = true;
                    saveStats(false);
                    document.getElementById('wp-wordle-solution-container').style.display = 'block';
                    document.getElementById('wp-wordle-solution-text').textContent = data.solution;
                    btnShare.style.display = 'inline-block';
                    setTimeout(() => openModal(modalStats), 1500);
                } else {
                    currentRow++;
                    currentTile = 0;
                    currentGuess = '';
                }
                isSubmitting = false;
            });

        } catch (err) {
            console.error('Errore invio tentativo:', err);
            showToast('Errore di connessione');
            isSubmitting = false;
        }
    }

    function animateRowReveal(rowIndex, evaluation, onComplete) {
        const row = board.querySelector(`.wp-wordle-row[data-row="${rowIndex}"]`);
        const tiles = row.querySelectorAll('.wp-wordle-tile');

        tiles.forEach((tile, colIndex) => {
            const delay = colIndex * 250;

            setTimeout(() => {
                tile.classList.add('flip');
                setTimeout(() => {
                    const status = evaluation[colIndex].status;
                    tile.classList.add(status);
                    updateKeyboardKey(evaluation[colIndex].letter, status);
                }, 250);

            }, delay);
        });

        setTimeout(onComplete, 1550);
    }

    function updateKeyboardKey(letter, status) {
        const key = keyboard.querySelector(`.wp-wordle-key[data-key="${letter}"]`);
        if (!key) return;

        if (key.classList.contains('correct')) return;
        if (key.classList.contains('present') && status === 'absent') return;

        key.classList.remove('present', 'absent');
        key.classList.add(status);
    }

    function shakeRow(rowIndex) {
        const row = board.querySelector(`.wp-wordle-row[data-row="${rowIndex}"]`);
        row.classList.add('shake');
        setTimeout(() => row.classList.remove('shake'), 450);
    }

    function bounceWinRow(rowIndex) {
        const row = board.querySelector(`.wp-wordle-row[data-row="${rowIndex}"]`);
        const tiles = row.querySelectorAll('.wp-wordle-tile');
        tiles.forEach((t) => t.classList.add('win-bounce'));
    }

    btnShare.addEventListener('click', () => {
        const attempts = isGameOver && evaluationsHistory.length;
        const total = isGameOver ? (evaluationsHistory[evaluationsHistory.length - 1].every(e => e.status === 'correct') ? attempts : 'X') : attempts;
        let shareText = `Wordle (${lang.toUpperCase()}) ${total}/6\n\n`;

        evaluationsHistory.forEach(rowEval => {
            rowEval.forEach(tile => {
                if (tile.status === 'correct') shareText += '🟩';
                else if (tile.status === 'present') shareText += '🟨';
                else shareText += '⬛';
            });
            shareText += '\n';
        });

        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareText).then(() => {
                showToast(config.i18n.copied);
            });
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        handleKeyPress(e.key);
    });

    keyboard.addEventListener('click', (e) => {
        const target = e.target.closest('.wp-wordle-key');
        if (!target) return;
        handleKeyPress(target.dataset.key);
    });

    function openModal(modal) { modal.style.display = 'flex'; }
    function closeModal(modal) { modal.style.display = 'none'; }

    btnHelp.addEventListener('click', () => openModal(modalHelp));
    btnStats.addEventListener('click', () => {
        updateStatsModal(getStats());
        openModal(modalStats);
    });

    document.querySelectorAll('.wp-wordle-modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modal = e.target.closest('.wp-wordle-modal-backdrop');
            closeModal(modal);
        });
    });

    modeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (isSubmitting) return;
            startNewGame(btn.dataset.mode);
        });
    });

    btnNewGame.addEventListener('click', () => {
        closeModal(modalStats);
        startNewGame('practice');
    });

    updateStatsModal(getStats());
    startNewGame(mode);
});