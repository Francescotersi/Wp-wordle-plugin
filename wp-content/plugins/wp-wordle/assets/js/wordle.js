/**
 * WP Wordle - Frontend Game Controller (NYT Style)
 */

(function () {
    function initWordle() {
        const container = document.getElementById('wp-wordle-app');
        if (!container) return;

        if (container.dataset.initialized === 'true') return;
        container.dataset.initialized = 'true';

        const config = window.wpWordleConfig || {
            apiUrl: container.dataset.api || '/wp-json/wp-wordle/v1/',
            nonce: container.dataset.nonce || '',
            mode: container.dataset.mode || 'daily',
            lang: container.dataset.lang || 'it',
            i18n: {
                winTitle: 'Splendid!',
                lossTitle: 'Game Over',
                solutionLabel: 'The word was:',
                copied: 'Copied to clipboard',
                notEnoughLetters: 'Not enough letters',
                invalidWord: 'Not in word list',
                share: 'Share',
                newGame: 'Play Again',
            }
        };

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
        const modeButtons = container.querySelectorAll('.wp-wordle-mode-btn');

        if (!board || !keyboard) return;

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
            const elPlayed = document.getElementById('wp-wordle-stat-played');
            const elWinrate = document.getElementById('wp-wordle-stat-winrate');
            const elStreak = document.getElementById('wp-wordle-stat-streak');
            const elMaxStreak = document.getElementById('wp-wordle-stat-maxstreak');

            if (elPlayed) elPlayed.textContent = stats.played;
            if (elWinrate) {
                const winrate = stats.played > 0 ? Math.round((stats.wins / stats.played) * 100) : 0;
                elWinrate.textContent = `${winrate}%`;
            }
            if (elStreak) elStreak.textContent = stats.currentStreak;
            if (elMaxStreak) elMaxStreak.textContent = stats.maxStreak;
        }

        function showToast(message, duration = 2200) {
            if (!toastContainer) return;
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

            if (btnNewGame) {
                btnNewGame.style.display = mode === 'practice' ? 'inline-block' : 'none';
            }

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
                if (data && data.token) {
                    token = data.token;
                }
            } catch (err) {}
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
                const keyAttr = k.getAttribute('data-key') || '';
                k.className = keyAttr.length > 1 ? 'wp-wordle-key wp-wordle-key-action' : 'wp-wordle-key';
            });

            const solCont = document.getElementById('wp-wordle-solution-container');
            if (solCont) solCont.style.display = 'none';
            if (btnShare) btnShare.style.display = 'none';
        }

        function handleKeyPress(key) {
            if (isGameOver || isSubmitting) return;

            key = (key || '').toUpperCase();

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
            if (!row) return;
            const tile = row.querySelector(`.wp-wordle-tile[data-col="${currentTile}"]`);
            if (!tile) return;

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
            if (!row) return;
            const tile = row.querySelector(`.wp-wordle-tile[data-col="${currentTile}"]`);
            if (!tile) return;

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
                            if (btnShare) btnShare.style.display = 'inline-block';
                            if (modalStats) openModal(modalStats);
                        }, 1200);
                    } else if (data.is_game_over) {
                        isGameOver = true;
                        saveStats(false);
                        const solCont = document.getElementById('wp-wordle-solution-container');
                        const solText = document.getElementById('wp-wordle-solution-text');
                        if (solCont) solCont.style.display = 'block';
                        if (solText) solText.textContent = data.solution;
                        if (btnShare) btnShare.style.display = 'inline-block';
                        setTimeout(() => {
                            if (modalStats) openModal(modalStats);
                        }, 1500);
                    } else {
                        currentRow++;
                        currentTile = 0;
                        currentGuess = '';
                    }
                    isSubmitting = false;
                });

            } catch (err) {
                showToast('Connection error');
                isSubmitting = false;
            }
        }

        function animateRowReveal(rowIndex, evaluation, onComplete) {
            const row = board.querySelector(`.wp-wordle-row[data-row="${rowIndex}"]`);
            if (!row) {
                if (onComplete) onComplete();
                return;
            }

            const tiles = row.querySelectorAll('.wp-wordle-tile');

            tiles.forEach((tile, colIndex) => {
                const delay = colIndex * 300;

                setTimeout(() => {
                    tile.classList.add('flip-in');

                    setTimeout(() => {
                        tile.classList.remove('flip-in');
                        const status = evaluation[colIndex].status;
                        tile.classList.add(status, 'flip-out');
                        updateKeyboardKey(evaluation[colIndex].letter, status);

                        setTimeout(() => {
                            tile.classList.remove('flip-out');
                        }, 250);
                    }, 250);

                }, delay);
            });

            setTimeout(onComplete, 5 * 300 + 300);
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
            if (!row) return;
            row.classList.add('shake');
            setTimeout(() => row.classList.remove('shake'), 450);
        }

        function bounceWinRow(rowIndex) {
            const row = board.querySelector(`.wp-wordle-row[data-row="${rowIndex}"]`);
            if (!row) return;
            const tiles = row.querySelectorAll('.wp-wordle-tile');
            tiles.forEach((t) => t.classList.add('win-bounce'));
        }

        if (btnShare) {
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
        }

        window.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            handleKeyPress(e.key);
        });

        keyboard.addEventListener('click', (e) => {
            const keyBtn = e.target.closest('.wp-wordle-key');
            if (!keyBtn) return;
            e.preventDefault();
            const key = keyBtn.getAttribute('data-key');
            if (key) {
                handleKeyPress(key);
            }
        });

        function openModal(modal) { if (modal) modal.style.display = 'flex'; }
        function closeModal(modal) { if (modal) modal.style.display = 'none'; }

        if (btnHelp && modalHelp) {
            btnHelp.addEventListener('click', () => openModal(modalHelp));
        }

        if (btnStats && modalStats) {
            btnStats.addEventListener('click', () => {
                updateStatsModal(getStats());
                openModal(modalStats);
            });
        }

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

        if (btnNewGame) {
            btnNewGame.addEventListener('click', () => {
                closeModal(modalStats);
                startNewGame('practice');
            });
        }

        updateStatsModal(getStats());
        startNewGame(mode);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWordle);
    } else {
        initWordle();
    }
})();