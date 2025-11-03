<div class="car-attributes-fields">
    <h3 class="form-section-title">Vehicle Details</h3>
    
    <div class="form-row">
        <div class="form-group">
            <label for="car_make">Make *</label>
            <select name="car_make" id="car_make" class="form-control" required>
                <option value="">Select Make</option>
                <?php foreach ($this->getMakes() as $make): ?>
                    <option value="<?= htmlspecialchars($make) ?>" 
                        <?= (isset($attributes['car_make']) && $attributes['car_make'] === $make) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($make) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="car_model">Model *</label>
            <input type="text" name="car_model" id="car_model" class="form-control" 
                   value="<?= htmlspecialchars($attributes['car_model'] ?? '') ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="car_year">Year *</label>
            <input type="number" name="car_year" id="car_year" class="form-control" 
                   min="1900" max="<?= date('Y') + 1 ?>" 
                   value="<?= htmlspecialchars($attributes['car_year'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="car_mileage">Mileage (km)</label>
            <input type="number" name="car_mileage" id="car_mileage" class="form-control" 
                   min="0" value="<?= htmlspecialchars($attributes['car_mileage'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="car_condition">Condition *</label>
            <select name="car_condition" id="car_condition" class="form-control" required>
                <option value="">Select Condition</option>
                <?php foreach ($this->getConditions() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['car_condition']) && $attributes['car_condition'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="car_transmission">Transmission *</label>
            <select name="car_transmission" id="car_transmission" class="form-control" required>
                <option value="">Select Transmission</option>
                <?php foreach ($this->getTransmissions() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['car_transmission']) && $attributes['car_transmission'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="car_fuel_type">Fuel Type *</label>
            <select name="car_fuel_type" id="car_fuel_type" class="form-control" required>
                <option value="">Select Fuel Type</option>
                <?php foreach ($this->getFuelTypes() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['car_fuel_type']) && $attributes['car_fuel_type'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="car_body_type">Body Type</label>
            <select name="car_body_type" id="car_body_type" class="form-control">
                <option value="">Select Body Type</option>
                <?php foreach ($this->getBodyTypes() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['car_body_type']) && $attributes['car_body_type'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="car_color">Color</label>
            <select name="car_color" id="car_color" class="form-control">
                <option value="">Select Color</option>
                <?php foreach ($this->getColors() as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= (isset($attributes['car_color']) && $attributes['car_color'] === $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="car_engine_size">Engine Size (L)</label>
            <input type="text" name="car_engine_size" id="car_engine_size" class="form-control" 
                   placeholder="e.g., 2.0" value="<?= htmlspecialchars($attributes['car_engine_size'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="car_doors">Doors</label>
            <input type="number" name="car_doors" id="car_doors" class="form-control" 
                   min="2" max="6" value="<?= htmlspecialchars($attributes['car_doors'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="car_seats">Seats</label>
            <input type="number" name="car_seats" id="car_seats" class="form-control" 
                   min="2" max="9" value="<?= htmlspecialchars($attributes['car_seats'] ?? '') ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="car_vin">VIN (Vehicle Identification Number)</label>
        <input type="text" name="car_vin" id="car_vin" class="form-control" 
               maxlength="17" value="<?= htmlspecialchars($attributes['car_vin'] ?? '') ?>">
        <small class="form-text">Optional - helps verify vehicle authenticity</small>
    </div>
</div>

<style>
.car-attributes-fields {
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
</style>

