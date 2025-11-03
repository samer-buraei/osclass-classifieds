# Real Estate Plugin

This plugin adds specialized fields for property listings in your Osclass classifieds platform.

## Features

### Property Information
- **Property Type** - House, Apartment, Condo, Villa, Land, Commercial, etc.
- **Listing Type** - For Sale, For Rent, For Lease, Foreclosure
- **Bedrooms & Bathrooms** - Configurable room counts
- **Area** - Property size with multiple units (sqft, sqm, acres, hectares)
- **Lot Size** - Land area for properties with yards
- **Year Built** - Construction year
- **Floor Information** - Floor number and total floors in building
- **Parking** - Number of parking spaces
- **Furnished Status** - Unfurnished, Semi-Furnished, Fully Furnished

### Amenities & Features (20+ options)
- Swimming Pool
- Gym/Fitness Center
- Garden & Balcony
- Elevator
- 24/7 Security
- Air Conditioning
- Central Heating
- Fireplace
- Basement & Attic
- Laundry Room
- Storage
- Pet Friendly
- Wheelchair Accessible
- Solar Panels
- Smart Home
- High-Speed Internet
- And more...

## Installation

1. Copy the `real-estate` folder to your `plugins/` directory
2. The plugin will automatically activate for the "Real Estate" category
3. Start creating property listings with comprehensive details!

## Usage

### For Sellers/Agents

When creating a listing in the Real Estate category:
1. Select property type and listing type
2. Enter bedroom and bathroom counts
3. Specify property area and lot size
4. Add floor information if applicable
5. Select amenities using checkboxes
6. Save and publish

### For Buyers/Renters

Use the advanced search filters to find properties:
- Filter by property type and listing type
- Set bedroom and bathroom requirements
- Define area range preferences
- Select must-have amenities
- Find your perfect property!

## Configuration

To customize which categories use real estate attributes, edit `plugin.php` and modify the `isRealEstateCategory()` method.

## Database Storage

All property attributes are stored in the `listing_attributes` table with the prefix `re_` (e.g., `re_property_type`, `re_bedrooms`).

Amenities are stored as JSON arrays for efficient querying.

## Compatibility

- PHP 8.2+
- Works with the core Osclass platform
- Mobile responsive
- SEO friendly
- Multilingual ready

## Support

For issues or feature requests, please contact support.

