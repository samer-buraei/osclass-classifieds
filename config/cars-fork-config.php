<?php
/**
 * Cars Only Fork Configuration
 * Based on Polovni Automobili structure
 */

return [
    'site' => [
        'name' => 'Polovni Automobili Clone',
        'tagline' => 'Polovni automobili - auto oglasi, prodaja automobila',
        'description' => 'Oglasi za polovne i nove automobile. Pronađite svoj idealan auto.',
        'keywords' => 'polovni automobili, auto oglasi, prodaja automobila, kupovina automobila',
    ],

    'features' => [
        'cars_only' => true,
        'multi_category' => false, // Only cars, no other categories
        'hierarchical_categories' => true,
        'advanced_search' => true,
        'featured_listings' => true,
        'dealer_profiles' => true,
        'credit_available' => true,
        'warranty_filter' => true,
        'first_owner_filter' => true,
    ],

    // Body Types (Karoserija)
    'body_types' => [
        'limousine' => 'Limuzina',
        'hatchback' => 'Hečbek',
        'station-wagon' => 'Karavan',
        'coupe' => 'Kupe',
        'convertible' => 'Kabriolet/Roadster',
        'minivan' => 'Monovolumen (MiniVan)',
        'suv' => 'Džip/SUV',
        'pickup' => 'Pickup',
    ],

    // Fuel Types
    'fuel_types' => [
        'diesel' => 'Dizel',
        'petrol' => 'Benzin',
        'hybrid' => 'Hibrid',
        'plugin-hybrid' => 'Plug-in hibrid',
        'electric' => 'Električni',
        'lpg' => 'TNG (LPG)',
        'cng' => 'CNG',
        'hydrogen' => 'Vodonik',
    ],

    // Transmission Types
    'transmission_types' => [
        'manual' => 'Manuelni',
        'automatic' => 'Automatski',
        'semi-automatic' => 'Poluautomatski',
        'cvt' => 'CVT',
        'dct' => 'DSG/DCT',
    ],

    // Drive Types
    'drive_types' => [
        'fwd' => 'Prednji pogon',
        'rwd' => 'Zadnji pogon',
        'awd' => '4x4 (Stalni)',
        '4wd' => '4x4 (Priključivi)',
    ],

    // Colors
    'colors' => [
        'white' => 'Bela',
        'black' => 'Crna',
        'silver' => 'Srebrna',
        'gray' => 'Siva',
        'red' => 'Crvena',
        'blue' => 'Plava',
        'green' => 'Zelena',
        'yellow' => 'Žuta',
        'orange' => 'Narandžasta',
        'brown' => 'Braon',
        'gold' => 'Zlatna',
        'beige' => 'Bež',
        'purple' => 'Ljubičasta',
    ],

    // Vehicle Conditions
    'conditions' => [
        'new' => 'Novo vozilo',
        'excellent' => 'Odlično stanje',
        'very-good' => 'Vrlo dobro',
        'good' => 'Dobro',
        'fair' => 'Solidno',
        'damaged' => 'Oštećeno',
        'parts' => 'Za delove',
    ],

    // Emission Classes
    'emission_classes' => [
        'euro6d' => 'EURO 6D',
        'euro6c' => 'EURO 6C',
        'euro6b' => 'EURO 6B',
        'euro6' => 'EURO 6',
        'euro5' => 'EURO 5',
        'euro4' => 'EURO 4',
        'euro3' => 'EURO 3',
        'euro2' => 'EURO 2',
        'euro1' => 'EURO 1',
    ],

    // Top Brands (for quick filters)
    'top_brands' => [
        'Audi', 'BMW', 'Mercedes Benz', 'Volkswagen', 'Škoda',
        'Toyota', 'Honda', 'Nissan', 'Mazda', 'Hyundai', 'Kia',
        'Peugeot', 'Renault', 'Citroen', 'Fiat', 'Opel', 'Ford',
    ],

    // Search Configuration
    'search' => [
        'results_per_page' => 25,
        'max_price' => 500000,
        'min_price' => 500,
        'max_year' => date('Y') + 1,
        'min_year' => 1960,
        'max_mileage' => 500000,
        'default_sort' => 'date_desc', // date_desc, date_asc, price_asc, price_desc, mileage_asc
    ],

    // Featured Listing Prices (in EUR)
    'listing_prices' => [
        'standard' => 0, // Free
        'featured_7days' => 10,
        'featured_14days' => 18,
        'featured_30days' => 30,
        'top_7days' => 15,
        'top_14days' => 25,
        'top_30days' => 40,
        'spotlight_7days' => 20,
        'spotlight_14days' => 35,
        'spotlight_30days' => 60,
    ],

    // Equipment Categories (for filters)
    'equipment_categories' => [
        'safety' => 'Sigurnost',
        'comfort' => 'Komfor',
        'entertainment' => 'Multimedija',
        'exterior' => 'Eksterijer',
        'technology' => 'Tehnologija',
    ],

    // Quick Filters (Brza pretraga)
    'quick_filters' => [
        'latest' => 'Najnoviji oglasi',
        'new_cars' => 'Novi automobili',
        'first_owner' => 'Prvi vlasnik',
        'bought_new_serbia' => 'Kupljen nov u Srbiji',
        'on_credit' => 'Automobili na kredit',
        'with_warranty' => 'Automobili sa garancijom',
        'for_exchange' => 'Automobili za zamenu',
        'electric' => 'Električni automobili',
        'hybrid' => 'Hibridni automobili',
    ],

    // Dealer Types
    'dealer_types' => [
        'private' => 'Privatno lice',
        'dealer' => 'Auto plac',
        'authorized_dealer' => 'Ovlašćeni prodavac',
        'rent_a_car' => 'Rent-a-car',
    ],

    // Languages
    'languages' => [
        'sr' => 'Srpski',
        'en' => 'English',
    ],

    // Currency
    'currency' => [
        'default' => 'EUR',
        'symbol' => '€',
        'position' => 'after', // before or after
    ],
];

