DELIMITER $ $ CREATE DEFINER = `root` @`localhost` PROCEDURE `GenerateAttendanceReport`(IN `fromDate` DATE, IN `toDate` DATE) BEGIN -- Create temporary table to generate numbers
CREATE TEMPORARY TABLE IF NOT EXISTS Numbers (i INT);

-- Populate temporary table with numbers from 0 to DATEDIFF(toDate, fromDate)
SET
    @max_number = DATEDIFF(toDate, fromDate);

SET
    @counter = 0;

WHILE @counter <= @max_number DO
INSERT INTO
    Numbers
VALUES
    (@counter);

SET
    @counter = @counter + 1;

END WHILE;

-- Create temporary table for dates
CREATE TEMPORARY TABLE IF NOT EXISTS Dates (attendance_date DATE);

-- Populate temporary table with dates from fromDate to toDate
INSERT INTO
    Dates
SELECT
    fromDate + INTERVAL i DAY AS attendance_date
FROM
    Numbers
WHERE
    fromDate + INTERVAL i DAY <= toDate;

-- Your remaining query
SELECT
    u.id as user_id,
    CONCAT(u.first_name, ' ', u.last_name) AS user_name,
    u.role_id,
    u.district_id,
    att.shift_type_id,
    u.employee_id,
    districts.district_name,
    roles.role_name,
    roles.role_slug,
    shift_types.shift_name,
    att.login_time,
    att.login_location,
    att.logout_time,
    att.logout_location,
    att.login_meter_reading,
    att.logout_meter_reading,
    att.duration,
    att.km_run,
    lv.status,
    lv.leave_reason,
    lv.leave_type_id,
    CASE
        WHEN hl.date IS NOT NULL THEN 'Holiday'
        WHEN lv.from_date IS NOT NULL THEN COALESCE(lv.status, 'Leave')
        WHEN att.attendance_date IS NOT NULL THEN 'Present'
        ELSE 'Absent'
    END AS attendance_status,
    d.attendance_date AS attendance_date
FROM
    users u
    CROSS JOIN Dates d
    LEFT JOIN holidays hl ON hl.date = d.attendance_date
    LEFT JOIN leaves lv ON u.id = lv.user_id
    AND d.attendance_date BETWEEN lv.from_date
    AND lv.to_date
    LEFT JOIN attendances att ON u.id = att.user_id
    AND d.attendance_date = att.attendance_date
    LEFT JOIN roles ON u.role_id = roles.id
    LEFT JOIN districts ON u.district_id = districts.id
    LEFT JOIN shift_types ON shift_types.id = att.shift_type_id
WHERE
    (
        u.created_at <= d.attendance_date
        AND d.attendance_date <= toDate
    )
    AND d.attendance_date >= fromDate
    OR (
        u.created_at < d.attendance_date
        AND d.attendance_date < CURDATE()
        AND d.attendance_date >= fromDate
    )
ORDER BY
    d.attendance_date;

-- Drop temporary tables
DROP TEMPORARY TABLE IF EXISTS Numbers;

DROP TEMPORARY TABLE IF EXISTS Dates;

END $ $ DELIMITER;