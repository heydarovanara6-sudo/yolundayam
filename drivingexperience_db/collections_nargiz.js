db = {
  Users: [
    {
      "idUser": 1,
      "name": "Nargiz",
      "surname": "Heydarova",
      "patronymic": "Zahir",
      "age": 25
    }
  ],
  
  DrivingExperience: [
    // Experience 1
    {
      "idDrivingExp": 1,
      "startTime": "08:00:00",
      "endTime": "08:45:00",
      "mileage": 25.5,
      "date": "2023-01-01",
      "idUser": 1,
      "idWeather": 1,
      "idFatigue": 1,
      "idSupervisor": 1
    },
    // Experience 2-30 (follow same pattern)
    {
      "idDrivingExp": 2,
      "startTime": "09:15:00",
      "endTime": "10:30:00",
      "mileage": 42.3,
      "date": "2023-01-05",
      "idUser": 1,
      "idWeather": 2,
      "idFatigue": 2,
      "idSupervisor": 2
    }
    // Continue with all 30 experiences...
  ],
  
  WeatherCondition: [
    { "idWeather": 1, "weather": "Sunny" },
    { "idWeather": 2, "weather": "Rainy" },
    { "idWeather": 3, "weather": "Cloudy" },
    { "idWeather": 4, "weather": "Foggy" },
    { "idWeather": 5, "weather": "Windy" },
    { "idWeather": 6, "weather": "Snowy" }
  ],
  
  RoadCondition: [
    { "idRoadCondition": 1, "road": "Dry" },
    { "idRoadCondition": 2, "road": "Wet" },
    { "idRoadCondition": 3, "road": "Icy" },
    { "idRoadCondition": 4, "road": "Muddy" },
    { "idRoadCondition": 5, "road": "Bumpy" },
    { "idRoadCondition": 6, "road": "Smooth" }
  ],
  
  TrafficCondition: [
    { "idTraffic": 1, "traffic": "Light" },
    { "idTraffic": 2, "traffic": "Medium" },
    { "idTraffic": 3, "traffic": "Heavy" },
    { "idTraffic": 4, "traffic": "Congested" },
    { "idTraffic": 5, "traffic": "Stop-and-go" }
  ],
  
  FatigueLevel: [
    { "idFatigue": 1, "fatigueLevel": 1 },
    { "idFatigue": 2, "fatigueLevel": 2 },
    { "idFatigue": 3, "fatigueLevel": 3 },
    { "idFatigue": 4, "fatigueLevel": 4 },
    { "idFatigue": 5, "fatigueLevel": 5 }
  ],
  
  Supervisor: [
    { "idSupervisor": 1, "supervisorName": "John Smith" },
    { "idSupervisor": 2, "supervisorName": "Emily Brown" },
    { "idSupervisor": 3, "supervisorName": "Michael Johnson" },
    { "idSupervisor": 4, "supervisorName": "Sarah Wilson" },
    { "idSupervisor": 5, "supervisorName": "David Lee" }
  ],
  
  Manouvre: [
    { "idManouvre": 1, "manouvreType": "LTurn" },
    { "idManouvre": 2, "manouvreType": "RTurn" },
    { "idManouvre": 3, "manouvreType": "Park" },
    { "idManouvre": 4, "manouvreType": "Merge" },
    { "idManouvre": 5, "manouvreType": "LaneC" },
    { "idManouvre": 6, "manouvreType": "Utrn" },
    { "idManouvre": 7, "manouvreType": "Ovtak" },
    { "idManouvre": 8, "manouvreType": "Brk" }
  ],
  
  DrivingExp_roadCondition: [
    { "idDrivingExp": 1, "idRoadCondition": 1 },
    { "idDrivingExp": 1, "idRoadCondition": 6 },
    // Continue with all road condition associations...
  ],
  
  DrivingExp_trafficCondition: [
    { "idDrivingExp": 1, "idTraffic": 1 },
    // Continue with all traffic condition associations...
  ],
  
  DrivingExp_manouvre: [
    { "idDrivingExp": 1, "idManouvre": 1 },
    { "idDrivingExp": 1, "idManouvre": 2 },
    { "idDrivingExp": 1, "idManouvre": 3 },
    // Continue with all manoeuvre associations...
  ]
}