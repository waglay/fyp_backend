-- generate mysql query
-- ```
-- table member, COLUMNs = [email,hight, PASSWORD, weight, phone, ADDRESS, name, MEMBER_id]
-- table gym, column = [gym_id, gym_name, gym_address, gym_phone, gym_email, gym_photos]
-- table payment, column = [payment_id, payment_date, payment_amount, payment_type, payment_status, member_id, gym_id]
-- table report, COLUMNs =[date, report_id]
-- ```


CREATE TABLE member (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    member_name VARCHAR(255) NOT NULL,
    member_email VARCHAR(255) NOT NULL,
    member_PASSWORD VARCHAR(255) NOT NULL,
    member_phone VARCHAR(20),
    member_ADDRESS VARCHAR(255),
    member_hight DECIMAL(5,2),
    member_weight DECIMAL(5,2)
);

CREATE TABLE gym (
    gym_id INT AUTO_INCREMENT PRIMARY KEY,
    gym_name VARCHAR(255) NOT NULL,
    gym_address VARCHAR(255),
    gym_phone VARCHAR(20),
    gym_email VARCHAR(255),
    gym_photos TEXT
);

CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_date DATE NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_type VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) NOT NULL,
    member_id INT,
    gym_id INT,
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (gym_id) REFERENCES gym(gym_id)
);

CREATE TABLE report (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL
);

-- Index for the 'member' table
CREATE INDEX idx_member_email ON member(member_email);
CREATE INDEX idx_member_phone ON member(member_phone);
CREATE INDEX idx_member_name ON member(member_name);

-- Index for the 'gym' table
CREATE INDEX idx_gym_name ON gym(gym_name);
CREATE INDEX idx_gym_address ON gym(gym_address);
CREATE INDEX idx_gym_phone ON gym(gym_phone);
CREATE INDEX idx_gym_email ON gym(gym_email);

-- Index for the 'payment' table
CREATE INDEX idx_payment_member_id ON payment(member_id);
CREATE INDEX idx_payment_gym_id ON payment(gym_id);
CREATE INDEX idx_payment_date ON payment(payment_date);
CREATE INDEX idx_payment_status ON payment(payment_status);

-- Index for the 'report' table
CREATE INDEX idx_report_date ON report(date);
