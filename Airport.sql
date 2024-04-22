CREATE DATABASE IF NOT EXISTS AIRPORT;
USE AIRPORT;

-- Create the Passenger table
CREATE TABLE Passenger (
    passportID INT PRIMARY KEY,
    FullName VARCHAR(50),
    nationality VARCHAR(30),
    dob DATE,
    phoneNumber VARCHAR(20)
);

-- Create the Airlines table
CREATE TABLE Airlines(
    airlineID INT PRIMARY KEY,
    name VARCHAR(30),
    FleetSize INT,
    countryOfOrigin VARCHAR(30)
);

-- Create the Flight table
CREATE TABLE Flight (
	flightName VARCHAR(30),
    flightID INT PRIMARY KEY,
    departureTime DATETIME,
    arrivalTime DATETIME,
    departureLoc VARCHAR(30),
    arrivalLoc VARCHAR(30),
    distance INT,
    AirlineID INT,
    FOREIGN KEY (AirlineID) REFERENCES Airlines(airlineID) ON DELETE CASCADE
);

-- Create the PassengerToFlight table
CREATE TABLE PassengerToFlight (
    passportID INT,
    flightID INT,
    PRIMARY KEY (passportID, flightID),
    FOREIGN KEY (flightID) REFERENCES Flight(flightID) ON DELETE CASCADE,
    FOREIGN KEY (passportID) REFERENCES Passenger(passportID) ON DELETE CASCADE
);

-- Create the Luggage table
CREATE TABLE Luggage (
    luggageId INT PRIMARY KEY,
    weight INT,
    dimensions VARCHAR(30),
    fragile CHAR(1),
    checkInTime DATETIME,
    passportID INT,
    flightID INT,
    FOREIGN KEY (passportID) REFERENCES Passenger(passportID) ON DELETE CASCADE,
    FOREIGN KEY (flightID) REFERENCES Flight(flightID) ON DELETE CASCADE
);

-- Create the Crew table
CREATE TABLE Crew(
    crewMemberId INT PRIMARY KEY,
    fullName VARCHAR(30),
    nationality VARCHAR(30),
    dob DATE
);

-- Create the FlightToCrew table
CREATE TABLE FlightToCrew (
    flightID INT,
    crewMemberId INT,
    PRIMARY KEY (flightID, crewMemberId),
    FOREIGN KEY (flightID) REFERENCES Flight(flightID) ON DELETE CASCADE,
    FOREIGN KEY (crewMemberId) REFERENCES Crew(crewMemberId) ON DELETE CASCADE
);

-- Create the Airplane table
CREATE TABLE Airplane(
    id INT PRIMARY KEY,
    airlineId INT,
    model VARCHAR(30),
    capacity INT, 
    lastMaintenanceDate DATE,
    flightID INT,
    FOREIGN KEY (flightID) REFERENCES Flight(flightID) ON DELETE CASCADE,
    FOREIGN KEY (airlineId) REFERENCES Airlines(airlineID) ON DELETE CASCADE
);

-- Create the Gates table
CREATE TABLE Gates (
    gateId INT PRIMARY KEY,
    location VARCHAR(150),
    status VARCHAR(30),
    terminal VARCHAR(30),
    flightID INT,
    FOREIGN KEY (flightID) REFERENCES Flight(flightID) ON DELETE CASCADE
);

-- Create the AirportFacility table
CREATE TABLE AirportFacility (
    facilityId INT PRIMARY KEY,
    name VARCHAR(30),
    type VARCHAR(30),
    location VARCHAR(150),
    operatingHours VARCHAR(100),
    maintenanceSchedule DATE
);

-- Create the SeatAssignment table
CREATE TABLE SeatAssignment (
    flightID INT,
    passengerID INT,
    seatNumber VARCHAR(10),
    PRIMARY KEY (flightID, passengerID),
    FOREIGN KEY (flightID) REFERENCES Flight(flightID) ON DELETE CASCADE,
    FOREIGN KEY (passengerID) REFERENCES Passenger(passportID) ON DELETE CASCADE
);

-- Creating necessary indexes
CREATE INDEX idx_flight_airlineID ON Flight(AirlineID);
CREATE INDEX idx_passenger_nationality ON Passenger(nationality);
CREATE INDEX idx_luggage_flightID ON Luggage(flightID);
CREATE INDEX idx_crew_nationality ON Crew(nationality);
CREATE INDEX idx_facility_type ON AirportFacility(type);
CREATE INDEX idx_flight_times ON Flight(departureTime, arrivalTime);

-- Creating views
CREATE VIEW UpcomingFlightsForPassenger AS
SELECT f.flightID, f.departureTime, f.arrivalTime
FROM Flight f
JOIN PassengerToFlight pf ON f.flightID = pf.flightID
WHERE pf.passportID = 1234 AND f.departureTime > NOW(); 

CREATE VIEW TotalLuggageWeightForFlight AS
SELECT flightID, SUM(weight) AS TotalWeight
FROM Luggage
GROUP BY flightID;

CREATE VIEW MaintenanceSchedule AS
SELECT facilityId, name, maintenanceSchedule
FROM AirportFacility
WHERE maintenanceSchedule >= CURDATE(); 

CREATE VIEW FlightPassengerSeats AS
SELECT 
    f.flightID, 
    p.passportID, 
    p.FullName, 
    sa.seatNumber
FROM 
    Flight f
JOIN 
    PassengerToFlight ptf ON f.flightID = ptf.flightID
JOIN 
    Passenger p ON p.passportID = ptf.passportID
JOIN 
    SeatAssignment sa ON p.passportID = sa.passengerID AND f.flightID = sa.flightID
WHERE
    f.departureTime > NOW();
