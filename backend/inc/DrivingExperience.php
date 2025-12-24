<?php
/**
 * DrivingExperience Entity Class
 * Represents a single driving session.
 */
class DrivingExperience
{
    public $id;
    public $date;
    public $startTime;
    public $endTime;
    public $mileage;
    public $idWeather;
    public $idFatigue;
    public $weatherLabel;
    public $fatigueLabel;
    public $trafficLabel;
    public $roadConditions = [];
    public $manoeuvres = [];

    public function __construct($data = [])
    {
        $this->id = $data['idDrivingExp'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->startTime = $data['startTime'] ?? null;
        $this->endTime = $data['endTime'] ?? null;
        $this->mileage = $data['mileage'] ?? 0;
        $this->idWeather = $data['idWeather'] ?? null;
        $this->idFatigue = $data['idFatigue'] ?? null;
        $this->weatherLabel = $data['weather_label'] ?? null;
        $this->fatigueLabel = $data['fatigue_label'] ?? null;
        $this->trafficLabel = $data['traffic_label'] ?? null;
        $this->roadConditions = $data['road_conditions'] ?? [];
        $this->manoeuvres = $data['manoeuvres'] ?? [];
    }

    /**
     * Formats the duration of the session.
     */
    public function getDuration()
    {
        if (!$this->startTime || !$this->endTime)
            return "N/A";
        $start = new DateTime($this->startTime);
        $end = new DateTime($this->endTime);
        $diff = $start->diff($end);
        return $diff->format('%h h %i min');
    }

    /**
     * Formats the date for display.
     */
    public function getFormattedDate()
    {
        return date('M d, Y', strtotime($this->date));
    }
}
