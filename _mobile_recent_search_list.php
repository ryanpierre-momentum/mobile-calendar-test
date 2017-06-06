<?php
if (empty($suggested_airports)) {
    return;
}
?>
<div class="recent-search-wrap">
    <ul class="recent-search-list">
    <?php foreach ($suggested_airports as $airport_type => $airports) :
        if ($direction == 'to' && $airport_type == 'nearby') {
            continue;
        }
        ?>
        <?php foreach ($airports as $airport_code => $airport_name) : ?>
            <li class="form-field-selected-item <?= $airport_type == 'nearby' ? 'nearby' : 'recent'; ?>">
                <a href="#modalAutocomplete"
                   data-code="<?= $airport_code; ?>"
                   data-name="<?= $airport_name; ?>"
                   data-index="<?= $index; ?>"
                   data-direction="<?= $direction; ?>">
                    <i
                        class="fa <?= $airport_type == 'nearby' ? 'fa-location-arrow' : 'fa-history'; ?> icon"
                        aria-hidden="true"></i>
                    <div class="airport-name-wrapper">
                        <span class="airport-name-outer">
                            <p class="airport-name-inner"><?= $airport_name; ?></p>
                        </span>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </ul>
</div>