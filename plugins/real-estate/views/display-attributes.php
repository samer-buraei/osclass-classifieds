<div class="property-attributes">
    <h3 class="attributes-title">Property Details</h3>
    
    <div class="attributes-grid">
        <?php if (isset($attributes['re_property_type'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Property Type</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['re_property_type'])) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_listing_type'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Listing Type</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['re_listing_type'])) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_bedrooms'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Bedrooms</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['re_bedrooms']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_bathrooms'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Bathrooms</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['re_bathrooms']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_area'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Area</span>
            <span class="attribute-value">
                <?= number_format($attributes['re_area']) ?> 
                <?= strtoupper($attributes['re_area_unit'] ?? 'sqft') ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_lot_size']) && $attributes['re_lot_size'] > 0): ?>
        <div class="attribute-item">
            <span class="attribute-label">Lot Size</span>
            <span class="attribute-value">
                <?= number_format($attributes['re_lot_size']) ?> 
                <?= strtoupper($attributes['re_area_unit'] ?? 'sqft') ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_year_built'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Year Built</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['re_year_built']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_floor'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Floor</span>
            <span class="attribute-value">
                <?= htmlspecialchars($attributes['re_floor']) ?>
                <?php if (isset($attributes['re_total_floors'])): ?>
                    / <?= htmlspecialchars($attributes['re_total_floors']) ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_parking_spaces']) && $attributes['re_parking_spaces'] > 0): ?>
        <div class="attribute-item">
            <span class="attribute-label">Parking</span>
            <span class="attribute-value"><?= htmlspecialchars($attributes['re_parking_spaces']) ?> spaces</span>
        </div>
        <?php endif; ?>

        <?php if (isset($attributes['re_furnished'])): ?>
        <div class="attribute-item">
            <span class="attribute-label">Furnished</span>
            <span class="attribute-value"><?= ucwords(str_replace('_', ' ', $attributes['re_furnished'])) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <?php if (isset($attributes['re_amenities']) && !empty($attributes['re_amenities'])): ?>
    <div class="amenities-section">
        <h4>Amenities & Features</h4>
        <div class="amenities-list">
            <?php 
            $allAmenities = $this->getAmenities();
            foreach ($attributes['re_amenities'] as $amenity): 
                if (isset($allAmenities[$amenity])):
            ?>
                <span class="amenity-badge">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                    </svg>
                    <?= $allAmenities[$amenity] ?>
                </span>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.property-attributes {
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
    border-bottom: 2px solid #28a745;
}

.attributes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
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

.amenities-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.amenities-section h4 {
    margin: 0 0 15px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.amenities-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.amenity-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: #e8f5e9;
    color: #2e7d32;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.amenity-badge svg {
    flex-shrink: 0;
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

