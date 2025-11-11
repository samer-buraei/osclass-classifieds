<div class="real-estate-fields">
    <h3 class="form-section-title">Property Details</h3>
    
    <div class="form-row">
        <div class="form-group">
            <label for="re_property_type">Property Type *</label>
            <select name="re_property_type" id="re_property_type" class="form-control" required>
                <option value="">Select Type</option>
                <?php foreach ($this->getPropertyTypes() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['re_property_type']) && $attributes['re_property_type'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="re_listing_type">Listing Type *</label>
            <select name="re_listing_type" id="re_listing_type" class="form-control" required>
                <option value="">Select Type</option>
                <?php foreach ($this->getListingTypes() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['re_listing_type']) && $attributes['re_listing_type'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="re_bedrooms">Bedrooms</label>
            <input type="number" name="re_bedrooms" id="re_bedrooms" class="form-control" 
                   min="0" max="20" value="<?= htmlspecialchars($attributes['re_bedrooms'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="re_bathrooms">Bathrooms</label>
            <input type="number" name="re_bathrooms" id="re_bathrooms" class="form-control" 
                   min="0" max="10" step="0.5" value="<?= htmlspecialchars($attributes['re_bathrooms'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="re_area">Area *</label>
            <input type="number" name="re_area" id="re_area" class="form-control" 
                   min="0" value="<?= htmlspecialchars($attributes['re_area'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="re_area_unit">Area Unit</label>
            <select name="re_area_unit" id="re_area_unit" class="form-control">
                <?php foreach ($this->getAreaUnits() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['re_area_unit']) && $attributes['re_area_unit'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="re_lot_size">Lot Size</label>
            <input type="number" name="re_lot_size" id="re_lot_size" class="form-control" 
                   min="0" value="<?= htmlspecialchars($attributes['re_lot_size'] ?? '') ?>">
            <small class="form-text">Land area (same unit as above)</small>
        </div>

        <div class="form-group">
            <label for="re_year_built">Year Built</label>
            <input type="number" name="re_year_built" id="re_year_built" class="form-control" 
                   min="1800" max="<?= date('Y') ?>" value="<?= htmlspecialchars($attributes['re_year_built'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="re_floor">Floor</label>
            <input type="number" name="re_floor" id="re_floor" class="form-control" 
                   min="0" max="200" value="<?= htmlspecialchars($attributes['re_floor'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="re_total_floors">Total Floors in Building</label>
            <input type="number" name="re_total_floors" id="re_total_floors" class="form-control" 
                   min="1" max="200" value="<?= htmlspecialchars($attributes['re_total_floors'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="re_parking_spaces">Parking Spaces</label>
            <input type="number" name="re_parking_spaces" id="re_parking_spaces" class="form-control" 
                   min="0" max="20" value="<?= htmlspecialchars($attributes['re_parking_spaces'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="re_furnished">Furnished</label>
            <select name="re_furnished" id="re_furnished" class="form-control">
                <option value="">Select Option</option>
                <?php foreach ($this->getFurnishedOptions() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['re_furnished']) && $attributes['re_furnished'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label>Amenities & Features</label>
        <div class="amenities-grid">
            <?php 
            $selectedAmenities = isset($attributes['re_amenities']) ? json_decode($attributes['re_amenities'], true) : [];
            foreach ($this->getAmenities() as $value => $label): 
            ?>
                <label class="amenity-checkbox">
                    <input type="checkbox" name="re_amenities[]" value="<?= $value ?>" 
                        <?= in_array($value, $selectedAmenities ?? []) ? 'checked' : '' ?>>
                    <span><?= $label ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.real-estate-fields {
    margin: 20px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.form-section-title {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 5px;
    font-weight: 500;
    color: #555;
}

.form-control {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
}

.form-text {
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.amenity-checkbox {
    display: flex;
    align-items: center;
    padding: 8px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.amenity-checkbox:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.amenity-checkbox input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}

.amenity-checkbox span {
    font-size: 13px;
    color: #333;
}
</style>

