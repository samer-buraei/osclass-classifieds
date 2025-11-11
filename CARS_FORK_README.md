# 🚗 Cars Only Fork - Polovni Automobili Style

**Based on**: [Polovni Automobili](https://www.polovniautomobili.com/)  
**Focus**: Specialized classified ads platform for cars only

---

## 🎯 What's Different?

This fork specializes **exclusively in cars**, following the successful Polovni Automobili model from Serbia.

### ✅ Key Features

1. **🏢 Hierarchical Categories**
   - Body Types (Limousine, Hatchback, SUV, etc.)
   - Brands (100+ car manufacturers)
   - Popular Models (for top brands)
   - Special filters (First owner, New cars, etc.)

2. **🔍 Advanced Search**
   - Filter by brand, model, body type
   - Year range (1960 - 2026)
   - Price range
   - Mileage
   - Fuel type (Diesel, Petrol, Electric, Hybrid)
   - Transmission type
   - Drive type (FWD, RWD, AWD, 4WD)
   - Colors (exterior & interior)
   - Equipment/Features

3. **📋 Car-Specific Attributes**
   - Engine size & power (HP/kW)
   - VIN number
   - Registration status
   - First owner badge
   - Bought new in Serbia badge
   - Service book availability
   - Warranty information
   - Credit availability
   - Exchange possibility
   - Emission class (EURO standards)
   - Fuel consumption

4. **🛠️ Equipment Database**
   - **Safety**: ABS, ESP, Airbags, ISOFIX
   - **Comfort**: Climate control, Heated seats, Parking sensors
   - **Entertainment**: Navigation, Android Auto, Apple CarPlay
   - **Exterior**: LED/Xenon lights, Panorama roof, Alloy wheels
   - **Technology**: Lane assist, Blind spot, HUD

5. **💼 Dealer Features**
   - Business seller profiles
   - Dealer vs. Private listings
   - Multiple listing packages
   - Featured/Spotlight options

---

## 📁 New Files Created

### Database Files

#### `database/categories-cars-only.sql`
Complete hierarchical category structure:
- **8 Body Types** (Limuzina, Hečbek, Karavan, Kupe, Kabriolet, Monovolumen, Džip/SUV, Pickup)
- **80+ Car Brands** (Audi, BMW, Mercedes, VW, Toyota, Honda, etc.)
- **30+ Popular Models** (for top brands)
- **8 Special Filters** (New cars, First owner, Electric, etc.)

#### `database/car-attributes-enhanced.sql`
Enhanced car attributes system:
- **25+ new columns** in listings table
- **Car equipment table** with 70+ features
- **Search indexes** for performance
- Equipment categories (Safety, Comfort, Entertainment, etc.)

### Configuration Files

#### `config/cars-fork-config.php`
Complete configuration:
- Body types, fuel types, transmissions
- Colors, conditions, emission classes
- Top brands list
- Search settings
- Featured listing prices
- Quick filters
- Dealer types

---

## 📊 Category Hierarchy

```
Putnička vozila (Passenger Vehicles)
│
├── Body Types (Karoserija)
│   ├── Limuzina (Sedan)
│   ├── Hečbek (Hatchback)
│   ├── Karavan (Station Wagon)
│   ├── Kupe (Coupe)
│   ├── Kabriolet/Roadster (Convertible)
│   ├── Monovolumen (Minivan)
│   ├── Džip/SUV
│   └── Pickup
│
├── Brands (Marke)
│   ├── Premium (Audi, BMW, Mercedes, Porsche)
│   ├── Popular (Toyota, Honda, VW, Mazda)
│   ├── European (Peugeot, Renault, Fiat, Opel)
│   ├── Luxury (Lexus, Jaguar, Land Rover, Volvo)
│   ├── American (Jeep, Chevrolet, Dodge)
│   ├── Asian (Suzuki, Mitsubishi, Subaru)
│   ├── Chinese (Chery, BYD, Geely, Great Wall)
│   └── Electric (Tesla, Polestar)
│
└── Popular Models (for each brand)
    ├── VW: Golf, Passat, Polo, Tiguan
    ├── BMW: 3 Series, 5 Series, X1, X3, X5
    ├── Mercedes: C-Class, E-Class, GLA, GLC
    └── Audi: A3, A4, A6, Q3, Q5, Q7
```

---

## 🔧 Installation

### 1. Apply Database Changes

```bash
# Navigate to your project
cd C:\xampp\htdocs\osclass

# Import new categories
mysql -u root osclass_db < database/categories-cars-only.sql

# Import enhanced car attributes
mysql -u root osclass_db < database/car-attributes-enhanced.sql
```

### 2. Update Configuration

The car-specific configuration is in:
```
config/cars-fork-config.php
```

Load it in your application:
```php
$carsConfig = require 'config/cars-fork-config.php';
```

---

## 🎨 Key Differences from Original

| Feature | Original | Cars Fork |
|---------|----------|-----------|
| **Categories** | Multiple (Real Estate, Electronics, etc.) | Cars Only |
| **Brands** | N/A | 80+ car brands |
| **Models** | N/A | 100+ popular models |
| **Body Types** | N/A | 8 specific types |
| **Attributes** | Generic | 25+ car-specific |
| **Equipment** | Basic | 70+ features |
| **Filters** | Standard | Advanced car filters |
| **Search** | Basic | Multi-level hierarchical |

---

## 🚀 Features Based on Polovni Automobili

### 1. **Quick Filters (Brza Pretraga)**
- Najnoviji oglasi (Latest ads)
- Novi automobili (New cars)
- Prvi vlasnik (First owner)
- Kupljen nov u Srbiji (Bought new in Serbia)
- Na kredit (On credit)
- Sa garancijom (With warranty)
- Za zamenu (For exchange)
- Električni (Electric)
- Hibridni (Hybrid)

### 2. **Advanced Search Fields**
- Brand selection (dropdown)
- Model selection (dependent on brand)
- Body type (multi-select)
- Year range (from-to)
- Price range (from-to)
- Mileage range (from-to)
- Fuel type (multi-select)
- Transmission type
- Drive type (FWD/RWD/AWD/4WD)
- Engine size range
- Power range (HP or kW)
- Equipment features (checkboxes)

### 3. **Listing Card Display**
Each listing shows:
- **Large photo** (with gallery indicator)
- **Price** (EUR)
- **Brand & Model** (e.g., "Volkswagen Golf")
- **Year**
- **Mileage** (km)
- **Fuel type** icon
- **Transmission** icon
- **Engine size**
- **Location** (city)
- **Badges**: First owner, Warranty, Credit available
- **Seller type**: Dealer or Private

### 4. **Sorting Options**
- Date (newest first) - default
- Date (oldest first)
- Price (lowest first)
- Price (highest first)
- Mileage (lowest first)
- Mileage (highest first)

---

## 💡 Usage Examples

### Search by Brand and Model

```php
$listings = $listingModel->where(
    'brand = :brand AND model = :model AND status = :status',
    [
        'brand' => 'Volkswagen',
        'model' => 'Golf',
        'status' => 'active'
    ]
);
```

### Search with Multiple Filters

```php
$listings = $listingModel->where(
    'brand = :brand 
     AND year >= :year_from 
     AND year <= :year_to
     AND fuel_type = :fuel
     AND transmission = :trans
     AND first_owner = 1
     AND status = :status
     ORDER BY created_at DESC',
    [
        'brand' => 'Audi',
        'year_from' => 2018,
        'year_to' => 2023,
        'fuel' => 'diesel',
        'trans' => 'automatic',
        'status' => 'active'
    ]
);
```

### Get Cars with Specific Equipment

```php
$sql = "SELECT DISTINCT l.* 
        FROM listings l
        JOIN car_equipment ce ON l.id = ce.listing_id
        WHERE ce.feature_code IN ('LED', 'NAVIGATION', 'PARKING_CAMERA')
        AND l.status = 'active'
        GROUP BY l.id
        HAVING COUNT(DISTINCT ce.feature_code) = 3";
```

---

## 📝 Data Structure Example

### Sample Listing

```json
{
  "id": 1,
  "title": "Volkswagen Golf 7 GTI",
  "brand": "Volkswagen",
  "model": "Golf",
  "body_type": "hatchback",
  "year": 2019,
  "mileage": 45000,
  "fuel_type": "petrol",
  "transmission": "dct",
  "engine_size": 2.0,
  "power_hp": 230,
  "power_kw": 169,
  "drive_type": "fwd",
  "doors": 5,
  "seats": 5,
  "color_exterior": "red",
  "color_interior": "black",
  "condition_vehicle": "excellent",
  "first_owner": true,
  "bought_new_serbia": true,
  "service_book": true,
  "warranty": true,
  "warranty_months": 12,
  "available_credit": true,
  "exchange_possible": false,
  "emission_class": "euro6d",
  "price": 23500,
  "location": "Beograd",
  "equipment": [
    "LED", "NAVIGATION", "LEATHER_SEATS",
    "HEATED_SEATS", "PARKING_CAMERA", "CRUISE_ADAPTIVE"
  ]
}
```

---

## 🎯 Next Steps

1. **Import Data**
   ```bash
   mysql -u root osclass_db < database/categories-cars-only.sql
   mysql -u root osclass_db < database/car-attributes-enhanced.sql
   ```

2. **Update Templates**
   - Modify search forms to include car filters
   - Update listing cards with car-specific info
   - Add equipment checkboxes

3. **Test Advanced Search**
   - Brand/model filtering
   - Equipment filtering
   - Multi-criteria search

4. **Add Sample Data**
   - Create test listings with full car attributes
   - Add equipment to listings

---

## 📚 Documentation

- **Original**: See `ARCHITECTURE.md`
- **Car Fork**: This file
- **Database Schema**: `database/schema.sql` + new car tables
- **Configuration**: `config/cars-fork-config.php`

---

## 🌐 Inspiration

This fork is based on the successful structure of:
- **[Polovni Automobili](https://www.polovniautomobili.com/)** - Serbia's largest car classifieds platform

Key features replicated:
- ✅ Hierarchical categories (Brand → Model)
- ✅ Body type filtering
- ✅ Advanced equipment search
- ✅ First owner/New car badges
- ✅ Credit/Warranty indicators
- ✅ Dealer vs. Private seller distinction

---

## 🚀 Status

✅ **Database structure created**  
✅ **Configuration file ready**  
⏳ **Next**: Update templates & import data  
⏳ **Next**: Test advanced search functionality

---

**Version**: 1.0  
**Last Updated**: November 11, 2025  
**Repository**: https://github.com/samer-buraei/osclass-classifieds

