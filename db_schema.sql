-- MySQL Database Schema for River side Cottage Booking System

-- Create database
CREATE DATABASE IF NOT EXISTS luxevista_resort;
USE luxevista_resort;

-- Table for room types
CREATE TABLE IF NOT EXISTS room_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    capacity INT NOT NULL,
    amenities TEXT,
    image_url VARCHAR(255)
);

-- Table for room images
CREATE TABLE IF NOT EXISTS room_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT,
    image_url VARCHAR(255),
    caption VARCHAR(255),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
);

-- Table for rooms
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL,
    room_type_id INT,
    status ENUM('available', 'booked', 'maintenance') DEFAULT 'available',
    FOREIGN KEY (room_type_id) REFERENCES room_types(id)
);

-- Table for users/customers
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for bookings
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guests INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    special_requests TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Insert sample room types (2 rooms as specified)
INSERT INTO room_types (name, description, price, capacity, amenities, image_url) VALUES
('Deluxe River View Suite', 'Experience stunning views of the river from your private balcony. This spacious suite includes a king-size bed, luxury bathroom, and all modern amenities.', 150.00, 2, 'King bed, Private balcony, River view, Mini bar, WiFi, TV, Air conditioning', 'images/room-1.jpg'),
('Garden View Room', 'Relax in our comfortable garden view room with beautiful views of our tropical gardens. Perfect for couples or solo travelers.', 100.00, 2, 'Queen bed, Garden view, WiFi, TV, Air conditioning, Mini fridge', 'images/room-2.jpg');

-- Insert sample room images
INSERT INTO room_images (room_type_id, image_url, caption) VALUES
(1, 'images/room-1.jpg', 'Deluxe River View Suite - Main View'),
(1, 'images/room-1-balcony.jpg', 'Private Balcony with River View'),
(1, 'images/room-1-bathroom.jpg', 'Luxury Bathroom'),
(1, 'images/room-1-bedroom.jpg', 'King Size Bedroom'),
(2, 'images/room-2.jpg', 'Garden View Room - Main View'),
(2, 'images/room-2-garden.jpg', 'View of Tropical Gardens'),
(2, 'images/room-2-bathroom.jpg', 'Modern Bathroom'),
(2, 'images/room-2-bedroom.jpg', 'Cozy Bedroom');

-- Insert sample rooms
INSERT INTO rooms (room_number, room_type_id, status) VALUES
('101', 1, 'available'),
('201', 2, 'available');

-- Sample user
INSERT INTO users (first_name, last_name, email, phone) VALUES
('John', 'Doe', 'john.doe@example.com', '+1234567890');