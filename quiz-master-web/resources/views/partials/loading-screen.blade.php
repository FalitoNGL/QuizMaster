<div id="global-loader" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #020617; /* Slate-950 */
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: opacity 0.5s ease, visibility 0.5s;
">
    <div style="position: relative; width: 120px; height: 120px;">
        <!-- Pulsing Glow -->
        <div class="loader-glow"></div>
        
        <!-- Animated Logo -->
        <img src="{{ asset('logo.svg') }}" alt="Logo" class="loader-logo" style="
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 2;
        ">
    </div>

    <div style="margin-top: 32px;">
        <span style="
            color: rgba(255, 255, 255, 0.6);
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 4px;
            text-transform: uppercase;
            animation: textPulse 1s ease-in-out infinite alternate;
        ">Loading Magic...</span>
    </div>

    <style>
        .loader-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, transparent 70%);
            z-index: 1;
            animation: pulseGlow 1.5s ease-in-out infinite alternate;
        }

        .loader-logo {
            animation: pulseScale 1s ease-in-out infinite alternate;
        }

        @keyframes pulseGlow {
            from { opacity: 0.3; width: 140px; height: 140px; }
            to { opacity: 0.8; width: 200px; height: 200px; }
        }

        @keyframes pulseScale {
            from { transform: scale(1); }
            to { transform: scale(1.15); }
        }

        @keyframes textPulse {
            from { opacity: 0.4; }
            to { opacity: 1; }
        }

        .loader-hide {
            opacity: 0 !important;
            visibility: hidden !important;
        }
    </style>

    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-loader');
            setTimeout(() => {
                loader.classList.add('loader-hide');
            }, 600); // Small delay for polish
        });
    </script>
</div>
