<div id="form-field-modal" style="display: none;">
    <div class="search-field-tabs main-search-form">
        
        <div class="search-header-wrap">
            <div class="search-form-modal-close">
                <a href="#" onclick="Modal.close();"><i class="fa fa-chevron-left"></i></a>
            </div>
            <div class="search-header-message going-to-message"> Where are you going to ? </div>
            <div class="search-header-message leaving-from-message"> Where are you leaving from ? </div>
        </div>

        <?php for ($i = 0; $i < 5; $i++) : ?>
        <div id="modal-flight-wrap-<?= $i; ?>"
             class="modal-flight-wrap" <?= $i > 0 ? 'style="display: none;"': ''; ?>
             data-index="<?= $i; ?>">

             <!-- SELECT DIRECTION -->

            <ul class="search-field-tabs-list">
                <li class="from">
                    <a href="#modalAutocomplete" class="search-field-action"
                       data-anchor="leaving-from-<?= $i; ?>"
                       data-index="<?= $i; ?>">
                        <i class="fa fa-plane"></i>
                        <span class="modal-tab-label">From</span>
                        <div class="search-airport-value" id="modal_seg<?= $i; ?>_from">---</div>
                    </a>
                </li>
                <li class="to">
                    <a href="#modalAutocomplete" class="search-field-action"
                       data-anchor="going-to-<?= $i; ?>"
                       data-index="<?= $i; ?>">
                        <i class="fa fa-plane fa-rotate-90"></i>
                        <span class="modal-tab-label">To</span>
                        <div class="search-airport-value" id="modal_seg<?= $i; ?>_to">---</div>
                    </a>
                </li>
            </ul>

            <!-- Direction selection indicator --> 

            <div class="search-direction-indicator" data-anchor="direction-indicator-<?= $i; ?>"></div>

            <!-- FROM -->

            <div id="leaving-from-<?= $i; ?>" class="search-field-content">
                <div class="form-field-from">
                    <input type="text" class="modal-airport-search"
                           data-name="seg<?= $i; ?>_from"
                           placeholder="Enter an origin city or airport"
                           value=""
                           autocomplete="off" />
                </div>
                <?php
                if (!empty($this->suggested_airports)) {
                    echo $this->partial(
                        'mobile_suggestion/_mobile_recent_search_list',
                        array(
                            'suggested_airports' => $this->suggested_airports,
                            'index' => $i,
                            'direction' => 'from'
                        )
                    );
                }
                ?>
            </div>

            <!-- TO -->

            <div id="going-to-<?= $i; ?>" class="search-field-content" style="display: none;">
                <div class="form-field-to">
                    <input type="text" class="modal-airport-search"
                           data-name="seg<?= $i; ?>_to"
                           placeholder="Enter a destination city or airport"
                           value=""
                           autocomplete="off" />
                </div>
                <?php
                if (!empty($this->suggested_airports)) {
                    echo $this->partial(
                        'mobile_suggestion/_mobile_recent_search_list',
                        array(
                            'suggested_airports' => $this->suggested_airports,
                            'index' => $i,
                            'direction' => 'to'
                        )
                    );
                }
                ?>
            </div>

        </div>
        <?php endfor; ?>
    </div>
</div>