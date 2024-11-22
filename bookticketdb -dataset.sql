
--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`fname`, `lname`, `email`, `password`) VALUES
( 'Tatum', 'Drake', 'taturm@gmail.com', ''),
('Leroy', 'Mcgee', 'leroy@gmail.com', ''),
('Katell', 'Machonald', 'katell@gmail.com', ''),
('Sarah', 'Walton', 'sarah@gmail.com', ''),
('Emery', 'West', 'west@gmail.com', ''),
('Gill', 'Conway', 'gill@gmail.com', '');



--
-- Dumping data for table `flight`


INSERT INTO `flight` (`flightname`, `departure_location`, `destination_location`) VALUES
('JQ240', 'Auckland', 'Sydney'),
('MU779', 'Auckland', 'Shanghai'),
('NZ941', 'Auckland', 'Christchurch'),
('QF143', 'Wellington', 'Queenstown'),
('QF8526', 'Auckland', 'Paris'),
('SQ285', 'Auckland', 'Singapore');





-- Dumping data for table `ticket`


INSERT INTO `ticket` (`flightcode`, `customerID`, `departure_Date`, `arrival_Date`, `price`, `seat_Options`) VALUES
(6, 1, '2024-03-15', '2024-08-16', 2999, 'Basic economy'),
(3, 4, '2024-03-01', '2024-08-02', 1399, 'Economy Plus'),
(4, 3, '2024-03-07', '2024-08-07', 99, 'Premium Plus'),
(1, 6, '2024-03-14', '2024-08-14', 399, NULL),
(6, 3, '2024-03-15', '2024-08-16', 2999, NULL);

