// Treasure Nexus - Hunt Management JavaScript
// Handles hunt creation, puzzle solving, and interactive gameplay

class HuntManager {
    constructor() {
        this.currentHunt = null;
        this.currentClue = 1;
        this.startTime = null;
        this.hintsUsed = 0;
        this.attempts = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadHuntData();
    }

    bindEvents() {
        // Hunt creation form
        const createForm = document.getElementById('create-hunt-form');
        if (createForm) {
            createForm.addEventListener('submit', (e) => this.handleHuntCreation(e));
        }

        // Clue answer submission
        const answerForm = document.getElementById('answer-form');
        if (answerForm) {
            answerForm.addEventListener('submit', (e) => this.handleAnswerSubmission(e));
        }

        // Hint request
        const hintBtn = document.getElementById('hint-btn');
        if (hintBtn) {
            hintBtn.addEventListener('click', () => this.requestHint());
        }

        // File uploads for clues
        this.setupFileUploads();
    }

    // Load current hunt data from URL parameters
    loadHuntData() {
        const urlParams = new URLSearchParams(window.location.search);
        const huntId = urlParams.get('id');
        
        if (huntId) {
            this.loadHunt(huntId);
        }
    }

    // Load hunt details and start session
    async loadHunt(huntId) {
        try {
            const response = await fetch(`api/hunts.php?action=get&id=${huntId}`);
            const data = await response.json();
            
            if (data.success) {
                this.currentHunt = data.hunt;
                this.startHuntSession();
                this.displayCurrentClue();
            } else {
                this.showError('Failed to load hunt: ' + data.message);
            }
        } catch (error) {
            this.showError('Network error: ' + error.message);
        }
    }

    // Start a new hunt session
    startHuntSession() {
        this.startTime = new Date();
        this.currentClue = 1;
        this.hintsUsed = 0;
        this.attempts = [];
        
        // Update UI
        this.updateTimer();
        this.updateProgress();
        
        // Start timer
        this.timerInterval = setInterval(() => this.updateTimer(), 1000);
        
        // Log session start
        this.logAttemptStart();
    }

    // Display current clue
    async displayCurrentClue() {
        try {
            const response = await fetch(`api/hunts.php?action=get_clue&hunt_id=${this.currentHunt.id}&clue_number=${this.currentClue}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderClue(data.clue);
                this.playClueRevealAnimation();
            } else {
                this.showError('Failed to load clue: ' + data.message);
            }
        } catch (error) {
            this.showError('Network error: ' + error.message);
        }
    }

    // Render clue content based on type
    renderClue(clue) {
        const container = document.getElementById('clue-container');
        if (!container) return;

        let clueHTML = `
            <div class="clue-card glass-card" style="padding: 32px; margin-bottom: 24px;">
                <div class="clue-header" style="margin-bottom: 24px;">
                    <h3 style="font-size: 1.8rem; margin-bottom: 8px;" class="text-gradient-cyber">
                        ${this.escapeHtml(clue.title)}
                    </h3>
                    <div style="display: flex; gap: 16px; color: var(--text-muted);">
                        <span>Clue ${this.currentClue} of ${this.currentHunt.total_puzzles}</span>
                        <span>Type: ${clue.type.toUpperCase()}</span>
                    </div>
                </div>
                
                <div class="clue-content" style="margin-bottom: 32px;">
        `;

        switch (clue.type) {
            case 'text':
                clueHTML += `<div class="text-clue" style="font-size: 1.2rem; line-height: 1.8; color: var(--text-primary);">
                    ${this.escapeHtml(clue.content)}
                </div>`;
                break;
                
            case 'image':
                clueHTML += `
                    <div class="image-clue text-center">
                        <img src="${this.escapeHtml(clue.content_file)}" alt="Puzzle Image" 
                             style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: var(--shadow-blue);">
                        <p style="margin-top: 16px; font-size: 1.1rem;">${this.escapeHtml(clue.content)}</p>
                    </div>
                `;
                break;
                
            case 'audio':
                clueHTML += `
                    <div class="audio-clue text-center">
                        <audio controls style="margin-bottom: 16px; width: 100%;">
                            <source src="${this.escapeHtml(clue.content_file)}" type="audio/mpeg">
                            Your browser does not support audio.
                        </audio>
                        <p style="font-size: 1.1rem;">${this.escapeHtml(clue.content)}</p>
                    </div>
                `;
                break;
        }

        clueHTML += `
                </div>
            </div>
            
            <div class="answer-section glass-card" style="padding: 24px;">
                <form id="answer-form">
                    <div style="margin-bottom: 16px;">
                        <label for="answer-input" style="display: block; margin-bottom: 8px; color: var(--text-muted);">
                            Your Answer:
                        </label>
                        <input type="text" id="answer-input" name="answer" 
                               style="width: 100%; padding: 12px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 1.1rem;"
                               placeholder="Enter your answer..." required>
                    </div>
                    
                    <div style="display: flex; gap: 16px; justify-content: space-between; align-items: center;">
                        <button type="button" id="hint-btn" class="cyber-btn" 
                                ${clue.hint ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
                            💡 Get Hint ${clue.hint ? '(-5 min penalty)' : '(Not available)'}
                        </button>
                        
                        <button type="submit" class="cyber-btn hologram" style="font-size: 1.1rem; padding: 12px 24px;">
                            🔍 Submit Answer
                        </button>
                    </div>
                </form>
            </div>
        `;

        container.innerHTML = clueHTML;
        
        // Re-bind form events
        this.bindAnswerForm();
    }

    // Bind answer form events
    bindAnswerForm() {
        const form = document.getElementById('answer-form');
        const hintBtn = document.getElementById('hint-btn');
        
        if (form) {
            form.addEventListener('submit', (e) => this.handleAnswerSubmission(e));
        }
        
        if (hintBtn && !hintBtn.disabled) {
            hintBtn.addEventListener('click', () => this.requestHint());
        }
    }

    // Handle answer submission
    async handleAnswerSubmission(e) {
        e.preventDefault();
        
        const answerInput = document.getElementById('answer-input');
        const answer = answerInput.value.trim();
        
        if (!answer) {
            this.showError('Please enter an answer');
            return;
        }

        // Disable form during submission
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Checking...';

        try {
            const response = await fetch('api/hunts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'submit_answer',
                    hunt_id: this.currentHunt.id,
                    clue_number: this.currentClue,
                    answer: answer
                })
            });

            const data = await response.json();
            
            if (data.success) {
                if (data.correct) {
                    this.handleCorrectAnswer();
                } else {
                    this.handleIncorrectAnswer();
                }
            } else {
                this.showError('Error: ' + data.message);
            }
        } catch (error) {
            this.showError('Network error: ' + error.message);
        } finally {
            // Re-enable form
            submitBtn.disabled = false;
            submitBtn.textContent = '🔍 Submit Answer';
        }
    }

    // Handle correct answer
    handleCorrectAnswer() {
        // Play success sound and animation
        this.playSuccessAnimation();
        
        // Record attempt
        this.attempts.push({
            clue: this.currentClue,
            correct: true,
            time: new Date() - this.startTime
        });

        // Check if hunt is complete
        if (this.currentClue >= this.currentHunt.total_puzzles) {
            this.completeHunt();
        } else {
            // Move to next clue
            setTimeout(() => {
                this.currentClue++;
                this.displayCurrentClue();
                this.updateProgress();
            }, 2000);
        }
    }

    // Handle incorrect answer
    handleIncorrectAnswer() {
        this.playErrorAnimation();
        
        // Record attempt
        this.attempts.push({
            clue: this.currentClue,
            correct: false,
            time: new Date() - this.startTime
        });

        // Clear answer input
        const answerInput = document.getElementById('answer-input');
        if (answerInput) {
            answerInput.value = '';
            answerInput.focus();
        }
    }

    // Request hint for current clue
    async requestHint() {
        if (this.hintsUsed >= 3) {
            this.showError('Maximum hints used for this hunt');
            return;
        }

        try {
            const response = await fetch(`api/hunts.php?action=get_hint&hunt_id=${this.currentHunt.id}&clue_number=${this.currentClue}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayHint(data.hint);
                this.hintsUsed++;
                
                // Disable hint button
                const hintBtn = document.getElementById('hint-btn');
                if (hintBtn) {
                    hintBtn.disabled = true;
                    hintBtn.textContent = 'Hint Used';
                }
            } else {
                this.showError('Hint not available');
            }
        } catch (error) {
            this.showError('Network error: ' + error.message);
        }
    }

    // Display hint
    displayHint(hint) {
        const hintContainer = document.createElement('div');
        hintContainer.className = 'hint-container glass-card';
        hintContainer.style.cssText = `
            margin: 16px 0;
            padding: 16px;
            background: var(--glass-bg);
            border: 1px solid var(--neon-green);
            border-radius: 8px;
            animation: fadeInUp 0.5s ease;
        `;
        
        hintContainer.innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <span style="font-size: 1.2rem;">💡</span>
                <strong class="neon-green">Hint:</strong>
            </div>
            <p style="color: var(--text-muted);">${this.escapeHtml(hint)}</p>
        `;
        
        const answerSection = document.querySelector('.answer-section');
        if (answerSection) {
            answerSection.parentNode.insertBefore(hintContainer, answerSection);
        }
    }

    // Complete hunt
    async completeHunt() {
        clearInterval(this.timerInterval);
        const totalTime = new Date() - this.startTime;
        
        try {
            const response = await fetch('api/hunts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'complete_hunt',
                    hunt_id: this.currentHunt.id,
                    total_time: Math.floor(totalTime / 1000),
                    hints_used: this.hintsUsed
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.displayCompletionScreen(data.score, data.rank);
            }
        } catch (error) {
            this.showError('Error completing hunt: ' + error.message);
        }
    }

    // Display completion screen
    displayCompletionScreen(score, rank) {
        const container = document.getElementById('clue-container');
        if (!container) return;

        const totalTime = new Date() - this.startTime;
        const minutes = Math.floor(totalTime / 60000);
        const seconds = Math.floor((totalTime % 60000) / 1000);

        container.innerHTML = `
            <div class="completion-screen glass-card text-center" style="padding: 48px;">
                <div style="font-size: 4rem; margin-bottom: 24px;">🏆</div>
                
                <h2 class="text-gradient-hologram" style="font-size: 3rem; margin-bottom: 16px;">
                    HUNT COMPLETED!
                </h2>
                
                <p style="font-size: 1.5rem; color: var(--text-muted); margin-bottom: 32px;">
                    Congratulations! You've successfully completed "${this.currentHunt.title}"
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    <div class="stat-card">
                        <div class="stat-number neon-blue">${score}</div>
                        <div class="stat-label">Final Score</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number neon-green">${minutes}:${seconds.toString().padStart(2, '0')}</div>
                        <div class="stat-label">Total Time</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number neon-purple">#${rank}</div>
                        <div class="stat-label">Your Rank</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number neon-blue">${this.hintsUsed}</div>
                        <div class="stat-label">Hints Used</div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                    <a href="index.php" class="cyber-btn hologram">🏠 Home</a>
                    <a href="discover.php" class="cyber-btn cyber">🔍 More Hunts</a>
                    <a href="leaderboard.php" class="cyber-btn">🏆 Leaderboard</a>
                </div>
            </div>
        `;
        
        this.playCompletionAnimation();
    }

    // Update timer display
    updateTimer() {
        const timerElement = document.getElementById('timer');
        if (!timerElement || !this.startTime) return;

        const elapsed = new Date() - this.startTime;
        const minutes = Math.floor(elapsed / 60000);
        const seconds = Math.floor((elapsed % 60000) / 1000);
        
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        timerElement.className = 'neon-blue';
    }

    // Update progress bar
    updateProgress() {
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        
        if (progressBar) {
            const progress = (this.currentClue - 1) / this.currentHunt.total_puzzles * 100;
            progressBar.style.width = progress + '%';
        }
        
        if (progressText) {
            progressText.textContent = `${this.currentClue - 1} / ${this.currentHunt.total_puzzles} puzzles solved`;
        }
    }

    // Animation methods
    playClueRevealAnimation() {
        const clueCard = document.querySelector('.clue-card');
        if (clueCard) {
            clueCard.style.animation = 'fadeInUp 0.8s ease';
        }
    }

    playSuccessAnimation() {
        const container = document.getElementById('clue-container');
        if (container) {
            const flash = document.createElement('div');
            flash.style.cssText = `
                position: fixed;
                inset: 0;
                background: var(--neon-green);
                opacity: 0.2;
                animation: flash 0.5s ease;
                z-index: 1000;
                pointer-events: none;
            `;
            document.body.appendChild(flash);
            
            setTimeout(() => document.body.removeChild(flash), 500);
        }
    }

    playErrorAnimation() {
        const answerInput = document.getElementById('answer-input');
        if (answerInput) {
            answerInput.style.animation = 'shake 0.5s ease';
            answerInput.style.borderColor = 'var(--neon-red)';
            
            setTimeout(() => {
                answerInput.style.animation = '';
                answerInput.style.borderColor = '';
            }, 500);
        }
    }

    playCompletionAnimation() {
        // Trigger fireworks or celebration animation
        this.createFireworks();
    }

    createFireworks() {
        for (let i = 0; i < 10; i++) {
            setTimeout(() => {
                const firework = document.createElement('div');
                firework.style.cssText = `
                    position: fixed;
                    top: ${Math.random() * 50 + 25}%;
                    left: ${Math.random() * 80 + 10}%;
                    width: 4px;
                    height: 4px;
                    background: var(--neon-blue);
                    border-radius: 50%;
                    animation: explode 1s ease-out forwards;
                    z-index: 1000;
                `;
                document.body.appendChild(firework);
                
                setTimeout(() => document.body.removeChild(firework), 1000);
            }, i * 200);
        }
    }

    // Utility methods
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showError(message) {
        const errorContainer = document.getElementById('error-container') || this.createErrorContainer();
        errorContainer.innerHTML = `
            <div class="error-message glass-card" style="padding: 16px; margin: 16px 0; background: var(--glass-bg); border: 1px solid var(--neon-red); border-radius: 8px;">
                <span style="color: var(--neon-red);">❌ ${this.escapeHtml(message)}</span>
            </div>
        `;
        
        setTimeout(() => {
            errorContainer.innerHTML = '';
        }, 5000);
    }

    createErrorContainer() {
        const container = document.createElement('div');
        container.id = 'error-container';
        document.body.appendChild(container);
        return container;
    }

    setupFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.handleFileUpload(e));
        });
    }

    handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            this.showError('File size must be less than 10MB');
            e.target.value = '';
            return;
        }

        // Preview file if it's an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('file-preview');
                if (preview) {
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; height: auto; border-radius: 8px;">`;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Log attempt start for analytics
    async logAttemptStart() {
        try {
            await fetch('api/hunts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'start_attempt',
                    hunt_id: this.currentHunt.id
                })
            });
        } catch (error) {
            console.error('Failed to log attempt start:', error);
        }
    }
}

// Additional CSS for animations
const huntAnimationCSS = `
@keyframes flash {
    0% { opacity: 0; }
    50% { opacity: 0.3; }
    100% { opacity: 0; }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

@keyframes explode {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(20);
        opacity: 0;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
`;

// Add animation styles
const huntStyle = document.createElement('style');
huntStyle.textContent = huntAnimationCSS;
document.head.appendChild(huntStyle);

// Initialize hunt manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.huntManager = new HuntManager();
});