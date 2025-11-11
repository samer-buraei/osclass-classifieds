<div class="car-search-filters">
    <h4>Vehicle Filters</h4>
    
    <div class="filter-group">
        <label>Make</label>
        <select name="car_make" class="form-control">
            <option value="">Any Make</option>
            <?php foreach ($this->getMakes() as $make): ?>
                <option value="<?= htmlspecialchars($make) ?>" 
                    <?= (isset($_GET['car_make']) && $_GET['car_make'] === $make) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($make) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Year Range</label>
        <div class="range-inputs">
            <input type="number" name="year_min" class="form-control" 
                   placeholder="Min" min="1900" max="<?= date('Y') ?>" 
                   value="<?= htmlspecialchars($_GET['year_min'] ?? '') ?>">
            <span>to</span>
            <input type="number" name="year_max" class="form-control" 
                   placeholder="Max" min="1900" max="<?= date('Y') + 1 ?>" 
                   value="<?= htmlspecialchars($_GET['year_max'] ?? '') ?>">
        </div>
    </div>

    <div class="filter-group">
        <label>Mileage (km)</label>
        <div class="range-inputs">
            <input type="number" name="mileage_min" class="form-control" 
                   placeholder="Min" value="<?= htmlspecialchars($_GET['mileage_min'] ?? '') ?>">
            <span>to</span>
            <input type="number" name="mileage_max" class="form-control" 
                   placeholder="Max" value="<?= htmlspecialchars($_GET['mileage_max'] ?? '') ?>">
        </div>
    </div>

    <div class="filter-group">
        <label>Condition</label>
        <select name="car_condition" class="form-control">
            <option value="">Any Condition</option>
            <?php foreach ($this->getConditions() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['car_condition']) && $_GET['car_condition'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Transmission</label>
        <select name="car_transmission" class="form-control">
            <option value="">Any Transmission</option>
            <?php foreach ($this->getTransmissions() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['car_transmission']) && $_GET['car_transmission'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Fuel Type</label>
        <select name="car_fuel_type" class="form-control">
            <option value="">Any Fuel Type</option>
            <?php foreach ($this->getFuelTypes() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['car_fuel_type']) && $_GET['car_fuel_type'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Body Type</label>
        <select name="car_body_type" class="form-control">
            <option value="">Any Body Type</option>
            <?php foreach ($this->getBodyTypes() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['car_body_type']) && $_GET['car_body_type'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Color</label>
        <select name="car_color" class="form-control">
            <option value="">Any Color</option>
            <?php foreach ($this->getColors() as $value => $label): ?>
                <option value="<?= $value ?>" 
                    <?= (isset($_GET['car_color']) && $_GET['car_color'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<style>
.car-search-filters {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}

.car-search-filters h4 {
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
</style>

