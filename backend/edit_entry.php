<?php
// edit_entry.php - Modernized Data Entry Form for Editing
session_start();
require_once 'inc/class.inc.php';

$manager = new DrivingExperienceManager();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = SessionAnonymizer::getId($_GET['id'] ?? '');
$details = $manager->getExperienceDetails($id);

if (!$details) {
    header('Location: dashboard.php');
    exit;
}

// Fetch lookup data
$weatherList = $manager->getWeatherList();
$roadConditionsList = $manager->getRoadConditionList();
$trafficList = $manager->getTrafficConditionList();
$manoeuvresList = $manager->getManoeuvreList();
$fatigueLevels = $manager->getFatigueLevels();

$message = "";
$messageType = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'date' => $_POST['date'],
        'start_time' => $_POST['startTime'],
        'end_time' => $_POST['endTime'],
        'mileage' => $_POST['mileage'],
        'fatigue_id' => $_POST['fatigue'],
        'weather_id' => $_POST['weather'],
        'traffic_id' => $_POST['trafficIntensity']
    ];

    $selectedManoeuvres = isset($_POST['manoeuvres']) ? $_POST['manoeuvres'] : [];
    $selectedRoadConditions = isset($_POST['roadConditions']) ? $_POST['roadConditions'] : [];

    try {
        $manager->updateExperience($id, $data, $selectedManoeuvres, $selectedRoadConditions);
        $message = "Session updated successfully!";
        $messageType = "success";
        // Refresh details after update
        $details = $manager->getExperienceDetails($id);
    } catch (Exception $e) {
        $message = "Error updating session: " . $e->getMessage();
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit session - Yolundayam</title>
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
            display: flex;
            flex-direction: column;
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
            padding: 3rem 1rem;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-weight: 800;
            letter-spacing: -2px;
            text-transform: uppercase;
            font-size: 3rem;
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

        .alert {
            padding: 1.2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: rgba(52, 211, 153, 0.2);
            color: #6ee7b7;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .alert-error {
            background: rgba(248, 113, 113, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        .form-section {
            margin-bottom: 3rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .form-section-title::after {
            content: '';
            flex-grow: 1;
            height: 1px;
            background: linear-gradient(to right, var(--glass-border), transparent);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }

        input,
        select {
            width: 100%;
            padding: 1.1rem;
            background: var(--glass-light);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
            border-color: var(--primary);
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
        }

        input::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 1rem;
        }

        .option-item input {
            position: absolute;
            opacity: 0;
        }

        .option-label {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 3.5rem;
            padding: 0.8rem 1rem;
            background: var(--glass-light);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            height: 100%;
            box-sizing: border-box;
        }

        .option-item input:checked+.option-label {
            background: var(--primary);
            color: var(--dark);
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(96, 165, 250, 0.3);
        }

        .btn-submit {
            width: 100%;
            padding: 1.4rem;
            background: var(--accent);
            color: var(--dark);
            border: none;
            border-radius: 18px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.4s;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 1rem;
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.2);
        }

        .btn-submit:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
            box-shadow: 0 15px 30px rgba(251, 191, 36, 0.4);
        }

        .back-nav {
            text-align: center;
            margin-top: 2rem;
        }

        .back-nav a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .back-nav a:hover {
            color: var(--primary);
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>Edit Session</h1>
    </header>

    <main class="container">
        <div class="card">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo ($messageType == 'success' ? '✅' : '❌'); ?>     <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="edit_entry.php?id=<?php echo SessionAnonymizer::getCode($details->id); ?>" method="POST">
                <div class="form-section">
                    <div class="form-section-title">General Info</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" required value="<?php echo $details->date; ?>">
                        </div>
                        <div class="form-group">
                            <label for="mileage">Kilometers Traveled</label>
                            <input type="number" id="mileage" name="mileage" step="0.1" min="0.1" required
                                value="<?php echo $details->mileage; ?>">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="startTime">Start Time</label>
                            <input type="time" id="startTime" name="startTime" required
                                value="<?php echo date('H:i', strtotime($details->startTime)); ?>">
                        </div>
                        <div class="form-group">
                            <label for="endTime">End Time</label>
                            <input type="time" id="endTime" name="endTime" required
                                value="<?php echo date('H:i', strtotime($details->endTime)); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Context</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="weather">Weather</label>
                            <select id="weather" name="weather" required>
                                <?php foreach ($weatherList as $w): ?>
                                    <option value="<?php echo $w['id']; ?>" <?php echo ($details->idWeather == $w['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($w['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fatigue">Your Fatigue Level (1-5)</label>
                            <select id="fatigue" name="fatigue" required>
                                <?php foreach ($fatigueLevels as $f): ?>
                                    <option value="<?php echo $f['id']; ?>" <?php echo ($details->idFatigue == $f['id']) ? 'selected' : ''; ?>>
                                        Level <?php echo htmlspecialchars($f['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Road Conditions</div>
                    <div class="options-grid">
                        <?php foreach ($roadConditionsList as $rc): ?>
                            <div class="option-item">
                                <input type="checkbox" id="rc_<?php echo $rc['id']; ?>" name="roadConditions[]"
                                    value="<?php echo $rc['id']; ?>" <?php echo (in_array($rc['label'], $details->roadConditions)) ? 'checked' : ''; ?>>
                                <label for="rc_<?php echo $rc['id']; ?>"
                                    class="option-label"><?php echo htmlspecialchars($rc['label']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Traffic Intensity</div>
                    <div class="options-grid">
                        <?php foreach ($trafficList as $tc): ?>
                            <div class="option-item">
                                <input type="radio" id="tc_<?php echo $tc['id']; ?>" name="trafficIntensity"
                                    value="<?php echo $tc['id']; ?>" required <?php echo ($details->trafficLabel == $tc['label']) ? 'checked' : ''; ?>>
                                <label for="tc_<?php echo $tc['id']; ?>"
                                    class="option-label"><?php echo htmlspecialchars($tc['label']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Manoeuvres Performed</div>
                    <div class="options-grid">
                        <?php foreach ($manoeuvresList as $m): ?>
                            <div class="option-item">
                                <input type="checkbox" id="m_<?php echo $m['id']; ?>" name="manoeuvres[]"
                                    value="<?php echo $m['id']; ?>" <?php echo (in_array($m['label'], $details->manoeuvres)) ? 'checked' : ''; ?>>
                                <label for="m_<?php echo $m['id']; ?>"
                                    class="option-label"><?php echo htmlspecialchars($m['label']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Update Session</button>
            </form>

            <div class="back-nav">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </main>
</body>

</html>