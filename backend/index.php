<?php
// index.php - Improved Landing Page
session_start();

$title = "Yolundayam";

if (isset($_GET['action']) && $_GET['action'] == 'reset') {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4682b4;
            --secondary: #87ceeb;
            --accent: #ffca28;
            --dark: #1e2a47;
            --light: #ffffff;
            --glass: rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            background: var(--dark);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            color: var(--light);
        }

        /* Video Background Wrapper */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            /* Static placeholder: Night road in Baku */
            background: #000 url('https://images.pond5.com/night-timelapse-busy-street-baku-footage-248203216_iconl.jpeg') no-repeat center center;
            background-size: cover;
        }

        .video-background iframe {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100vw;
            height: 56.25vw;
            /* 16:9 aspect ratio */
            min-height: 100vh;
            min-width: 177.77vh;
            /* 16:9 aspect ratio */
            transform: translate(-50%, -50%) scale(1.05);
            /* Slight scale to hide edge artifacts */
            pointer-events: none;
            opacity: 0;
            /* Start invisible */
            transition: opacity 2s ease-in-out;
            /* Smooth fade in */
        }

        /* Class to trigger the fade-in via JS */
        .video-background iframe.playing {
            opacity: 0.6;
        }

        /* Glass Container */
        .hero {
            text-align: center;
            padding: 4rem;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            max-width: 700px;
            width: 90%;
            animation: fadeIn 1s ease-out;
            position: relative;
            z-index: 1;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: inline-block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        h1 {
            font-size: 4rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(to right, #ffffff, #ffca28);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        p {
            font-size: 1.25rem;
            opacity: 0.9;
            margin: 1.5rem 0 3rem;
            font-weight: 400;
            line-height: 1.6;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* Buttons */
        .nav-actions {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .btn {
            padding: 1.2rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 15px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-block;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--dark);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background: var(--light);
            color: var(--dark);
        }

        .btn-outline {
            background: transparent;
            color: var(--light);
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--light);
            transform: translateY(-3px);
        }

        .reset-link {
            margin-top: 2rem;
            display: block;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.3s;
        }

        .reset-link:hover {
            color: var(--light);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2.5rem;
                letter-spacing: -1px;
            }

            .hero {
                padding: 1.5rem;
                width: 85%;
            }

            p {
                font-size: 1rem;
                margin-bottom: 2rem;
            }

            .btn {
                padding: 1rem 1.5rem;
                font-size: 1rem;
            }

            .video-background iframe {
                width: 300vw;
                /* zoom in for portrait mobile */
                left: 50%;
            }
        }
    </style>
</head>

<body>
    <div class="video-background">
        <div id="player"></div>
    </div>

    <!-- YouTube IFrame API -->
    <script>
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('player', {
                height: '100%',
                width: '100%',
                videoId: 'qxj_R_uYPEU',
                playerVars: {
                    'autoplay': 1,
                    'controls': 0,
                    'showinfo': 0,
                    'rel': 0,
                    'iv_load_policy': 3,
                    'modestbranding': 1,
                    'disablekb': 1,
                    'mute': 1,
                    'loop': 1,
                    'playlist': 'qxj_R_uYPEU'
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            event.target.playVideo();
            event.target.mute();
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING) {
                // Wait 800ms to ensure player is cleared of logo/controls before fading in
                setTimeout(function() {
                    document.querySelector('.video-background iframe').classList.add('playing');
                }, 800);
            }
        }
    </script>

    <main class="hero">
        <header>
            <h1>Yolundayam</h1>
            <p>Your premium companion for tracking and optimizing your supervised driving journey.</p>
        </header>

        <nav class="nav-actions">
            <a href="form_entry.php" class="btn btn-primary">Start New Session</a>
            <a href="dashboard.php" class="btn btn-outline">Analytics Dashboard</a>
        </nav>

        <a href="?action=reset" class="reset-link">Clear Current Session Data</a>
    </main>
</body>

</html>