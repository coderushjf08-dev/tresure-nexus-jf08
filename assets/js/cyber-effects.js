// Treasure Nexus - Cyber Effects JavaScript
// 2050 Futuristic UI Animations and Effects

class CyberEffects {
    constructor() {
        this.init();
    }

    init() {
        this.createParticles();
        this.addHoverEffects();
        this.addScanLineEffects();
        this.addTypingEffects();
        this.addFloatingShapes();
        this.initScrollAnimations();
    }

    // Create floating particles in background
    createParticles() {
        const particleContainer = document.querySelector('.particle-bg');
        if (!particleContainer) return;

        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 1}px;
                height: ${Math.random() * 4 + 1}px;
                background: var(--neon-blue);
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${Math.random() * 10 + 5}s ease-in-out infinite;
                animation-delay: ${Math.random() * 5}s;
                opacity: ${Math.random() * 0.5 + 0.2};
                box-shadow: 0 0 10px var(--neon-blue-glow);
            `;
            particleContainer.appendChild(particle);
        }
    }

    // Add hover glow effects to buttons and cards
    addHoverEffects() {
        const glowElements = document.querySelectorAll('.cyber-btn, .glass-card, .hunt-card');
        
        glowElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.addGlow(e.target);
            });
            
            element.addEventListener('mouseleave', (e) => {
                this.removeGlow(e.target);
            });
        });
    }

    addGlow(element) {
        const colors = ['var(--neon-blue)', 'var(--neon-purple)', 'var(--neon-green)'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        
        element.style.transition = 'all 0.3s ease';
        element.style.boxShadow = `0 0 20px ${randomColor}, 0 0 40px ${randomColor}`;
        element.style.transform = 'translateY(-5px) scale(1.02)';
    }

    removeGlow(element) {
        element.style.boxShadow = '';
        element.style.transform = '';
    }

    // Add scan line effects to specific elements
    addScanLineEffects() {
        const scanElements = document.querySelectorAll('.scan-effect');
        
        scanElements.forEach(element => {
            if (!element.querySelector('.scan-line')) {
                const scanLine = document.createElement('div');
                scanLine.className = 'scan-line';
                scanLine.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, var(--neon-blue), transparent);
                    animation: scan-line 3s linear infinite;
                `;
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(scanLine);
            }
        });
    }

    // Matrix-style typing effect
    addTypingEffects() {
        const matrixElements = document.querySelectorAll('.matrix-text');
        
        matrixElements.forEach(element => {
            this.typeWriter(element);
        });
    }

    typeWriter(element) {
        const text = element.textContent;
        element.textContent = '';
        let i = 0;
        
        const timer = setInterval(() => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
            } else {
                clearInterval(timer);
            }
        }, 100);
    }

    // Add floating geometric shapes
    addFloatingShapes() {
        const hero = document.querySelector('.hero');
        if (!hero) return;

        const shapes = [
            { size: 60, color: 'var(--neon-blue)', rotation: 45, delay: 0 },
            { size: 40, color: 'var(--neon-purple)', rotation: 12, delay: 2 },
            { size: 80, color: 'var(--neon-green)', rotation: 0, delay: 1 }
        ];

        shapes.forEach((shape, index) => {
            const element = document.createElement('div');
            element.style.cssText = `
                position: absolute;
                width: ${shape.size}px;
                height: ${shape.size}px;
                border: 2px solid ${shape.color};
                transform: rotate(${shape.rotation}deg);
                animation: float 6s ease-in-out infinite;
                animation-delay: ${shape.delay}s;
                opacity: 0.3;
                top: ${20 + Math.random() * 60}%;
                left: ${10 + Math.random() * 80}%;
                z-index: 1;
            `;
            hero.appendChild(element);
        });
    }

    // Initialize scroll-based animations
    initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
                }
            });
        }, observerOptions);

        // Observe elements for scroll animations
        const animateElements = document.querySelectorAll('.hunt-card, .stat-card, .leaderboard-row');
        animateElements.forEach(el => observer.observe(el));
    }

    // Pulse effect for important elements
    addPulseEffect(element) {
        element.style.animation = 'pulse-glow 2s ease-in-out infinite';
    }

    // Holographic text effect
    createHolographicText(element) {
        element.style.background = 'linear-gradient(45deg, var(--neon-blue), var(--neon-purple), var(--neon-green))';
        element.style.webkitBackgroundClip = 'text';
        element.style.webkitTextFillColor = 'transparent';
        element.style.backgroundClip = 'text';
        element.style.backgroundSize = '200% 200%';
        element.style.animation = 'gradient-shift 4s ease infinite';
    }

    // Glitch effect for dramatic moments
    createGlitchEffect(element) {
        const glitchKeyframes = `
            @keyframes glitch {
                0% { transform: translate(0); }
                20% { transform: translate(-2px, 2px); }
                40% { transform: translate(-2px, -2px); }
                60% { transform: translate(2px, 2px); }
                80% { transform: translate(2px, -2px); }
                100% { transform: translate(0); }
            }
        `;
        
        if (!document.querySelector('#glitch-styles')) {
            const style = document.createElement('style');
            style.id = 'glitch-styles';
            style.textContent = glitchKeyframes;
            document.head.appendChild(style);
        }
        
        element.style.animation = 'glitch 0.3s ease-in-out 3';
    }
}

// Digital clock with neon effect
class DigitalClock {
    constructor(element) {
        this.element = element;
        this.update();
        setInterval(() => this.update(), 1000);
    }

    update() {
        const now = new Date();
        const time = now.toLocaleTimeString('en-US', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        if (this.element) {
            this.element.textContent = time;
            this.element.className = 'neon-blue';
            this.element.style.fontFamily = 'Orbitron, monospace';
            this.element.style.fontWeight = '900';
        }
    }
}

// Audio feedback system
class AudioFeedback {
    constructor() {
        this.context = null;
        this.initAudio();
    }

    initAudio() {
        try {
            this.context = new (window.AudioContext || window.webkitAudioContext)();
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    playBeep(frequency = 800, duration = 200, type = 'sine') {
        if (!this.context) return;

        const oscillator = this.context.createOscillator();
        const gainNode = this.context.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(this.context.destination);

        oscillator.frequency.value = frequency;
        oscillator.type = type;

        gainNode.gain.setValueAtTime(0.3, this.context.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, this.context.currentTime + duration / 1000);

        oscillator.start(this.context.currentTime);
        oscillator.stop(this.context.currentTime + duration / 1000);
    }

    playSuccess() {
        this.playBeep(600, 200);
        setTimeout(() => this.playBeep(800, 200), 100);
    }

    playError() {
        this.playBeep(300, 400, 'sawtooth');
    }

    playHover() {
        this.playBeep(1000, 100, 'square');
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize cyber effects
    const cyberEffects = new CyberEffects();
    
    // Initialize audio feedback
    const audioFeedback = new AudioFeedback();
    
    // Add audio feedback to buttons
    document.querySelectorAll('.cyber-btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => audioFeedback.playHover());
        btn.addEventListener('click', () => audioFeedback.playBeep(1200, 150));
    });
    
    // Initialize digital clocks
    document.querySelectorAll('.digital-clock').forEach(clock => {
        new DigitalClock(clock);
    });
    
    // Add matrix rain effect to background (optional)
    createMatrixRain();
});

// Matrix rain effect
function createMatrixRain() {
    const canvas = document.createElement('canvas');
    canvas.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
        opacity: 0.1;
    `;
    
    document.body.appendChild(canvas);
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%+-/~{[|`]}";
    const matrixArray = matrix.split("");
    
    const fontSize = 14;
    const columns = canvas.width / fontSize;
    const drops = [];
    
    for (let x = 0; x < columns; x++) {
        drops[x] = 1;
    }
    
    function draw() {
        ctx.fillStyle = 'rgba(13, 16, 23, 0.04)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = '#00d4ff';
        ctx.font = fontSize + 'px Orbitron';
        
        for (let i = 0; i < drops.length; i++) {
            const text = matrixArray[Math.floor(Math.random() * matrixArray.length)];
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            
            if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                drops[i] = 0;
            }
            drops[i]++;
        }
    }
    
    setInterval(draw, 35);
    
    // Resize canvas on window resize
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

// CSS animations keyframes
const additionalCSS = `
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

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes scan-line {
    0% { left: -100%; }
    100% { left: 100%; }
}
`;

// Add additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);