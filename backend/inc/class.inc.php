<?php
// class.inc.php - Logic for Managing Driving Experiences
date_default_timezone_set('Asia/Baku');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'DB.inc.php';
require_once 'DrivingExperience.php';

class DrivingExperienceManager
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    // --- Lookup Data ---
// Updated based on foreign key names in drivingExperience table: idWeather, idFatigue, etc.

    // --- Lookup Data ---
    public function getWeatherList()
    {
        return $this->db->query("SELECT idWeather as id, weather as label FROM weatherCondition")->fetchAll();
    }

    public function getRoadConditionList()
    {
        return $this->db->query("SELECT idRoadCondition as id, road as label FROM roadCondition")->fetchAll();
    }

    public function getTrafficConditionList()
    {
        return $this->db->query("SELECT idTraffic as id, traffic as label FROM trafficCondition")->fetchAll();
    }

    public function getManoeuvreList()
    {
        return $this->db->query("SELECT idManouvre as id, manouvreType as label FROM manouvre")->fetchAll();
    }

    public function getFatigueLevels()
    {
        return $this->db->query("SELECT idFatigue as id, fatigueLevel as label FROM fatigueLevel")->fetchAll();
    }


    // --- Driving Experience Operations ---

    public function saveExperience($data, $manoeuvres, $road_conditions)
    {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO drivingExperience (date, startTime, endTime, mileage, idWeather, idFatigue)
VALUES (:date, :start_time, :end_time, :mileage, :weather_id, :fatigue_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':date' => $data['date'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':mileage' => $data['mileage'],
                ':weather_id' => $data['weather_id'],
                ':fatigue_id' => $data['fatigue_id']
            ]);

            $experienceId = $this->db->lastInsertId();

            // Save traffic condition to junction table (single selection logic)
            if (!empty($data['traffic_id'])) {
                $stmtTraffic = $this->db->prepare("INSERT INTO drivingExp_trafficCondition (idDrivingExp, idTraffic) VALUES (?, ?)");
                $stmtTraffic->execute([$experienceId, $data['traffic_id']]);
            }

            // Save many-to-many manoeuvres
            if (!empty($manoeuvres)) {
                $stmtMan = $this->db->prepare("INSERT INTO drivingExp_manouvre (idDrivingExp, idManouvre) VALUES (?, ?)");
                foreach ($manoeuvres as $manId) {
                    $stmtMan->execute([$experienceId, $manId]);
                }
            }

            // Save many-to-many road conditions
            if (!empty($road_conditions)) {
                $stmtRoad = $this->db->prepare("INSERT INTO drivingExp_roadCondition (idDrivingExp, idRoadCondition) VALUES (?, ?)");
                foreach ($road_conditions as $rcId) {
                    $stmtRoad->execute([$experienceId, $rcId]);
                }
            }


            $this->db->commit();
            return $experienceId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAllExperiences()
    {
        $sql = "SELECT de.*, w.weather as weather_label, f.fatigueLevel as fatigue_label, tc.traffic as traffic_label,
(SELECT GROUP_CONCAT(m.manouvreType SEPARATOR ', ') FROM manouvre m
JOIN drivingExp_manouvre dem ON m.idManouvre = dem.idManouvre
WHERE dem.idDrivingExp = de.idDrivingExp) as manoeuvres_list,
(SELECT GROUP_CONCAT(rc.road SEPARATOR ', ') FROM roadCondition rc
JOIN drivingExp_roadCondition derc ON rc.idRoadCondition = derc.idRoadCondition
WHERE derc.idDrivingExp = de.idDrivingExp) as road_conditions_list
FROM drivingExperience de
LEFT JOIN weatherCondition w ON de.idWeather = w.idWeather
LEFT JOIN fatigueLevel f ON de.idFatigue = f.idFatigue
LEFT JOIN drivingExp_trafficCondition detc ON de.idDrivingExp = detc.idDrivingExp
LEFT JOIN trafficCondition tc ON detc.idTraffic = tc.idTraffic
ORDER BY de.date DESC, de.startTime DESC";
        $results = $this->db->query($sql)->fetchAll();

        $experiences = [];
        foreach ($results as $row) {
            // Map the joined results to the object format expected by DrivingExperience
            if (isset($row['manoeuvres_list'])) {
                $row['manoeuvres'] = explode(', ', $row['manoeuvres_list']);
            }
            if (isset($row['road_conditions_list'])) {
                $row['road_conditions'] = explode(', ', $row['road_conditions_list']);
            }
            $experiences[] = new DrivingExperience($row);
        }
        return $experiences;
    }

    public function getExperienceDetails($id)
    {
        $stmt = $this->db->prepare("SELECT de.*, w.weather as weather_label, f.fatigueLevel as fatigue_label, tc.traffic as
traffic_label
FROM drivingExperience de
LEFT JOIN weatherCondition w ON de.idWeather = w.idWeather
LEFT JOIN fatigueLevel f ON de.idFatigue = f.idFatigue
LEFT JOIN drivingExp_trafficCondition detc ON de.idDrivingExp = detc.idDrivingExp
LEFT JOIN trafficCondition tc ON detc.idTraffic = tc.idTraffic
WHERE de.idDrivingExp = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            // Fetch many-to-many road conditions
            $stmtRoad = $this->db->prepare("SELECT rc.road as label FROM roadCondition rc
JOIN drivingExp_roadCondition derc ON rc.idRoadCondition = derc.idRoadCondition
WHERE derc.idDrivingExp = ?");
            $stmtRoad->execute([$id]);
            $row['road_conditions'] = $stmtRoad->fetchAll(PDO::FETCH_COLUMN);

            // Fetch many-to-many manoeuvres
            $stmtMan = $this->db->prepare("SELECT m.manouvreType as label FROM manouvre m
JOIN drivingExp_manouvre dem ON m.idManouvre = dem.idManouvre
WHERE dem.idDrivingExp = ?");
            $stmtMan->execute([$id]);
            $row['manoeuvres'] = $stmtMan->fetchAll(PDO::FETCH_COLUMN);

            return new DrivingExperience($row);
        }

        return null;
    }

    public function deleteExperience($id)
    {
        $this->db->beginTransaction();
        try {
            // Delete from junction tables first
            $this->db->prepare("DELETE FROM drivingExp_roadCondition WHERE idDrivingExp = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM drivingExp_manouvre WHERE idDrivingExp = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM drivingExp_trafficCondition WHERE idDrivingExp = ?")->execute([$id]);

            // Delete main record
            $stmt = $this->db->prepare("DELETE FROM drivingExperience WHERE idDrivingExp = ?");
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateExperience($id, $data, $manoeuvres, $road_conditions)
    {
        $this->db->beginTransaction();

        try {
            $sql = "UPDATE drivingExperience SET
date = :date,
startTime = :start_time,
endTime = :end_time,
mileage = :mileage,
idWeather = :weather_id,
idFatigue = :fatigue_id
WHERE idDrivingExp = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':date' => $data['date'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':mileage' => $data['mileage'],
                ':weather_id' => $data['weather_id'],
                ':fatigue_id' => $data['fatigue_id'],
                ':id' => $id
            ]);

            // Update traffic (delete old, insert new)
            $this->db->prepare("DELETE FROM drivingExp_trafficCondition WHERE idDrivingExp = ?")->execute([$id]);
            if (!empty($data['traffic_id'])) {
                $stmtTraffic = $this->db->prepare("INSERT INTO drivingExp_trafficCondition (idDrivingExp, idTraffic) VALUES (?, ?)");
                $stmtTraffic->execute([$id, $data['traffic_id']]);
            }

            // Update road conditions
            $this->db->prepare("DELETE FROM drivingExp_roadCondition WHERE idDrivingExp = ?")->execute([$id]);
            if (!empty($road_conditions)) {
                $stmtRoad = $this->db->prepare("INSERT INTO drivingExp_roadCondition (idDrivingExp, idRoadCondition) VALUES (?, ?)");
                foreach ($road_conditions as $rcId) {
                    $stmtRoad->execute([$id, $rcId]);
                }
            }

            // Update manoeuvres
            $this->db->prepare("DELETE FROM drivingExp_manouvre WHERE idDrivingExp = ?")->execute([$id]);
            if (!empty($manoeuvres)) {
                $stmtMan = $this->db->prepare("INSERT INTO drivingExp_manouvre (idDrivingExp, idManouvre) VALUES (?, ?)");
                foreach ($manoeuvres as $mId) {
                    $stmtMan->execute([$id, $mId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getTotalDistance()
    {
        return $this->db->query("SELECT SUM(mileage) FROM drivingExperience")->fetchColumn();
    }

    public function getWeatherStats()
    {
        return $this->db->query("SELECT w.weather as label, COUNT(*) as count
FROM drivingExperience de
JOIN weatherCondition w ON de.idWeather = w.idWeather
GROUP BY w.idWeather")->fetchAll();
    }

    public function getAverageFatigue()
    {
        $avg = $this->db->query("SELECT AVG(idFatigue) FROM drivingExperience")->fetchColumn();
        return $avg ? $avg : 0;
    }
}

/**
 * SessionAnonymizer - Securely maps database IDs to temporary session codes.
 */
class SessionAnonymizer
{
    public static function getCode($id)
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['anonymized_ids']))
            $_SESSION['anonymized_ids'] = [];
        $code = array_search($id, $_SESSION['anonymized_ids']);
        if ($code === false) {
            $code = bin2hex(random_bytes(8));
            $_SESSION['anonymized_ids'][$code] = $id;
        }
        return $code;
    }

    public static function getId($code)
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['anonymized_ids']))
            return null;
        return $_SESSION['anonymized_ids'][$code] ?? null;
    }
}
?>