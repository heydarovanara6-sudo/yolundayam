<?php
// dashboard.php - Summary table and statistics
session_start();
require_once 'inc/class.inc.php';

$manager = new DrivingExperienceManager();
$experiences = $manager->getAllExperiences();
$totalDistance = $manager->getTotalDistance();
$weatherStats = $manager->getWeatherStats();

// Prepare data for ChartJS
$weatherLabels = [];
$weatherCounts = [];
foreach ($weatherStats as $stat) {
    if ($stat['label']) {
        $weatherLabels[] = $stat['label'];
        $weatherCounts[] = $stat['count'];
    }
}

$weatherList = $manager->getWeatherList();
$trafficList = $manager->getTrafficConditionList();
$fatigueLevels = $manager->getFatigueLevels();
$manoeuvreList = $manager->getManoeuvreList();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Yolundayam</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- jQuery and DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.6), rgba(15, 23, 42, 0.95));
            z-index: -1;
        }

        header {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            opacity: 0.7;
            transition: all 0.3s;
        }

        .nav-link:hover {
            opacity: 1;
            color: var(--primary);
        }

        main {
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                gap: 1rem;
                padding: 1.5rem 1rem;
                text-align: center;
            }

            .nav-links {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin-bottom: 2rem;
            }

            .stat-card {
                padding: 1.5rem !important;
                border-radius: 20px;
            }

            .stat-card .value {
                font-size: 1.8rem !important;
            }

            .stat-card h3 {
                font-size: 0.65rem !important;
                letter-spacing: 1px;
            }
        }

        .stat-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 2.5rem;
            border-radius: 30px;
            text-align: center;
            border: 1px solid var(--glass-border);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 2px;
        }

        .stat-card .value {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0.5rem 0;
            color: white;
        }

        .stat-card .unit {
            font-size: 1rem;
            color: var(--primary);
            margin-left: 5px;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2.5rem;
            margin-bottom: 2.5rem;
        }

        .card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 2.5rem;
            border-radius: 30px;
            border: 1px solid var(--glass-border);
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .chart-container {
            position: relative;
            margin: auto;
            max-width: 100%;
        }

        @media (max-width: 600px) {
            .chart-container {
                max-height: 250px;
            }
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            font-size: 0.95rem;
        }

        th {
            text-align: left;
            padding: 1rem 1.5rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 2px;
        }

        td {
            padding: 1.2rem 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: none;
        }

        tr td:first-child {
            border-radius: 15px 0 0 15px;
        }

        tr td:last-child {
            border-radius: 0 15px 15px 0;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.07);
        }

        /* DataTables Custom Styles */
        .dataTables_wrapper {
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            color: white;
            padding: 5px 10px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1.5rem;
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: white !important;
            border-radius: 8px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            border: none !important;
            color: var(--dark) !important;
        }

        table.dataTable.no-footer {
            border-bottom: none !important;
        }

        table.dataTable thead th {
            border-bottom: 2px solid var(--glass-border) !important;
        }

        .dataTables_filter {
            display: none;
            /* We use our own search bar */
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-weather {
            background: rgba(96, 165, 250, 0.15);
            color: var(--primary);
        }

        /* Fatigue Levels Styling */
        .fatigue-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.4rem 0.8rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .fatigue-1 {
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.1);
        }

        .fatigue-2 {
            background: rgba(167, 243, 208, 0.1);
            color: #6ee7b7;
            border: 1px solid rgba(167, 243, 208, 0.05);
        }

        .fatigue-3 {
            background: rgba(251, 191, 36, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.1);
        }

        .fatigue-4 {
            background: rgba(251, 146, 60, 0.15);
            color: #fb923c;
            border: 1px solid rgba(251, 146, 60, 0.1);
        }

        .fatigue-5 {
            background: rgba(248, 113, 113, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.1);
        }

        .fatigue-icon {
            font-size: 1rem;
        }

        .btn-action {
            padding: 0.6rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid var(--glass-border);
        }

        .btn-action:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--dark);
        }

        .fab {
            position: fixed;
            bottom: 3rem;
            right: 3rem;
            width: 70px;
            height: 70px;
            background: var(--accent);
            border-radius: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--dark);
            font-size: 2.2rem;
            text-decoration: none;
            box-shadow: 0 15px 30px rgba(251, 191, 36, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
        }

        .fab:hover {
            transform: scale(1.1) rotate(90deg);
            filter: brightness(1.1);
        }

        .filters-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 5px;
            margin-bottom: 0.5rem;
        }

        .filters-wrapper::-webkit-scrollbar {
            height: 4px;
        }

        .filters-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            padding: 2px;
            min-width: max-content;
        }

        @media (max-width: 900px) {
            .charts-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .table-container {
                display: block;
            }

            /* Prevent DataTables from adding unnecessary horizontal scroll to the page */
            .dataTables_wrapper {
                padding: 0 5px;
            }

            table#sessionsTable,
            table#sessionsTable thead,
            table#sessionsTable tbody,
            table#sessionsTable tr,
            table#sessionsTable td {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box;
            }

            table#sessionsTable thead {
                display: none !important;
            }

            table#sessionsTable tr {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 1.25rem;
                margin-bottom: 1.5rem;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            table#sessionsTable td {
                padding: 0.75rem 0 !important;
                background: none !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
                min-height: 2.5rem;
            }

            table#sessionsTable td:last-child {
                border-bottom: none !important;
                margin-top: 1rem;
                padding-top: 1.25rem !important;
                justify-content: center !important;
                gap: 15px;
            }

            table#sessionsTable td::before {
                content: attr(data-label);
                font-weight: 700;
                color: rgba(255, 255, 255, 0.4);
                text-transform: uppercase;
                font-size: 0.65rem;
                letter-spacing: 1.5px;
                margin-right: 10px;
            }

            .sessions-section {
                padding: 1.25rem;
                border-radius: 24px;
                margin-top: 1.5rem;
            }

            .section-header {
                flex-direction: column;
                gap: 1.5rem;
                align-items: stretch;
            }

            .search-input {
                width: auto;
                /* Allow items to shrink in scroll view */
                font-size: 0.9rem;
                padding: 0.7rem 1rem;
            }

            #tableSearch {
                width: 100% !important;
                margin-top: 10px;
            }
        }

        @media (max-width: 600px) {
            main {
                padding: 1rem;
            }

            .stat-card .value {
                font-size: 2.5rem;
            }

            .fab {
                bottom: 2rem;
                right: 2rem;
                width: 60px;
                height: 60px;
            }
        }

        .action-cell {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            text-decoration: none;
            font-size: 1.2rem;
            opacity: 0.7;
            transition: 0.3s;
        }

        .btn-action:hover {
            opacity: 1;
            transform: scale(1.2);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .search-input {
            padding: 0.8rem 1.2rem;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 160px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        select.search-input {
            appearance: none;
            padding-right: 2.5rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(255,255,255,0.5)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.2rem;
        }

        .search-input:hover {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 15px rgba(96, 165, 250, 0.2);
        }

        /* Desktop layout for search results */
        #tableSearch {
            width: 200px;
            cursor: text;
        }

        .btn-reset {
            padding: 0.8rem 1.5rem;
            background: rgba(248, 113, 113, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-reset:hover {
            background: rgba(248, 113, 113, 0.25);
            border-color: #f87171;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(248, 113, 113, 0.2);
        }

        .btn-reset:active {
            transform: translateY(0);
        }

        .sessions-section {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 2.5rem;
            margin-top: 2rem;
            border: 1px solid var(--glass-border);
        }

        footer {
            text-align: center;
            padding: 3rem 1rem;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
            border-top: 1px solid var(--glass-border);
            margin-top: 4rem;
            backdrop-filter: blur(10px);
            background: rgba(15, 23, 42, 0.4);
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>Yolundayam</h1>
        <p>Your Driving Journey Statistics</p>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="form_entry.php" class="nav-link" style="color: var(--accent);">+ New Session</a>
        </nav>
    </header>

    <main class="container">
        <section class="stats-overview">
            <article class="stat-card">
                <h3>Total Distance</h3>
                <div class="value"><?php echo number_format($manager->getTotalDistance(), 1); ?><span
                        class="unit">km</span></div>
            </article>
            <article class="stat-card">
                <h3>Total Sessions</h3>
                <div class="value"><?php echo count($experiences); ?></div>
            </article>
            <article class="stat-card">
                <h3>Avg. Fatigue</h3>
                <div class="value"><?php echo number_format($manager->getAverageFatigue(), 1); ?></div>
            </article>
        </section>

        <section class="charts-section">
            <article class="card">
                <div class="card-title">Weather Conditions</div>
                <div class="chart-container">
                    <canvas id="weatherChart" height="150"></canvas>
                </div>
            </article>
            <article class="card">
                <div class="card-title">Mileage Evolution</div>
                <div class="chart-container">
                    <canvas id="evolutionChart" height="150"></canvas>
                </div>
            </article>
        </section>

        <section class="sessions-section">
            <div class="section-header">
                <h2 class="card-title" style="margin-bottom: 0;">Recent Sessions</h2>
                <div class="filters-wrapper">
                    <div class="filter-group">
                        <select id="weatherFilter" class="search-input">
                            <option value="">All Weather</option>
                            <?php foreach ($weatherList as $w): ?>
                                <option value="<?php echo htmlspecialchars($w['label']); ?>">
                                    <?php echo htmlspecialchars($w['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="trafficFilter" class="search-input">
                            <option value="">All Traffic</option>
                            <?php foreach ($trafficList as $t): ?>
                                <option value="<?php echo htmlspecialchars($t['label']); ?>">
                                    <?php echo htmlspecialchars($t['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="fatigueFilter" class="search-input">
                            <option value="">All Fatigue</option>
                            <?php foreach ($fatigueLevels as $f): ?>
                                <option value="Level <?php echo htmlspecialchars($f['label']); ?>">Level
                                    <?php echo htmlspecialchars($f['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="manoeuvreFilter" class="search-input">
                            <option value="">All Manoeuvres</option>
                            <?php foreach ($manoeuvreList as $m): ?>
                                <option value="<?php echo htmlspecialchars($m['label']); ?>">
                                    <?php echo htmlspecialchars($m['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div style="position: relative; width: 200px;">
                            <input type="text" id="tableSearch" placeholder="Search..." class="search-input"
                                style="width: 100%; padding-right: 2.5rem;">
                            <span
                                style="position: absolute; right: 0.8rem; top: 50%; transform: translateY(-50%); opacity: 0.5; pointer-events: none; filter: grayscale(1);">🔍</span>
                        </div>
                        <button type="button" id="clearFilters" class="btn-reset">
                            <span>🧹</span> Clear
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="sessionsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Distance</th>
                            <th>Fatigue</th>
                            <th>Traffic</th>
                            <th>Manoeuvres</th>
                            <th>Weather</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experiences as $exp): ?>
                            <tr class="session-row" data-weather="<?php echo htmlspecialchars($exp->weatherLabel ?? ''); ?>"
                                data-traffic="<?php echo htmlspecialchars($exp->trafficLabel ?? ''); ?>"
                                data-fatigue="Level <?php echo htmlspecialchars($exp->fatigueLabel ?? ''); ?>"
                                data-manoeuvres="<?php echo htmlspecialchars(implode(', ', $exp->manoeuvres ?? [])); ?>">
                                <td data-label="Date" data-order="<?php echo $exp->date; ?>">
                                    <strong><?php echo $exp->getFormattedDate(); ?></strong></td>
                                <td data-label="Time" data-order="<?php echo $exp->startTime; ?>">
                                    <?php echo date('H:i', strtotime($exp->startTime)); ?> -
                                    <?php echo date('H:i', strtotime($exp->endTime)); ?>
                                </td>
                                <td data-label="Distance"><?php echo number_format($exp->mileage, 1); ?> km</td>
                                <td data-label="Fatigue">
                                    <?php
                                    $fLevel = (int) ($exp->fatigueLabel ?? 1);
                                    $icons = ['', '😊', '🙂', '😐', '😫', '😴'];
                                    $fClass = "fatigue-" . min(5, max(1, $fLevel));
                                    ?>
                                    <div class="fatigue-badge <?php echo $fClass; ?>">
                                        <span class="fatigue-icon"><?php echo $icons[$fLevel] ?? '😐'; ?></span>
                                        <span>Level <?php echo $fLevel; ?></span>
                                    </div>
                                </td>
                                <td data-label="Traffic"><span
                                        class="badge badge-weather"><?php echo htmlspecialchars($exp->trafficLabel ?? 'Normal'); ?></span>
                                </td>
                                <td data-label="Manoeuvres">
                                    <?php if (!empty($exp->manoeuvres)): ?>
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                            <?php foreach ($exp->manoeuvres as $m): ?>
                                                <span class="badge badge-weather"
                                                    style="font-size: 0.7rem; padding: 0.2rem 0.5rem;"><?php echo htmlspecialchars($m); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="opacity: 0.5;">None</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Weather"><span
                                        class="badge badge-weather"><?php echo htmlspecialchars($exp->weatherLabel ?? 'Clear'); ?></span>
                                </td>
                                <td class="action-cell">
                                    <a href="details.php?id=<?php echo SessionAnonymizer::getCode($exp->id); ?>"
                                        class="btn-action" title="View Details">👁️</a>
                                    <a href="edit_entry.php?id=<?php echo SessionAnonymizer::getCode($exp->id); ?>"
                                        class="btn-action" title="Edit Session">✏️</a>
                                    <a href="#"
                                        onclick="deleteSession('<?php echo SessionAnonymizer::getCode($exp->id); ?>')"
                                        class="btn-action" title="Delete Session" style="color: #fca5a5;">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Yolundayam - Supervised Driving Excellence Dashboard. Built with Semantic
            HTML, Flexbox, and Grid.</p>
    </footer>

    <a href="form_entry.php" class="fab">+</a>

    <script>
        if (document.getElementById('weatherChart')) {
            const weatherCtx = document.getElementById('weatherChart').getContext('2d');
            new Chart(weatherCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($weatherLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($weatherCounts); ?>,
                        backgroundColor: ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#a78bfa'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                font: { family: 'Outfit', size: 12 },
                                padding: 20
                            }
                        }
                    }
                }
            });
        }

        // Cumulative Evolution Data
        <?php
        $sortedExp = array_reverse($experiences); // Oldest first
        $labels = [];
        $cumulative = [];
        $runningTotal = 0;
        foreach ($sortedExp as $e) {
            $labels[] = date('M d', strtotime($e->date));
            $runningTotal += $e->mileage;
            $cumulative[] = $runningTotal;
        }
        ?>
        const evolutionLabels = <?php echo json_encode($labels); ?>;
        const evolutionData = <?php echo json_encode($cumulative); ?>;

        if (document.getElementById('evolutionChart') && evolutionLabels.length > 0) {
            new Chart(document.getElementById('evolutionChart'), {
                type: 'line',
                data: {
                    labels: evolutionLabels,
                    datasets: [{
                        label: 'Cumulative km',
                        data: evolutionData,
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.5)' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: 'rgba(255, 255, 255, 0.5)' }
                        }
                    }
                }
            });
        }

        // Initialize DataTables & Custom Filters
        $(document).ready(function () {
            const table = $('#sessionsTable').DataTable({
                dom: 'lrtip', // Hide default search
                pageLength: 10,
                order: [[0, 'desc'], [1, 'desc']], // Sort by date and time (newest first)
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [7] } // Disable sorting on actions
                ],
                language: {
                    emptyTable: "No sessions recorded yet.",
                    zeroRecords: "🔍 No matching sessions found for your filters."
                }
            });

            window.applyFilters = function () {
                const weather = $('#weatherFilter').val();
                const traffic = $('#trafficFilter').val();
                const fatigue = $('#fatigueFilter').val();
                const manoeuvre = $('#manoeuvreFilter').val();
                const search = $('#tableSearch').val().toLowerCase();

                // Using DataTables search API for global search
                table.search(search).draw();

                // Custom filter for dropdowns
                $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                    const row = $(table.row(dataIndex).node());
                    const rowWeather = row.data('weather');
                    const rowTraffic = row.data('traffic');
                    const rowFatigue = row.data('fatigue');
                    const rowManoeuvres = row.data('manoeuvres') || "";

                    const matchWeather = !weather || rowWeather === weather;
                    const matchTraffic = !traffic || rowTraffic === traffic;
                    const matchFatigue = !fatigue || rowFatigue === fatigue;
                    const matchManoeuvre = !manoeuvre || rowManoeuvres.includes(manoeuvre);

                    return matchWeather && matchTraffic && matchFatigue && matchManoeuvre;
                });

                table.draw();
                $.fn.dataTable.ext.search.pop(); // Clear specific filters after draw
            };

            // Attach events
            $('#tableSearch').on('keyup', applyFilters);
            $('#weatherFilter, #trafficFilter, #fatigueFilter, #manoeuvreFilter').on('change', applyFilters);

            $('#clearFilters').on('click', function () {
                $('#tableSearch').val('');
                $('#weatherFilter, #trafficFilter, #fatigueFilter, #manoeuvreFilter').val('');
                applyFilters();
            });
        });

        function deleteSession(id) {
            if (confirm('Are you sure you want to delete this session?')) {
                window.location.href = 'delete_entry.php?id=' + id;
            }
        }
    </script>
</body>

</html>