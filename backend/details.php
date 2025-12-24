<?php
// details.php - Detailed view of a driving session
session_start();
require_once 'inc/class.inc.php';

$manager = new DrivingExperienceManager();

// Anonymization decoding using Session Mapping
$codedId = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['code']) ? $_GET['code'] : null);
$id = SessionAnonymizer::getId($codedId);

if (!$id) {
    header("Location: dashboard.php");
    exit();
}

try {
    $details = $manager->getExperienceDetails($id);
} catch (Exception $e) {
    $details = null;
}

if (!$details) {
    die("Session not found or database error.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details - Yolundayam</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #60a5fa;
            --secondary: #34d399;
            --accent: #fbbf24;
            --dark: #0f172a;
            --light: #f8fafc;
            --glass: rgba(15, 23, 42, 0.7);
            --glass-light: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://images.pond5.com/night-timelapse-busy-street-baku-footage-248203216_iconl.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: var(--light);
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.4), rgba(15, 23, 42, 0.9));
            z-index: -1;
        }

        .header {
            padding: 4rem 1rem;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -1px;
            font-size: 2.5rem;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container {
            max-width: 800px;
            margin: 0 auto 3rem;
            padding: 0 1rem;
            position: relative;
            z-index: 10;
        }

        .card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--glass-border);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2.5rem;
            margin-bottom: 3rem;
        }

        .info-item h4 {
            margin: 0;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .info-item .value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.5rem;
            color: white;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 800;
            margin: 2.5rem 0 1.5rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-title::after {
            content: '';
            flex-grow: 1;
            height: 1px;
            background: linear-gradient(to right, var(--glass-border), transparent);
        }

        .tag-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .tag {
            background: var(--glass-light);
            color: white;
            padding: 0.8rem 1.4rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid var(--glass-border);
            transition: all 0.3s;
        }

        .tag:hover {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
        }

        .back-nav {
            text-align: center;
            margin-top: 3rem;
        }

        .back-nav a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .back-nav a:hover {
            color: var(--primary);
            transform: translateX(-5px);
        }

        @media (max-width: 600px) {
            .card {
                padding: 1.5rem;
            }

            .grid {
                gap: 1.5rem;
            }

            .info-item .value {
                font-size: 1.2rem;
            }

            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>Session Details</h1>
        <p>A deep dive into your drive</p>
        <nav class="nav-links">
            <a href="dashboard.php">← Back to Dashboard</a>
        </nav>
    </header>

    <main class="container">
        <div class="main-card">
            <div class="details-header">
                <div class="date-badge">
                    <span class="day"><?php echo date('d', strtotime($details->date)); ?></span>
                    <span class="month"><?php echo date('M', strtotime($details->date)); ?></span>
                </div>
                <div class="primary-info">
                    <h2><?php echo number_format($details->mileage, 1); ?> km</h2>
                    <p><?php echo htmlspecialchars($details->getDuration()); ?> journey</p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <h4>Start Time</h4>
                    <div class="value"><?php echo date('H:i', strtotime($details->startTime)); ?></div>
                </div>
                <div class="info-item">
                    <h4>End Time</h4>
                    <div class="value"><?php echo date('H:i', strtotime($details->endTime)); ?></div>
                </div>
                <div class="info-item">
                    <h4>Weather</h4>
                    <div class="value"><?php echo htmlspecialchars($details->weatherLabel ?? 'Clear'); ?></div>
                </div>
                <div class="info-item">
                    <h4>Traffic</h4>
                    <div class="value"><?php echo htmlspecialchars($details->trafficLabel ?? 'Light'); ?></div>
                </div>
                <div class="info-item">
                    <h4>Fatigue</h4>
                    <div class="value">Level <?php echo htmlspecialchars($details->fatigueLabel ?? '1'); ?></div>
                </div>
            </div>

            <div class="section-title">Road Conditions</div>
            <div class="tag-group">
                <?php if (empty($details->roadConditions)): ?>
                    <span class="tag" style="opacity: 0.5;">None recorded</span>
                <?php else: ?>
                    <?php foreach ($details->roadConditions as $rc): ?>
                        <span class="tag"><?php echo htmlspecialchars($rc); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="section-title">Manoeuvres</div>
            <div class="tag-group">
                <?php if (empty($details->manoeuvres)): ?>
                    <span class="tag" style="opacity: 0.5;">None recorded</span>
                <?php else: ?>
                    <?php foreach ($details->manoeuvres as $m): ?>
                        <span class="tag"><?php echo htmlspecialchars($m); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a href="edit_entry.php?id=<?php echo SessionAnonymizer::getCode($details->id); ?>"
                    class="btn-edit">Edit
                    Session</a>
            </div>
        </div>
    </main>
</body>

</html>