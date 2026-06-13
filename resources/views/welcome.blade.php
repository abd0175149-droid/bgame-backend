<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BGame API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0a0a0f;
            color: #fff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99, 51, 255, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(0, 180, 255, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(255, 51, 102, 0.08) 0%, transparent 50%);
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0%   { opacity: 0.6; }
            100% { opacity: 1; }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 700px;
            width: 100%;
        }

        /* Radar animation */
        .radar {
            width: 140px;
            height: 140px;
            margin: 0 auto 2rem;
            position: relative;
        }

        .radar-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(99, 51, 255, 0.4);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: radarPulse 3s ease-out infinite;
        }

        .radar-circle:nth-child(1) { width: 40px;  height: 40px;  animation-delay: 0s; }
        .radar-circle:nth-child(2) { width: 80px;  height: 80px;  animation-delay: 0.5s; }
        .radar-circle:nth-child(3) { width: 120px; height: 120px; animation-delay: 1s; }
        .radar-circle:nth-child(4) { width: 140px; height: 140px; animation-delay: 1.5s; border-color: rgba(99,51,255,0.15); }

        @keyframes radarPulse {
            0%   { border-color: rgba(99, 51, 255, 0.6); }
            100% { border-color: rgba(99, 51, 255, 0.05); }
        }

        .radar-sweep {
            position: absolute;
            width: 50%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(99, 51, 255, 0.8));
            top: 50%; left: 50%;
            transform-origin: left center;
            animation: sweep 3s linear infinite;
        }

        @keyframes sweep {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .radar-dot {
            position: absolute;
            width: 8px; height: 8px;
            background: #6333ff;
            border-radius: 50%;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 12px rgba(99, 51, 255, 0.8);
        }

        h1 {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #6333ff, #00b4ff, #ff3366);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 1rem;
            margin-bottom: 2.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
            color: #00c864;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2.5rem;
            letter-spacing: 1px;
        }

        .status-dot {
            width: 8px; height: 8px;
            background: #00c864;
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.2; }
        }

        /* Endpoints grid */
        .endpoints {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .endpoint-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 1.2rem;
            text-align: left;
            transition: all 0.3s ease;
        }

        .endpoint-card:hover {
            background: rgba(99, 51, 255, 0.1);
            border-color: rgba(99, 51, 255, 0.4);
            transform: translateY(-2px);
        }

        .endpoint-method {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .method-post { background: rgba(255,140,0,0.2); color: #ff8c00; }
        .method-get  { background: rgba(0,180,255,0.2); color: #00b4ff; }

        .endpoint-path {
            font-family: monospace;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.8);
        }

        .endpoint-desc {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.35);
            margin-top: 4px;
        }

        /* Info row */
        .info-row {
            display: flex;
            justify-content: center;
            gap: 2rem;
            color: rgba(255,255,255,0.3);
            font-size: 0.8rem;
        }

        .info-row span { display: flex; align-items: center; gap: 6px; }
        .info-row strong { color: rgba(255,255,255,0.6); }
    </style>
</head>
<body>
    <div class="container">

        <!-- Radar -->
        <div class="radar">
            <div class="radar-circle"></div>
            <div class="radar-circle"></div>
            <div class="radar-circle"></div>
            <div class="radar-circle"></div>
            <div class="radar-sweep"></div>
            <div class="radar-dot"></div>
        </div>

        <h1>BGame API</h1>
        <p class="subtitle">Game Backend Server</p>

        <div class="status-badge">
            <div class="status-dot"></div>
            SYSTEM ONLINE
        </div>

        <div class="endpoints">
            <div class="endpoint-card">
                <span class="endpoint-method method-post">POST</span>
                <div class="endpoint-path">/api/auth/register</div>
                <div class="endpoint-desc">Player Registration</div>
            </div>
            <div class="endpoint-card">
                <span class="endpoint-method method-post">POST</span>
                <div class="endpoint-path">/api/auth/login</div>
                <div class="endpoint-desc">Player Login</div>
            </div>
            <div class="endpoint-card">
                <span class="endpoint-method method-get">GET</span>
                <div class="endpoint-path">/api/places</div>
                <div class="endpoint-desc">Saved Locations</div>
            </div>
            <div class="endpoint-card">
                <span class="endpoint-method method-post">POST</span>
                <div class="endpoint-path">/api/sessions/start</div>
                <div class="endpoint-desc">Start Game Session</div>
            </div>
            <div class="endpoint-card">
                <span class="endpoint-method method-get">GET</span>
                <div class="endpoint-path">/api/leaderboard</div>
                <div class="endpoint-desc">Global Rankings</div>
            </div>
            <div class="endpoint-card">
                <span class="endpoint-method method-get">GET</span>
                <div class="endpoint-path">/api/leaderboard/my-rank</div>
                <div class="endpoint-desc">My Rank</div>
            </div>
        </div>

        <div class="info-row">
            <span>Laravel <strong>v{{ app()->version() }}</strong></span>
            <span>PHP <strong>v{{ PHP_VERSION }}</strong></span>
            <span>BGame <strong>v1.0.0</strong></span>
        </div>

    </div>
</body>
</html>
