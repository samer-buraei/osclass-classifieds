# Car Attributes Plugin

This plugin adds specialized fields for vehicle listings in your Osclass classifieds platform.

## Features

- **Make & Model** - Comprehensive list of popular car manufacturers
- **Year** - Model year selection
- **Mileage** - Odometer reading tracking
- **Condition** - New, Used, Excellent, Good, Fair, Salvage
- **Transmission** - Automatic, Manual, CVT, Semi-Automatic
- **Fuel Type** - Gasoline, Diesel, Electric, Hybrid, Hydrogen
- **Body Type** - Sedan, SUV, Truck, Van, Coupe, etc.
- **Color** - 12+ standard colors
- **Engine Size** - Engine displacement in liters
- **Doors & Seats** - Interior configuration
- **VIN** - Vehicle Identification Number (optional)

## Installation

1. Copy the `car-attributes` folder to your `plugins/` directory
2. The plugin will automatically activate for the "Vehicles" category
3. Start creating car listings with enhanced attributes!

## Usage

### For Listing Creators

When creating a listing in the Vehicles category, you'll see additional fields for:
- Vehicle make and model
- Year and mileage
- Condition and transmission type
- And more...

### For Buyers

Use the enhanced search filters to find exactly what you're looking for:
- Filter by make, year range, mileage
- Select specific transmission or fuel types
- Find cars by body type and color

## Configuration

To customize which categories use car attributes, edit `plugin.php` and modify the `isVehicleCategory()` method.

## Database Storage

All car attributes are stored in the `listing_attributes` table with the prefix `car_` (e.g., `car_make`, `car_model`).

## Compatibility

- PHP 8.2+
- Works with the core Osclass platform
- Mobile responsive
- SEO friendly

## Support

For issues or feature requests, please contact support.

