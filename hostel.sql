-- =============================================
-- CONSOLIDATED SQL FILE FOR HOSTEL MANAGEMENT SYSTEM
-- =============================================
-- This file combines all SQL files in the proper order
-- with fixes for common issues.

-- Disable foreign key checks and strict mode temporarily
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =============================================
-- DROP ALL EXISTING TABLES
-- =============================================
-- This ensures a clean slate

DROP TABLE IF EXISTS `roommate_room_agreements`;
DROP TABLE IF EXISTS `joint_bookings`;
DROP TABLE IF EXISTS `solo_match_queue`;
DROP TABLE IF EXISTS `booking_workflow`;
DROP TABLE IF EXISTS `roommate_requests`;
DROP TABLE IF EXISTS `roommate_matches`;
DROP TABLE IF EXISTS `student_preferences`;
DROP TABLE IF EXISTS `student_login`;
DROP TABLE IF EXISTS `hostelbookings`;
DROP TABLE IF EXISTS `complaints`;
DROP TABLE IF EXISTS `branches`;
DROP TABLE IF EXISTS `userregistration`;
DROP TABLE IF EXISTS `roomsdetails`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `state_master`;
DROP TABLE IF EXISTS `users`;

-- Drop all views
DROP VIEW IF EXISTS `student_profile_view`;
DROP VIEW IF EXISTS `compatibility_matrix_view`;
DROP VIEW IF EXISTS `solo_students_view`;
DROP VIEW IF EXISTS `available_rooms_view`;
DROP VIEW IF EXISTS `joint_booking_view`;
DROP VIEW IF EXISTS `agreed_room_bookings_view`;

-- =============================================
-- CORE TABLES (from hostel_db.sql and hostel_db_corrected.sql)
-- =============================================

-- Create users table (admin login)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(300) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Insert admin user
INSERT INTO `users` (`username`, `email`, `password`, `entry_date`) 
VALUES ('admin', 'admin@mail.com', '21232f297a57a5a743894a0e4a801fc3', '2020-09-08 20:31:45');

-- Create complaints table
CREATE TABLE `complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `reg_no` varchar(255) NOT NULL,
  `room_no` int(11) NOT NULL,
  `complaint` text NOT NULL,
  `status` enum('pending','in-progress','resolved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create courses table
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(255) NOT NULL,
  `course_sn` varchar(255) NOT NULL,
  `course_fn` varchar(255) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create roomsdetails table
CREATE TABLE `roomsdetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seater` int(11) NOT NULL,
  `room_no` int(11) NOT NULL,
  `fees` int(11) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_no` (`room_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Create state_master table
CREATE TABLE `state_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `State` varchar(38) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Insert state data
INSERT INTO `state_master` (`id`, `State`) VALUES
(1, 'Andhra Pradesh'),
(2, 'Arunachal Pradesh'),
(3, 'Assam'),
(4, 'Bihar'),
(5, 'Chhattisgarh'),
(6, 'Goa'),
(7, 'Gujarat'),
(8, 'Haryana'),
(9, 'Himachal Pradesh'),
(10, 'Jharkhand'),
(11, 'Karnataka'),
(12, 'Kerala'),
(13, 'Madhya Pradesh'),
(14, 'Maharashtra'),
(15, 'Manipur'),
(16, 'Meghalaya'),
(17, 'Mizoram'),
(18, 'Nagaland'),
(19, 'Odisha'),
(20, 'Punjab'),
(21, 'Rajasthan'),
(22, 'Sikkim'),
(23, 'Tamil Nadu'),
(24, 'Telangana'),
(25, 'Tripura'),
(26, 'Uttarakhand'),
(27, 'Uttar Pradesh'),
(28, 'West Bengal'),
(29, 'Andaman & Nicobar'),
(30, 'Chandigarh'),
(31, 'Dadra and Nagar Haveli and Daman & Diu'),
(32, 'Delhi'),
(33, 'Jammu & Kashmir'),
(34, 'Lakshadweep'),
(35, 'Puducherry'),
(36, 'Ladakh');

-- Create userregistration table
CREATE TABLE `userregistration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `contact_no` bigint(20) NOT NULL,
  `emailid` varchar(255) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_no` (`registration_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create branches table with proper ID handling
CREATE TABLE `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(10) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_code` (`branch_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert branches data
INSERT INTO `branches` (`branch_code`, `branch_name`, `department`) VALUES
('CSE', 'Computer Science Engineering', 'Computer Science'),
('ECE', 'Electronics and Communication Engineering', 'Electronics'),
('EEE', 'Electrical and Electronics Engineering', 'Electrical'),
('ME', 'Mechanical Engineering', 'Mechanical'),
('CE', 'Civil Engineering', 'Civil'),
('IT', 'Information Technology', 'Computer Science'),
('EIE', 'Electronics and Instrumentation Engineering', 'Electronics'),
('CHE', 'Chemical Engineering', 'Chemical'),
('AE', 'Aeronautical Engineering', 'Aeronautical'),
('BME', 'Biomedical Engineering', 'Biomedical');

-- =============================================
-- ROOMMATE TABLES (from hostel_db_updated_roommate_booking.sql)
-- =============================================

-- Updated hostelbookings table with roommate booking features
CREATE TABLE `hostelbookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomno` int(11) NOT NULL,
  `seater` int(11) NOT NULL,
  `feespm` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `foodstatus` int(11) NOT NULL,
  `stayfrom` date NOT NULL,
  `duration` int(11) NOT NULL,
  `course` varchar(500) NOT NULL,
  `regno` varchar(255) NOT NULL,
  `firstName` varchar(500) NOT NULL,
  `lastName` varchar(500) NOT NULL,
  `gender` varchar(250) NOT NULL,
  `contactno` bigint(11) NOT NULL,
  `emailid` varchar(500) NOT NULL,
  `egycontactno` bigint(11) NOT NULL,
  `guardian_name` varchar(500) NOT NULL,
  `guardian_relation` varchar(500) NOT NULL,
  `guardian_contact` bigint(11) NOT NULL,
  `state` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(500) NOT NULL,
  `pin_code` int(11) NOT NULL,
  `booking_type` enum('solo','joint','matched') DEFAULT 'solo',
  `roommate_pair_id` int(11) NULL,
  `looking_for_roommate` tinyint(1) DEFAULT 1,
  `booking_status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `joint_booking_id` varchar(50) NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_roommate_pair` (`roommate_pair_id`),
  INDEX `idx_joint_booking` (`joint_booking_id`),
  INDEX `idx_booking_type` (`booking_type`),
  INDEX `idx_booking_status` (`booking_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Create student_preferences table
CREATE TABLE `student_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_no` varchar(255) NOT NULL,
  `lifestyle` enum('early-bird','night-owl','moderate') NOT NULL,
  `study_preference` enum('silent','music','discussion','flexible') NOT NULL,
  `noise_tolerance` enum('complete-silence','low-noise','moderate-noise','high-noise') NOT NULL,
  `cleanliness_level` enum('very-clean','clean','moderate','flexible') NOT NULL,
  `food_habit` enum('vegetarian','non-vegetarian','vegan','jain','flexible') NOT NULL,
  `sleep_schedule` enum('early-sleeper','late-sleeper','irregular','flexible') NOT NULL,
  `social_behavior` enum('introverted','extroverted','ambivert') NOT NULL,
  `smoking_drinking` enum('none','social','regular') NOT NULL DEFAULT 'none',
  `interests` text,
  `branch` varchar(100) NOT NULL,
  `year_of_study` enum('1','2','3','4','postgrad') NOT NULL,
  `preferred_branch_same` tinyint(1) DEFAULT 1,
  `preferred_year_same` tinyint(1) DEFAULT 0,
  `priority_preferences` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_no` (`reg_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create roommate_matches table
CREATE TABLE `roommate_matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student1_reg_no` varchar(255) NOT NULL,
  `student2_reg_no` varchar(255) NOT NULL,
  `match_score` decimal(5,2) NOT NULL,
  `match_factors` text,
  `status` enum('suggested','accepted','rejected','pending') DEFAULT 'suggested',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_match` (`student1_reg_no`, `student2_reg_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create roommate_requests table
CREATE TABLE `roommate_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requester_reg_no` varchar(255) NOT NULL,
  `requested_reg_no` varchar(255) NOT NULL,
  `message` text,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `request_type` enum('roommate_only','joint_booking') DEFAULT 'roommate_only',
  `preferred_room_type` int(11) NULL,
  `preferred_budget` int(11) NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_requester` (`requester_reg_no`),
  INDEX `idx_requested` (`requested_reg_no`),
  INDEX `idx_request_type` (`request_type`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Joint booking management table
CREATE TABLE `joint_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `joint_booking_id` varchar(50) NOT NULL,
  `student1_reg_no` varchar(255) NOT NULL,
  `student2_reg_no` varchar(255) NOT NULL,
  `roommate_pair_id` int(11) NOT NULL,
  `preferred_room_type` int(11) NULL,
  `preferred_budget_range` varchar(50) NULL,
  `booking_status` enum('initiated','room_selected','payment_pending','confirmed','cancelled') DEFAULT 'initiated',
  `selected_room` int(11) NULL,
  `total_amount` int(11) NULL,
  `booking_preferences` text NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `joint_booking_id` (`joint_booking_id`),
  INDEX `idx_roommate_pair` (`roommate_pair_id`),
  INDEX `idx_students` (`student1_reg_no`, `student2_reg_no`),
  INDEX `idx_selected_room` (`selected_room`),
  INDEX `idx_booking_status` (`booking_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Solo students looking for matches table
CREATE TABLE `solo_match_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_reg_no` varchar(255) NOT NULL,
  `hostel_booking_id` int(11) NOT NULL,
  `room_no` int(11) NOT NULL,
  `room_capacity` int(11) NOT NULL,
  `looking_for_match` tinyint(1) DEFAULT 1,
  `match_preferences` text NULL,
  `priority_score` decimal(5,2) DEFAULT 0,
  `urgent_booking` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_reg_no` (`student_reg_no`),
  INDEX `idx_room` (`room_no`),
  INDEX `idx_priority` (`priority_score`),
  INDEX `idx_looking` (`looking_for_match`),
  INDEX `idx_urgent` (`urgent_booking`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Booking workflow tracking
CREATE TABLE `booking_workflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` varchar(50) NOT NULL,
  `workflow_type` enum('solo_booking','joint_booking','match_later') NOT NULL,
  `student_reg_nos` text NOT NULL,
  `current_step` varchar(50) NOT NULL,
  `workflow_data` text NULL,
  `status` enum('in_progress','completed','cancelled') DEFAULT 'in_progress',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflow_id` (`workflow_id`),
  INDEX `idx_workflow_type` (`workflow_type`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table for storing roommate room agreements
CREATE TABLE `roommate_room_agreements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roommate_pair_id` int(11) NOT NULL,
  `student1_reg_no` varchar(255) NOT NULL,
  `student2_reg_no` varchar(255) NOT NULL,
  `agreed_room_no` int(11) NOT NULL,
  `student1_agreed` tinyint(1) DEFAULT 0,
  `student2_agreed` tinyint(1) DEFAULT 0,
  `agreement_status` enum('pending','agreed','cancelled','booked') DEFAULT 'pending',
  `room_type_preference` varchar(100) NULL,
  `max_budget` int(11) NULL,
  `preferred_floor` int(11) NULL,
  `special_requirements` text NULL,
  `agreed_at` timestamp NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_room_agreement` (`roommate_pair_id`, `agreed_room_no`),
  INDEX `idx_roommate_pair` (`roommate_pair_id`),
  INDEX `idx_room_no` (`agreed_room_no`),
  INDEX `idx_agreement_status` (`agreement_status`),
  INDEX `idx_students` (`student1_reg_no`, `student2_reg_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table for student login (for password change functionality)
CREATE TABLE `student_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_changed` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_no` (`registration_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- =============================================
-- VIEWS
-- =============================================

-- Student profile view
CREATE VIEW `student_profile_view` AS
SELECT 
    h.regno,
    CONCAT(h.firstName, ' ', h.lastName) as full_name,
    h.course,
    h.gender,
    h.contactno,
    h.emailid,
    h.roomno,
    sp.lifestyle,
    sp.study_preference,
    sp.noise_tolerance,
    sp.cleanliness_level,
    sp.food_habit,
    sp.sleep_schedule,
    sp.social_behavior,
    sp.interests,
    sp.branch,
    sp.year_of_study,
    b.branch_name,
    b.department
FROM hostelbookings h
LEFT JOIN student_preferences sp ON h.regno = sp.reg_no
LEFT JOIN branches b ON sp.branch = b.branch_code;

-- Compatibility matrix view
CREATE VIEW `compatibility_matrix_view` AS
SELECT 
    s1.reg_no as student1,
    s2.reg_no as student2,
    s1.branch as branch1,
    s2.branch as branch2,
    (
        CASE WHEN s1.lifestyle = s2.lifestyle THEN 15 ELSE 0 END +
        CASE WHEN s1.study_preference = s2.study_preference THEN 20 ELSE 0 END +
        CASE WHEN s1.noise_tolerance = s2.noise_tolerance THEN 15 ELSE 0 END +
        CASE WHEN s1.cleanliness_level = s2.cleanliness_level THEN 15 ELSE 0 END +
        CASE WHEN s1.food_habit = s2.food_habit THEN 10 ELSE 0 END +
        CASE WHEN s1.sleep_schedule = s2.sleep_schedule THEN 15 ELSE 0 END +
        CASE WHEN s1.social_behavior = s2.social_behavior THEN 5 ELSE 0 END +
        CASE WHEN s1.branch = s2.branch AND s1.preferred_branch_same = 1 THEN 5 ELSE 0 END
    ) as compatibility_score
FROM student_preferences s1
CROSS JOIN student_preferences s2
WHERE s1.reg_no != s2.reg_no;

-- View for solo students available for matching
CREATE VIEW `solo_students_view` AS
SELECT 
    smq.*,
    CONCAT(h.firstName, ' ', h.lastName) as student_name,
    h.course,
    h.gender,
    h.contactno,
    h.emailid,
    sp.lifestyle,
    sp.study_preference,
    sp.noise_tolerance,
    sp.cleanliness_level,
    sp.food_habit,
    sp.sleep_schedule,
    sp.social_behavior,
    sp.branch,
    sp.year_of_study,
    b.branch_name,
    rd.seater,
    rd.fees
FROM solo_match_queue smq
JOIN hostelbookings h ON smq.student_reg_no = h.regno
LEFT JOIN student_preferences sp ON smq.student_reg_no = sp.reg_no
LEFT JOIN branches b ON sp.branch = b.branch_code
LEFT JOIN roomsdetails rd ON smq.room_no = rd.room_no
WHERE smq.looking_for_match = 1;

-- View for available rooms with capacity
CREATE VIEW `available_rooms_view` AS
SELECT 
    rd.room_no,
    rd.seater,
    rd.fees,
    COUNT(h.id) as current_occupants,
    (rd.seater - COUNT(h.id)) as available_spaces,
    CASE 
        WHEN COUNT(h.id) = 0 THEN 'empty'
        WHEN COUNT(h.id) < rd.seater THEN 'partially_occupied'
        ELSE 'full'
    END as occupancy_status,
    GROUP_CONCAT(CONCAT(h.firstName, ' ', h.lastName) SEPARATOR ', ') as current_residents,
    GROUP_CONCAT(h.regno SEPARATOR ', ') as current_reg_nos
FROM roomsdetails rd
LEFT JOIN hostelbookings h ON rd.room_no = h.roomno AND h.booking_status = 'confirmed'
GROUP BY rd.room_no, rd.seater, rd.fees
ORDER BY available_spaces DESC, rd.fees ASC;

-- View for joint booking status
CREATE VIEW `joint_booking_view` AS
SELECT 
    jb.*,
    CONCAT(h1.firstName, ' ', h1.lastName) as student1_name,
    CONCAT(h2.firstName, ' ', h2.lastName) as student2_name,
    h1.contactno as student1_contact,
    h2.contactno as student2_contact,
    rm.match_score as compatibility_score,
    rd.seater as room_capacity,
    rd.fees as room_fees
FROM joint_bookings jb
JOIN hostelbookings h1 ON jb.student1_reg_no = h1.regno
JOIN hostelbookings h2 ON jb.student2_reg_no = h2.regno
LEFT JOIN roommate_matches rm ON jb.roommate_pair_id = rm.id
LEFT JOIN roomsdetails rd ON jb.selected_room = rd.room_no;

-- View for agreed room bookings ready for processing
CREATE VIEW `agreed_room_bookings_view` AS
SELECT 
    rra.*,
    CONCAT(u1.first_name, ' ', u1.last_name) as student1_name,
    CONCAT(u2.first_name, ' ', u2.last_name) as student2_name,
    u1.contact_no as student1_contact,
    u1.emailid as student1_email,
    u1.gender as student1_gender,
    u2.contact_no as student2_contact,
    u2.emailid as student2_email,
    u2.gender as student2_gender,
    rd.seater,
    rd.fees,
    rm.match_score as compatibility_score,
    sp1.branch as student1_branch,
    sp2.branch as student2_branch,
    b1.branch_name as student1_branch_name,
    b2.branch_name as student2_branch_name
FROM roommate_room_agreements rra
JOIN userregistration u1 ON rra.student1_reg_no = u1.registration_no
JOIN userregistration u2 ON rra.student2_reg_no = u2.registration_no
JOIN roomsdetails rd ON rra.agreed_room_no = rd.room_no
JOIN roommate_matches rm ON rra.roommate_pair_id = rm.id
LEFT JOIN student_preferences sp1 ON rra.student1_reg_no = sp1.reg_no
LEFT JOIN student_preferences sp2 ON rra.student2_reg_no = sp2.reg_no
LEFT JOIN branches b1 ON sp1.branch = b1.branch_code
LEFT JOIN branches b2 ON sp2.branch = b2.branch_code
WHERE rra.agreement_status = 'agreed'
ORDER BY rra.agreed_at ASC;

-- =============================================
-- STORED PROCEDURES AND TRIGGERS
-- =============================================

DELIMITER //

-- Trigger to add solo students to match queue when they book alone
CREATE TRIGGER `after_solo_booking_insert` 
AFTER INSERT ON `hostelbookings`
FOR EACH ROW
BEGIN
    IF NEW.booking_type = 'solo' AND NEW.looking_for_roommate = 1 THEN
        INSERT IGNORE INTO solo_match_queue 
        (student_reg_no, hostel_booking_id, room_no, room_capacity, urgent_booking)
        SELECT NEW.regno, NEW.id, NEW.roomno, NEW.seater, 
               CASE WHEN NEW.booking_status = 'confirmed' THEN 1 ELSE 0 END;
    END IF;
END//

-- Trigger to update match queue when booking is confirmed
CREATE TRIGGER `after_booking_status_update`
AFTER UPDATE ON `hostelbookings`
FOR EACH ROW
BEGIN
    IF NEW.booking_status = 'confirmed' AND OLD.booking_status != 'confirmed' THEN
        UPDATE solo_match_queue 
        SET urgent_booking = 1, priority_score = priority_score + 10
        WHERE student_reg_no = NEW.regno;
    END IF;
END//

-- Trigger to create joint booking record when roommate pair is accepted
CREATE TRIGGER `after_roommate_pair_created`
AFTER INSERT ON `roommate_matches`
FOR EACH ROW
BEGIN
    DECLARE joint_id VARCHAR(50);
    SET joint_id = CONCAT('JB', YEAR(NOW()), MONTH(NOW()), DAY(NOW()), '_', NEW.id);
    
    IF NEW.status = 'accepted' AND NEW.match_factors LIKE '%joint_booking%' THEN
        INSERT INTO joint_bookings 
        (joint_booking_id, student1_reg_no, student2_reg_no, roommate_pair_id, booking_status)
        VALUES (joint_id, NEW.student1_reg_no, NEW.student2_reg_no, NEW.id, 'initiated');
    END IF;
END//

-- Trigger to update agreement status when both students agree
CREATE TRIGGER `update_agreement_status` 
AFTER UPDATE ON `roommate_room_agreements`
FOR EACH ROW
BEGIN
    IF NEW.student1_agreed = 1 AND NEW.student2_agreed = 1 AND OLD.agreement_status = 'pending' THEN
        UPDATE roommate_room_agreements 
        SET agreement_status = 'agreed', agreed_at = NOW() 
        WHERE id = NEW.id;
    END IF;
END//

-- Stored procedure for finding compatible roommates for solo students
CREATE PROCEDURE `FindCompatibleRoommate`(IN student_reg_no VARCHAR(255))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE potential_roommate VARCHAR(255);
    DECLARE compatibility_score DECIMAL(5,2);
    DECLARE cur CURSOR FOR 
        SELECT 
            ssv.student_reg_no,
            COALESCE(cv.compatibility_score, 0) as score
        FROM solo_students_view ssv
        LEFT JOIN compatibility_matrix_view cv ON 
            (cv.student1 = student_reg_no AND cv.student2 = ssv.student_reg_no)
            OR (cv.student2 = student_reg_no AND cv.student1 = ssv.student_reg_no)
        WHERE ssv.student_reg_no != student_reg_no
        AND ssv.room_capacity > 1
        AND ssv.student_reg_no NOT IN (
            SELECT student1_reg_no FROM roommate_matches WHERE status = 'accepted'
            UNION
            SELECT student2_reg_no FROM roommate_matches WHERE status = 'accepted'
        )
        ORDER BY score DESC, ssv.created_at ASC
        LIMIT 5;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO potential_roommate, compatibility_score;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Insert suggestion into roommate_requests if score is decent
        IF compatibility_score >= 30 THEN
            INSERT IGNORE INTO roommate_requests 
            (requester_reg_no, requested_reg_no, message, status, request_type)
            VALUES 
            (potential_roommate, student_reg_no, 
             CONCAT('System suggested match based on ', compatibility_score, '% compatibility. Both students are looking for roommates.'), 
             'pending', 'roommate_only');
        END IF;
    END LOOP;
    
    CLOSE cur;
END//

-- Stored procedure for initiating joint booking
CREATE PROCEDURE `InitiateJointBooking`(
    IN student1_reg VARCHAR(255), 
    IN student2_reg VARCHAR(255), 
    IN pair_id INT,
    OUT joint_booking_id VARCHAR(50)
)
BEGIN
    DECLARE new_joint_id VARCHAR(50);
    SET new_joint_id = CONCAT('JB', YEAR(NOW()), LPAD(MONTH(NOW()),2,'0'), LPAD(DAY(NOW()),2,'0'), '_', pair_id);
    
    INSERT INTO joint_bookings 
    (joint_booking_id, student1_reg_no, student2_reg_no, roommate_pair_id, booking_status)
    VALUES (new_joint_id, student1_reg, student2_reg, pair_id, 'initiated');
    
    SET joint_booking_id = new_joint_id;
END//

DELIMITER ;

-- =============================================
-- AUTO_INCREMENT settings
-- =============================================

ALTER TABLE `complaints` AUTO_INCREMENT=1;
ALTER TABLE `courses` AUTO_INCREMENT=1;
ALTER TABLE `hostelbookings` AUTO_INCREMENT=1;