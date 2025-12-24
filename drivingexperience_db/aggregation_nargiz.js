// Denormalization pipeline to embed all related data
db.DrivingExperience.aggregate([
  // Join with user data
  {
    $lookup: {
      from: "Users",
      localField: "idUser",
      foreignField: "idUser",
      as: "user"
    }
  },
  { $unwind: "$user" },
  
  // Join with weather
  {
    $lookup: {
      from: "WeatherCondition",
      localField: "idWeather",
      foreignField: "idWeather",
      as: "weather"
    }
  },
  { $unwind: "$weather" },
  
  // Join with fatigue level
  {
    $lookup: {
      from: "FatigueLevel",
      localField: "idFatigue",
      foreignField: "idFatigue",
      as: "fatigue"
    }
  },
  { $unwind: "$fatigue" },
  
  // Join with supervisor
  {
    $lookup: {
      from: "Supervisor",
      localField: "idSupervisor",
      foreignField: "idSupervisor",
      as: "supervisor"
    }
  },
  { $unwind: "$supervisor" },
  
  // Join with road conditions (many-to-many)
  {
    $lookup: {
      from: "DrivingExp_roadCondition",
      localField: "idDrivingExp",
      foreignField: "idDrivingExp",
      as: "roadLinks"
    }
  },
  {
    $lookup: {
      from: "RoadCondition",
      localField: "roadLinks.idRoadCondition",
      foreignField: "idRoadCondition",
      as: "roadConditions"
    }
  },
  
  // Join with traffic conditions (many-to-many)
  {
    $lookup: {
      from: "DrivingExp_trafficCondition",
      localField: "idDrivingExp",
      foreignField: "idDrivingExp",
      as: "trafficLinks"
    }
  },
  {
    $lookup: {
      from: "TrafficCondition",
      localField: "trafficLinks.idTraffic",
      foreignField: "idTraffic",
      as: "trafficConditions"
    }
  },
  
  // Join with manoeuvres (many-to-many)
  {
    $lookup: {
      from: "DrivingExp_manouvre",
      localField: "idDrivingExp",
      foreignField: "idDrivingExp",
      as: "manouvreLinks"
    }
  },
  {
    $lookup: {
      from: "Manouvre",
      localField: "manouvreLinks.idManouvre",
      foreignField: "idManouvre",
      as: "manouvres"
    }
  },
  
  // Calculate duration
  {
    $addFields: {
      durationMinutes: {
        $divide: [
          { $subtract: [
            { $toDate: { $concat: ["1970-01-01T", "$endTime", "Z"] } },
            { $toDate: { $concat: ["1970-01-01T", "$startTime", "Z"] } }
          ] },
          60000
        ]
      }
    }
  },
  
  // Group by user and push all experiences
  {
    $group: {
      _id: "$user.idUser",
      userDetails: {
        $first: {
          name: "$user.name",
          surname: "$user.surname",
          patronymic: "$user.patronymic",
          age: "$user.age"
        }
      },
      drivingExperiences