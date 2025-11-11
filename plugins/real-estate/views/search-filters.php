<div class="property-search-filters">
    <h4>Property Filters</h4>
    
    <div class="filter-group">
        <label>Property Type</label>
        <select name="re_property_type" class="form-control">
            <option value="">Any Type</option>
            <?php foreach ($this->getPropertyTypes() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['re_property_type']) && $_GET['re_property_type'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Listing Type</label>
        <select name="re_listing_type" class="form-control">
            <option value="">Any</option>
            <?php foreach ($this->getListingTypes() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['re_listing_type']) && $_GET['re_listing_type'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Bedrooms</label>
        <select name="re_bedrooms" class="form-control">
            <option value="">Any</option>
            <option value="1" <?= (isset($_GET['re_bedrooms']) && $_GET['re_bedrooms'] === '1') ? 'selected' : '' ?>>1+</option>
            <option value="2" <?= (isset($_GET['re_bedrooms']) && $_GET['re_bedrooms'] === '2') ? 'selected' : '' ?>>2+</option>
            <option value="3" <?= (isset($_GET['re_bedrooms']) && $_GET['re_bedrooms'] === '3') ? 'selected' : '' ?>>3+</option>
            <option value="4" <?= (isset($_GET['re_bedrooms']) && $_GET['re_bedrooms'] === '4') ? 'selected' : '' ?>>4+</option>
            <option value="5" <?= (isset($_GET['re_bedrooms']) && $_GET['re_bedrooms'] === '5') ? 'selected' : '' ?>>5+</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Bathrooms</label>
        <select name="re_bathrooms" class="form-control">
            <option value="">Any</option>
            <option value="1" <?= (isset($_GET['re_bathrooms']) && $_GET['re_bathrooms'] === '1') ? 'selected' : '' ?>>1+</option>
            <option value="2" <?= (isset($_GET['re_bathrooms']) && $_GET['re_bathrooms'] === '2') ? 'selected' : '' ?>>2+</option>
            <option value="3" <?= (isset($_GET['re_bathrooms']) && $_GET['re_bathrooms'] === '3') ? 'selected' : '' ?>>3+</option>
            <option value="4" <?= (isset($_GET['re_bathrooms']) && $_GET['re_bathrooms'] === '4') ? 'selected' : '' ?>>4+</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Area Range</label>
        <div class="range-inputs">
            <input type="number" name="area_min" class="form-control" 
                   placeholder="Min" value="<?= htmlspecialchars($_GET['area_min'] ?? '') ?>">
            <span>to</span>
            <input type="number" name="area_max" class="form-control" 
                   placeholder="Max" value="<?= htmlspecialchars($_GET['area_max'] ?? '') ?>">
        </div>
        <select name="re_area_unit" class="form-control" style="margin-top: 5px;">
            <?php foreach ($this->getAreaUnits() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['re_area_unit']) && $_GET['re_area_unit'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Furnished</label>
        <select name="re_furnished" class="form-control">
            <option value="">Any</option>
            <?php foreach ($this->getFurnishedOptions() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['re_furnished']) && $_GET['re_furnished'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Parking</label>
        <select name="re_parking_spaces" class="form-control">
            <option value="">Any</option>
            <option value="1" <?= (isset($_GET['re_parking_spaces']) && $_GET['re_parking_spaces'] === '1') ? 'selected' : '' ?>>1+</option>
            <option value="2" <?= (isset($_GET['re_parking_spaces']) && $_GET['re_parking_spaces'] === '2') ? 'selected' : '' ?>>2+</option>
            <option value="3" <?= (isset($_GET['re_parking_spaces']) && $_GET['re_parking_spaces'] === '3') ? 'selected' : '' ?>>3+</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Key Amenities</label>
        <div class="amenities-checkboxes">
            <?php 
            $keyAmenities = ['pool', 'gym', 'garden', 'balcony', 'elevator', 'security', 'parking', 'ac'];
            $allAmenities = $this->getAmenities();
            foreach ($keyAmenities as $amenity): 
                if (isset($allAmenities[$amenity])):
            ?>
                <label class="amenity-checkbox-small">
                    <input type="checkbox" name="amenities[]" value="<?= $amenity ?>"
                        <?= (isset($_GET['amenities']) && in_array($amenity, $_GET['amenities'])) ? 'checked' : '' ?>>
                    <span><?= $allAmenities[$amenity] ?></span>
                </label>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
</div>

<style>
.property-search-filters {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}

.property-search-filters h4 {
    margin: 0 0 15px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.filter-group {
    margin-bottom: 15px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 13px;
    font-weight: 500;
    color: #555;
}

.range-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
}

.range-inputs input {
    flex: 1;
}

.range-inputs span {
    font-size: 13px;
    color: #666;
}

.amenities-checkboxes {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.amenity-checkbox-small {
    display: flex;
    align-items: center;
    font-size: 13px;
    cursor: pointer;
}

.amenity-checkbox-small input[type="checkbox"] {
    margin-right: 6px;
    cursor: pointer;
}
</style>

