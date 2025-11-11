-- ============================================
-- Polovni Automobili Style Categories
-- Cars Only Fork - Hierarchical Structure
-- ============================================

-- Clear existing categories
TRUNCATE TABLE categories;

-- ============================================
-- LEVEL 1: MAIN CATEGORIES (Putnička vozila focus)
-- ============================================

-- 1. AUTOMOBILES BY BRAND (Main Category)
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(1, 'Putnička vozila', 'passenger-vehicles', 'Svi putnički automobili', NULL, 'car', 1, 1);

-- ============================================
-- LEVEL 2: BODY TYPES (Karoserija)
-- ============================================

INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
-- Body Types
(10, 'Limuzina', 'limousine', 'Sedan - 4 vrata plus prtljažnik', 1, 'car-side', 1, 1),
(11, 'Hečbek', 'hatchback', 'Hatchback - 3 ili 5 vrata', 1, 'car-rear', 2, 1),
(12, 'Karavan', 'station-wagon', 'Station Wagon - produženi prtljažnik', 1, 'car-estate', 3, 1),
(13, 'Kupe', 'coupe', 'Coupe - sportski stil, 2 vrata', 1, 'car-sport', 4, 1),
(14, 'Kabriolet/Roadster', 'convertible', 'Convertible - otvoren krov', 1, 'car-convertible', 5, 1),
(15, 'Monovolumen (MiniVan)', 'minivan', 'Minivan - porodični automobil', 1, 'car-minivan', 6, 1),
(16, 'Džip/SUV', 'suv', 'SUV - terenski i visoki', 1, 'car-suv', 7, 1),
(17, 'Pickup', 'pickup', 'Pickup - otvoreni sanduk', 1, 'car-pickup', 8, 1);

-- ============================================
-- LEVEL 3: CAR BRANDS (Grouped by popularity)
-- ============================================

-- Premium Brands
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(100, 'Audi', 'audi', 'Audi automobili', 1, 'brand-audi', 10, 1),
(101, 'BMW', 'bmw', 'BMW automobili', 1, 'brand-bmw', 11, 1),
(102, 'Mercedes Benz', 'mercedes-benz', 'Mercedes Benz automobili', 1, 'brand-mercedes', 12, 1),
(103, 'Volkswagen', 'volkswagen', 'Volkswagen automobili', 1, 'brand-vw', 13, 1),
(104, 'Porsche', 'porsche', 'Porsche automobili', 1, 'brand-porsche', 14, 1),

-- Popular Brands
(110, 'Toyota', 'toyota', 'Toyota automobili', 1, 'brand-toyota', 20, 1),
(111, 'Honda', 'honda', 'Honda automobili', 1, 'brand-honda', 21, 1),
(112, 'Nissan', 'nissan', 'Nissan automobili', 1, 'brand-nissan', 22, 1),
(113, 'Mazda', 'mazda', 'Mazda automobili', 1, 'brand-mazda', 23, 1),
(114, 'Hyundai', 'hyundai', 'Hyundai automobili', 1, 'brand-hyundai', 24, 1),
(115, 'Kia', 'kia', 'Kia automobili', 1, 'brand-kia', 25, 1),

-- European Brands
(120, 'Peugeot', 'peugeot', 'Peugeot automobili', 1, 'brand-peugeot', 30, 1),
(121, 'Renault', 'renault', 'Renault automobili', 1, 'brand-renault', 31, 1),
(122, 'Citroen', 'citroen', 'Citroen automobili', 1, 'brand-citroen', 32, 1),
(123, 'Fiat', 'fiat', 'Fiat automobili', 1, 'brand-fiat', 33, 1),
(124, 'Opel', 'opel', 'Opel automobili', 1, 'brand-opel', 34, 1),
(125, 'Ford', 'ford', 'Ford automobili', 1, 'brand-ford', 35, 1),
(126, 'Seat', 'seat', 'Seat automobili', 1, 'brand-seat', 36, 1),
(127, 'Škoda', 'skoda', 'Škoda automobili', 1, 'brand-skoda', 37, 1),

-- Luxury Brands
(130, 'Lexus', 'lexus', 'Lexus automobili', 1, 'brand-lexus', 40, 1),
(131, 'Jaguar', 'jaguar', 'Jaguar automobili', 1, 'brand-jaguar', 41, 1),
(132, 'Land Rover', 'land-rover', 'Land Rover automobili', 1, 'brand-landrover', 42, 1),
(133, 'Volvo', 'volvo', 'Volvo automobili', 1, 'brand-volvo', 43, 1),

-- American Brands
(140, 'Chevrolet', 'chevrolet', 'Chevrolet automobili', 1, 'brand-chevrolet', 50, 1),
(141, 'Jeep', 'jeep', 'Jeep automobili', 1, 'brand-jeep', 51, 1),
(142, 'Dodge', 'dodge', 'Dodge automobili', 1, 'brand-dodge', 52, 1),
(143, 'Chrysler', 'chrysler', 'Chrysler automobili', 1, 'brand-chrysler', 53, 1),

-- Asian Brands
(150, 'Suzuki', 'suzuki', 'Suzuki automobili', 1, 'brand-suzuki', 60, 1),
(151, 'Mitsubishi', 'mitsubishi', 'Mitsubishi automobili', 1, 'brand-mitsubishi', 61, 1),
(152, 'Subaru', 'subaru', 'Subaru automobili', 1, 'brand-subaru', 62, 1),
(153, 'Dacia', 'dacia', 'Dacia automobili', 1, 'brand-dacia', 63, 1),

-- Chinese Brands (Growing market)
(160, 'Chery', 'chery', 'Chery automobili', 1, 'brand-chery', 70, 1),
(161, 'Geely', 'geely', 'Geely automobili', 1, 'brand-geely', 71, 1),
(162, 'BYD', 'byd', 'BYD električn automobili', 1, 'brand-byd', 72, 1),
(163, 'Great Wall', 'great-wall', 'Great Wall automobili', 1, 'brand-greatwall', 73, 1),

-- Electric/New Energy
(170, 'Tesla', 'tesla', 'Tesla električni automobili', 1, 'brand-tesla', 80, 1),
(171, 'Polestar', 'polestar', 'Polestar električni automobili', 1, 'brand-polestar', 81, 1),
(172, 'MINI', 'mini', 'MINI automobili', 1, 'brand-mini', 82, 1),

-- Italian Brands
(180, 'Alfa Romeo', 'alfa-romeo', 'Alfa Romeo automobili', 1, 'brand-alfaromeo', 90, 1),
(181, 'Lancia', 'lancia', 'Lancia automobili', 1, 'brand-lancia', 91, 1),
(182, 'Ferrari', 'ferrari', 'Ferrari sportski automobili', 1, 'brand-ferrari', 92, 1),
(183, 'Lamborghini', 'lamborghini', 'Lamborghini sportski automobili', 1, 'brand-lamborghini', 93, 1),
(184, 'Maserati', 'maserati', 'Maserati luksuzni automobili', 1, 'brand-maserati', 94, 1);

-- ============================================
-- LEVEL 4: POPULAR MODELS (Examples for top brands)
-- ============================================

-- Volkswagen Models
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(1030, 'Golf', 'vw-golf', 'VW Golf - sve generacije', 103, 'model', 1, 1),
(1031, 'Passat', 'vw-passat', 'VW Passat - sve generacije', 103, 'model', 2, 1),
(1032, 'Polo', 'vw-polo', 'VW Polo - kompaktni', 103, 'model', 3, 1),
(1033, 'Tiguan', 'vw-tiguan', 'VW Tiguan SUV', 103, 'model', 4, 1),
(1034, 'Touareg', 'vw-touareg', 'VW Touareg veliki SUV', 103, 'model', 5, 1),
(1035, 'T-Roc', 'vw-t-roc', 'VW T-Roc kompaktni SUV', 103, 'model', 6, 1);

-- BMW Models
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(1010, 'Serija 3', 'bmw-3-series', 'BMW Serija 3', 101, 'model', 1, 1),
(1011, 'Serija 5', 'bmw-5-series', 'BMW Serija 5', 101, 'model', 2, 1),
(1012, 'X1', 'bmw-x1', 'BMW X1 kompaktni SUV', 101, 'model', 3, 1),
(1013, 'X3', 'bmw-x3', 'BMW X3 SUV', 101, 'model', 4, 1),
(1014, 'X5', 'bmw-x5', 'BMW X5 veliki SUV', 101, 'model', 5, 1);

-- Mercedes Models
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(1020, 'C-Klasa', 'mercedes-c-class', 'Mercedes C-Klasa', 102, 'model', 1, 1),
(1021, 'E-Klasa', 'mercedes-e-class', 'Mercedes E-Klasa', 102, 'model', 2, 1),
(1022, 'A-Klasa', 'mercedes-a-class', 'Mercedes A-Klasa', 102, 'model', 3, 1),
(1023, 'GLA', 'mercedes-gla', 'Mercedes GLA kompaktni SUV', 102, 'model', 4, 1),
(1024, 'GLC', 'mercedes-glc', 'Mercedes GLC SUV', 102, 'model', 5, 1);

-- Audi Models
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(1000, 'A3', 'audi-a3', 'Audi A3 kompaktni', 100, 'model', 1, 1),
(1001, 'A4', 'audi-a4', 'Audi A4 srednja klasa', 100, 'model', 2, 1),
(1002, 'A6', 'audi-a6', 'Audi A6 poslovna klasa', 100, 'model', 3, 1),
(1003, 'Q3', 'audi-q3', 'Audi Q3 kompaktni SUV', 100, 'model', 4, 1),
(1004, 'Q5', 'audi-q5', 'Audi Q5 SUV', 100, 'model', 5, 1),
(1005, 'Q7', 'audi-q7', 'Audi Q7 veliki SUV', 100, 'model', 6, 1);

-- ============================================
-- ADDITIONAL FILTER CATEGORIES
-- ============================================

-- Special Categories (for filtering)
INSERT INTO categories (id, name, slug, description, parent_id, icon, display_order, is_active) VALUES
(200, 'Novi automobili', 'new-cars', 'Neregistrovani novi automobili', NULL, 'sparkles', 100, 1),
(201, 'Prvi vlasnik', 'first-owner', 'Automobili prvog vlasnika', NULL, 'user-check', 101, 1),
(202, 'Kupljen nov u Srbiji', 'bought-new-serbia', 'Kupljeni novi u Srbiji', NULL, 'flag', 102, 1),
(203, 'Na kredit', 'on-credit', 'Automobili dostupni na kredit', NULL, 'credit-card', 103, 1),
(204, 'Sa garancijom', 'with-warranty', 'Automobili sa garancijom', NULL, 'shield-check', 104, 1),
(205, 'Za zamenu', 'for-exchange', 'Automobili za zamenu', NULL, 'repeat', 105, 1),
(206, 'Električni', 'electric', 'Električni automobili', NULL, 'zap', 106, 1),
(207, 'Hibridni', 'hybrid', 'Hibridni automobili', NULL, 'battery', 107, 1);

-- Reset AUTO_INCREMENT
ALTER TABLE categories AUTO_INCREMENT = 2000;

