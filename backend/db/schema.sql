-- Database schema for Yolundayam (Supervised Driving Experience)

CREATE TABLE weather (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL
);

CREATE TABLE road_condition (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL
);

CREATE TABLE traffic_condition (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL
);

CREATE TABLE manoeuvre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL
);

CREATE TABLE supervisor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    fathers_name VARCHAR(100)
);

CREATE TABLE driving_experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    mileage DECIMAL(10, 2) NOT NULL,
    fatigue INT NOT NULL,
    user_id INT,
    supervisor_id INT,
    weather_id INT,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (supervisor_id) REFERENCES supervisor(id),
    FOREIGN KEY (weather_id) REFERENCES weather(id)
);

-- Junction table for many-to-many relationship with road conditions
CREATE TABLE driving_exp_road_condition (
    driving_exp_id INT,
    road_condition_id INT,
    PRIMARY KEY (driving_exp_id, road_condition_id),
    FOREIGN KEY (driving_exp_id) REFERENCES driving_experience(id) ON DELETE CASCADE,
    FOREIGN KEY (road_condition_id) REFERENCES road_condition(id) ON DELETE CASCADE
);

-- Junction table for many-to-many relationship with manoeuvres
CREATE TABLE driving_exp_manoeuvre (
    driving_exp_id INT,
    manoeuvre_id INT,
    PRIMARY KEY (driving_exp_id, manoeuvre_id),
    FOREIGN KEY (driving_exp_id) REFERENCES driving_experience(id) ON DELETE CASCADE,
    FOREIGN KEY (manoeuvre_id) REFERENCES manoeuvre(id) ON DELETE CASCADE
);

-- Junction table for many-to-many relationship with traffic conditions (optional based on requirements, but often useful)
CREATE TABLE driving_exp_traffic_condition (
    driving_exp_id INT,
    traffic_condition_id INT,
    PRIMARY KEY (driving_exp_id, traffic_condition_id),
    FOREIGN KEY (driving_exp_id) REFERENCES driving_experience(id) ON DELETE CASCADE,
    FOREIGN KEY (traffic_condition_id) REFERENCES traffic_condition(id) ON DELETE CASCADE
);

-- Initial Data
INSERT INTO weather (label) VALUES ('Sunny'), ('Cloudy'), ('Rainy'), ('Snowy'), ('Windy'), ('Foggy');
INSERT INTO road_condition (label) VALUES ('Dry'), ('Wet'), ('Icy'), ('Gravel'), ('Under Construction'), ('Paved');
INSERT INTO traffic_condition (label) VALUES ('Light'), ('Moderate'), ('Heavy'), ('Standstill');
INSERT INTO manoeuvre (label) VALUES ('Parking'), ('U-turn'), ('Highway merging'), ('Emergency braking'), ('Roundabout'), ('Overtaking'), ('Reverse driving'), ('Lane change');
INSERT INTO supervisor (name) VALUES ('John Doe'), ('Jane Smith'), ('Robert Brown'), ('Emily Davis'), ('Michael Wilson');
INSERT INTO user (name, surname, fathers_name) VALUES ('Nargiz', 'Heydarova', 'Heydar');
