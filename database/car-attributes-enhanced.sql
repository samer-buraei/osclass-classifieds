-- ============================================
-- Enhanced Car Attributes
-- Based on Polovni Automobili Structure
-- ============================================

-- Add new columns to listings table for car-specific data
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `brand` VARCHAR(100) DEFAULT NULL AFTER `category_id`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `model` VARCHAR(100) DEFAULT NULL AFTER `brand`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `body_type` VARCHAR(50) DEFAULT NULL AFTER `model`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `year` INT DEFAULT NULL AFTER `body_type`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `mileage` INT DEFAULT NULL AFTER `year`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `fuel_type` VARCHAR(50) DEFAULT NULL AFTER `mileage`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `transmission` VARCHAR(50) DEFAULT NULL AFTER `fuel_type`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `engine_size` DECIMAL(3,1) DEFAULT NULL AFTER `transmission`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `power_hp` INT DEFAULT NULL AFTER `engine_size`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `power_kw` INT DEFAULT NULL AFTER `power_hp`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `drive_type` VARCHAR(50) DEFAULT NULL AFTER `power_kw`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `doors` TINYINT DEFAULT NULL AFTER `drive_type`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `seats` TINYINT DEFAULT NULL AFTER `doors`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `color_exterior` VARCHAR(50) DEFAULT NULL AFTER `seats`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `color_interior` VARCHAR(50) DEFAULT NULL AFTER `color_exterior`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `condition_vehicle` VARCHAR(50) DEFAULT NULL AFTER `color_interior`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `registration_valid` DATE DEFAULT NULL AFTER `condition_vehicle`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `first_owner` TINYINT(1) DEFAULT 0 AFTER `registration_valid`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `bought_new_serbia` TINYINT(1) DEFAULT 0 AFTER `first_owner`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `service_book` TINYINT(1) DEFAULT 0 AFTER `bought_new_serbia`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `warranty` TINYINT(1) DEFAULT 0 AFTER `service_book`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `warranty_months` INT DEFAULT NULL AFTER `warranty`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `available_credit` TINYINT(1) DEFAULT 0 AFTER `warranty_months`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `exchange_possible` TINYINT(1) DEFAULT 0 AFTER `available_credit`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `vin_number` VARCHAR(17) DEFAULT NULL AFTER `exchange_possible`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `emission_class` VARCHAR(20) DEFAULT NULL AFTER `vin_number`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `co2_emission` INT DEFAULT NULL AFTER `emission_class`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `consumption_city` DECIMAL(4,1) DEFAULT NULL AFTER `co2_emission`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `consumption_highway` DECIMAL(4,1) DEFAULT NULL AFTER `consumption_city`;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS `consumption_combined` DECIMAL(4,1) DEFAULT NULL AFTER `consumption_highway`;

-- Create table for car equipment/features
CREATE TABLE IF NOT EXISTS `car_equipment` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `listing_id` INT NOT NULL,
  `feature_code` VARCHAR(50) NOT NULL,
  `feature_name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  KEY `listing_id` (`listing_id`),
  KEY `feature_code` (`feature_code`),
  FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Equipment features reference data
-- Safety Features
INSERT INTO car_equipment (listing_id, feature_code, feature_name, category) VALUES
(0, 'ABS', 'ABS (Antiblocking sistem)', 'safety'),
(0, 'ESP', 'ESP (Elektronska kontrola stabilnosti)', 'safety'),
(0, 'ASR', 'ASR (Antiskid sistem)', 'safety'),
(0, 'AIRBAG_DRIVER', 'Airbag za vozača', 'safety'),
(0, 'AIRBAG_PASSENGER', 'Airbag za suvozača', 'safety'),
(0, 'AIRBAG_SIDE', 'Bočni airbag-ovi', 'safety'),
(0, 'AIRBAG_CURTAIN', 'Airbag zavese', 'safety'),
(0, 'ISOFIX', 'ISOFIX sistem', 'safety'),
(0, 'ALARM', 'Alarm', 'safety'),
(0, 'IMMOBILIZER', 'Immobilizer', 'safety'),
(0, 'CENTRAL_LOCK', 'Centralno zaključavanje', 'safety'),
(0, 'REMOTE_LOCK', 'Daljinsko zaključavanje', 'safety');

-- Comfort Features
INSERT INTO car_equipment (listing_id, feature_code, feature_name, category) VALUES
(0, 'AC', 'Klima', 'comfort'),
(0, 'AC_AUTO', 'Automatska klima', 'comfort'),
(0, 'AC_DUAL', 'Dual zone klima', 'comfort'),
(0, 'AC_4ZONE', '4-zonska klima', 'comfort'),
(0, 'HEATED_SEATS', 'Grejanje sedišta', 'comfort'),
(0, 'COOLED_SEATS', 'Hlađenje sedišta', 'comfort'),
(0, 'MASSAGE_SEATS', 'Masažna sedišta', 'comfort'),
(0, 'ELECTRIC_SEATS', 'Električno podešavanje sedišta', 'comfort'),
(0, 'MEMORY_SEATS', 'Memory sedišta', 'comfort'),
(0, 'LEATHER_SEATS', 'Kožna sedišta', 'comfort'),
(0, 'SPORT_SEATS', 'Sportska sedišta', 'comfort'),
(0, 'CRUISE_CONTROL', 'Tempomat', 'comfort'),
(0, 'CRUISE_ADAPTIVE', 'Adaptivni tempomat', 'comfort'),
(0, 'PARKING_SENSORS', 'Parking senzori', 'comfort'),
(0, 'PARKING_CAMERA', 'Kamera za parking', 'comfort'),
(0, 'PARKING_360', '360° kamera', 'comfort'),
(0, 'PARK_ASSIST', 'Automatsko parkiranje', 'comfort'),
(0, 'KEYLESS_ENTRY', 'Keyless ulazak', 'comfort'),
(0, 'START_STOP', 'Start-Stop sistem', 'comfort'),
(0, 'ELECTRIC_MIRROR', 'Električna podešavanje retrovizora', 'comfort'),
(0, 'HEATED_MIRROR', 'Grejanje retrovizora', 'comfort'),
(0, 'FOLDING_MIRROR', 'Automatsko sklapanje retrovizora', 'comfort');

-- Entertainment Features
INSERT INTO car_equipment (listing_id, feature_code, feature_name, category) VALUES
(0, 'RADIO', 'Radio', 'entertainment'),
(0, 'CD', 'CD player', 'entertainment'),
(0, 'MP3', 'MP3 player', 'entertainment'),
(0, 'USB', 'USB priključak', 'entertainment'),
(0, 'BLUETOOTH', 'Bluetooth', 'entertainment'),
(0, 'ANDROID_AUTO', 'Android Auto', 'entertainment'),
(0, 'APPLE_CARPLAY', 'Apple CarPlay', 'entertainment'),
(0, 'NAVIGATION', 'Navigacija', 'entertainment'),
(0, 'TOUCHSCREEN', 'Touch screen', 'entertainment'),
(0, 'SOUND_PREMIUM', 'Premium ozvučenje', 'entertainment'),
(0, 'TV', 'TV tuner', 'entertainment'),
(0, 'DVD', 'DVD player', 'entertainment');

-- Exterior Features
INSERT INTO car_equipment (listing_id, feature_code, feature_name, category) VALUES
(0, 'XENON', 'Xenon svetla', 'exterior'),
(0, 'LED', 'LED svetla', 'exterior'),
(0, 'LED_MATRIX', 'Matrix LED', 'exterior'),
(0, 'LASER', 'Laser svetla', 'exterior'),
(0, 'FOG_LIGHTS', 'Maglenke', 'exterior'),
(0, 'DAYTIME_LIGHTS', 'Dnevna svetla', 'exterior'),
(0, 'ROOF_RAILS', 'Krovni nosači', 'exterior'),
(0, 'PANORAMA', 'Panorama krov', 'exterior'),
(0, 'SUNROOF', 'Sunroof', 'exterior'),
(0, 'ALLOY_WHEELS', 'Alu felne', 'exterior'),
(0, 'TINTED_GLASS', 'Tonirana stakla', 'exterior'),
(0, 'RAIN_SENSOR', 'Senzor za kišu', 'exterior'),
(0, 'LIGHT_SENSOR', 'Senzor za svetlo', 'exterior'),
(0, 'ELECTRIC_TRUNK', 'Električno otvaranje gepeka', 'exterior'),
(0, 'TOW_HOOK', 'Kuka za vuču', 'exterior');

-- Technology Features
INSERT INTO car_equipment (listing_id, feature_code, feature_name, category) VALUES
(0, 'LANE_ASSIST', 'Lane Assist', 'technology'),
(0, 'BLIND_SPOT', 'Blind Spot detekcija', 'technology'),
(0, 'COLLISION_WARN', 'Upozorenje na sudar', 'technology'),
(0, 'AUTO_BRAKE', 'Automatsko kočenje', 'technology'),
(0, 'TRAFFIC_SIGN', 'Prepoznavanje saobraćajnih znakova', 'technology'),
(0, 'NIGHT_VISION', 'Night vision', 'technology'),
(0, 'HUD', 'Head-up display', 'technology'),
(0, 'DIGITAL_COCKPIT', 'Digitalni kokpit', 'technology'),
(0, 'WIRELESS_CHARGING', 'Bežično punjenje telefona', 'technology'),
(0, 'WIFI', 'WiFi hotspot', 'technology');

-- Create indexes for better search performance
CREATE INDEX idx_brand ON listings(brand);
CREATE INDEX idx_model ON listings(model);
CREATE INDEX idx_year ON listings(year);
CREATE INDEX idx_price ON listings(price);
CREATE INDEX idx_mileage ON listings(mileage);
CREATE INDEX idx_fuel_type ON listings(fuel_type);
CREATE INDEX idx_body_type ON listings(body_type);
CREATE INDEX idx_first_owner ON listings(first_owner);
CREATE INDEX idx_warranty ON listings(warranty);

-- Sample data (for testing)
-- Delete this in production
DELETE FROM car_equipment WHERE listing_id = 0;

