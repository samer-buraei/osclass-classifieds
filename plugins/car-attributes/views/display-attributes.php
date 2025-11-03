<div class="car-attributes">
    <h3 class="attributes-title">Vehicle Specifications</h3>
    
    <div class="attributes-grid">
        <?php if (isset($attributes['car_make'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Make</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['car_make']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_model'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Model</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['car_model']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_year'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Year</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['car_year']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_mileage'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Mileage</span>
            <span class="attribute-value"><?= number_format($attributes['car_mileage']) ?> km</span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_condition'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Condition</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['car_condition'])) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_transmission'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Transmission</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['car_transmission'])) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_fuel_type'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Fuel Type</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['car_fuel_type'])) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_body_type'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Body Type</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['car_body_type'])) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_color'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Color</span>
            <span class="attribute-value"><?= ucfirst($attributes['car_color']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_engine_size'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Engine Size</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['car_engine_size']) ?>L</span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_doors'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Doors</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['car_doors']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['car_seats'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Seats</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['car_seats']) ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.car-attributes {
    margin: 30px 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.attributes-title {
    margin: 0 0 20px 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
}

.attributes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.attribute-item {
    display: flex;
    flex-direction: column;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
}

.attribute-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.attribute-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

@media (max-width: 768px) {
    .attributes-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .attributes-grid {
        grid-template-columns: 1fr;
    }
}
</style>

