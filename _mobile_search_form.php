<?php

$search_params = (!empty($this->flight_search_params))
    ? $this->flight_search_params
    : $this->session->get('inline_search_form');

if ($this->action == 'cheapflights') {
    $search_params['type'] = 'roundtrip';
    $route = false;

    // set the departure
    if ($this->departure_airport) {
        $search_params['seg0_from'] = $this->departure_airport->code;
        $search_params['seg0_from_code'] = $this->departure_airport->code;
        $route = true;
    }

    // set the destination
    if ($this->destination_airport) {
        $search_params['seg0_to'] = $this->destination_airport->code;
        $search_params['seg0_to_code'] = $this->destination_airport->code;
        $route = true;
    }

    if ($route) {
        $search_params['seg0_date'] = isset($search_params['seg0_date'])
            ? $search_params['seg0_date']
            : Infinity_Date_Helper::getDate(strtotime('+1 week'));
        $search_params['seg1_date'] = isset($search_params['seg1_date'])
            ? $search_params['seg1_date']
            : date('Y-m-d', strtotime($search_params['seg0_date'] . ' + 7 days'));
    }
}

/*find the default form mode*/
$type = empty($search_params['type']) ? 'roundtrip' : $this->escape($search_params['type']);
$containerClass = 'form-wrap-' . $type;

$showHotelSearch =  !empty($showHotelSearch);
$showCarsSearch =  !empty($showCarsSearch);

// Variables for mobile date-picker
if (Mv_Ota_Surfer_Helper::isMobile()) {
    $mobileDatesFormat = 'D. M d';
    $mobileDateDefaultText = 'Departing';
    $mobileRoundTripDefaultText = 'Travel dates';
}
?>

<style type="text/css">
<?= $this->partial('mobile_suggestion/_mobile_search_form.css'); ?>
</style>
<script type="text/javascript">
<?= $this->partial('mobile_suggestion/_mobile_search_form.js'); ?>
</script>

<?= $this->partial('mobile_suggestion/_mobile_search_form_modal'); ?>

<div class="search-form-selection">
    <?php if ($showHotelSearch) : ?>
        <div class="search-selection-tabs">
            <div class="search-selection-tab flights active" data-tab="search-flights">
                <div class="tab-icon flights"></div><span>Flights</span>
            </div>
            <div class="search-selection-tab hotels" data-tab="search-hotels">
                <div class="tab-icon hotels"></div><span>Hotels</span>
            </div>
            <?php if ($showCarsSearch) : ?>
                <div class="search-selection-tab cars" data-tab="search-cars">
                    <div class="tab-icon cars"></div><span>Cars</span>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>
    <div class="search-selection-wrap <?= $showHotelSearch ? 'home-page' : ''; ?>">
        <form name="air_search_form"
              action="<?php echo $this->getBaseUrl(); ?>flight/search"
              method="get" class="inline-search-form-form main-search-form"
              autocomplete="off" data-tab-target="search-flights"
              data-selected-currency="<?= $this->currency; ?>">
            <input type="hidden" name="new_search" value="1" />

            <div class="inline-search-form <?php echo $containerClass; ?>" id="inline-search-form-1">
                <div class="form-filters-wrap flight-type clearfix">
                    <div class="form-filters-left">
                        <ul class="clearfix inline-search-form-mode">
                            <li class="item">
                                <a href=""
                                   id="toggle-roundtrip"
                                   class="<?php echo $type === 'roundtrip' ? 'active ' : ' '; ?>search-type-toggle"
                                   rel="roundtrip">Round Trip</a>
                            </li>
                            <li class="item">
                                <a href=""
                                   id="toggle-oneway"
                                   class="<?php echo $type === 'oneway' ? 'active ' : ' '; ?>search-type-toggle"
                                   rel="oneway">One Way</a>
                            </li>
                            <li class="item">
                                <a href="" id="toggle-multi"
                                   class="<?php echo $type === 'multi' ? 'active ' : ' '; ?>search-type-toggle"
                                   rel="multi">Multi-City</a></li>
                        </ul>
                    </div>
                </div>

                <div class="table-wrap">
                    <div class="column inputs">
                        <div class="form-fields-wrap clearfix">
                            <div class="form-field-from form-field-wrap">
                                <i class="fa fa-plane icon"></i>
                                <input type="text"
                                       name="seg0_from"
                                       placeholder="Leaving from"
                                       autocomplete="off"
                                       class="airport-related airport-field-modal"
                                       value="<?= ($search_params['seg0_from']) ? $search_params['seg0_from'] . ' - ' . Mv_Ota_Jfly_Controller_Page::getLocationNameByAirportCode($search_params['seg0_from']) : ''; ?>" />
                                <input type="hidden" name="seg0_from_code" value="<?= $search_params['seg0_from']; ?>"/>

                                <a href="#" class="form-field-from-to-sort">
                                    <div><i class="fa fa-arrow-up"></i></div>
                                    <div><i class="fa fa-arrow-down"></i></div>
                                </a>
                            </div>

                            <div class="form-field-to form-field-wrap">
                                <i class="fa fa-plane icon"></i>
                                <input type="text"
                                       name="seg0_to"
                                       placeholder="Going to"
                                       autocomplete="off"
                                       class="airport-related airport-field-modal"
                                       value="<?= ($search_params['seg0_to']) ? $search_params['seg0_to'] . ' - ' . Mv_Ota_Jfly_Controller_Page::getLocationNameByAirportCode($search_params['seg0_to']) : ''; ?>"/>
                                <input type="hidden" name="seg0_to_code" value="<?= $search_params['seg0_to']; ?>"/>
                            </div>


                            <?php if (Mv_Ota_Surfer_Helper::isMobile()) : ?>
                                <div class="form-field-mobile-date">
                                    <i class="fa fa-calendar icon"></i>
                                <span class="mobile-calendar-toggle-react"
                                      data-initial-text="<?= $mobileDateDefaultText ?>"
                                      data-initial-rt-text="<?= $mobileRoundTripDefaultText ?>"
                                      data-trigger-id="0">
                                    <?php if ($search_params['seg0_date'] && $search_params['seg1_date']) : ?>
                                        <?php if ($type == 'oneway' || $type == 'multi') : ?>
                                            <?= date($mobileDatesFormat, strtotime($search_params['seg0_date'])) ?>
                                        <?php else : ?>
                                            <?= date($mobileDatesFormat, strtotime($search_params['seg0_date'])) . ' to ' . date($mobileDatesFormat, strtotime($search_params['seg1_date'])) ?>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <?= $mobileDateDefaultText ?>
                                    <?php endif; ?>
                                </span>

                                </div>
                            <?php else : ?>
                                <div class="form-field-from-date">
                                    <input readonly="true" type="text" name="seg0_date" placeholder="Departing"
                                           autocomplete="off"
                                           class="date-field-from"
                                           value="<?= $search_params['seg0_date']; ?>"/>
                                </div>

                                <div class="form-field-to-date">
                                    <input readonly="true" type="text" name="seg1_date" placeholder="Returning"
                                           autocomplete="off"
                                           class="date-field-to"
                                           value="<?= $search_params['seg1_date']; ?>"/>
                                </div>
                            <?php endif; ?>

                            <div id="multi-city-wrapper" class="multi-city-wrapper mobile-only"></div>

                        </div>
                        <div class="form-fields-wrap-multi-container">
                            <?php
                            $displayNumber = 0;
                            for ($i = 1; $i < 5; $i++) {
                                /*multi cities segments :*/
                                $fromAirportValue = $search_params['seg' . $i . '_from'] ? $search_params['seg' . $i . '_from'] . ' - ' .  Mv_Ota_Jfly_Controller_Page::getLocationNameByAirportCode($search_params['seg' . $i . '_from']) : '';
                                $fromAirportCodeValue = $search_params['seg' . $i . '_from'];
                                $dateValue = $search_params['seg' . $i . '_date'];
                                $toAirportValue = $search_params['seg' . $i . '_to'] ? $search_params['seg' . $i . '_to'] . ' - ' . Mv_Ota_Jfly_Controller_Page::getLocationNameByAirportCode($search_params['seg' . $i . '_to']) : '';
                                $toAirportCodeValue = $search_params['seg' . $i . '_to'];

                                /*display row if there is data*/
                                $rowFilled = false;

                                if (!empty($fromAirportValue)
                                    && !empty($dateValue)
                                    && !empty($toAirportValue)
                                ) {
                                    $rowFilled = true;
                                }

                                $display = ($i === 1) || $rowFilled;
                                $containerStyle = ($i === 1) ? '' : ' style="display:none;"';

                                if ($display) {
                                    $displayNumber++;
                                }
                                ?>

                                <div class="form-fields-wrap-multi clearfix"
                                     id="inline-search-form-segment-<?php echo $i; ?>" <?php echo $containerStyle ?>>
                                    <div class="form-field-from form-field-wrap">
                                        <i class="fa fa-plane icon"></i>
                                        <input type="text" name="seg<?php echo $i; ?>_from"
                                               placeholder="Leaving from" autocomplete="off"
                                               class="airport-related airport-field-modal"
                                               value="<?php echo $fromAirportValue; ?>"/>

                                        <input type="hidden" name="seg<?php echo $i; ?>_from_code"
                                               value="<?php echo $fromAirportCodeValue; ?>"/>
                                    </div>
                                    <div class="form-field-to form-field-wrap">
                                        <i class="fa fa-plane icon"></i>
                                        <input type="text" name="seg<?php echo $i; ?>_to" placeholder="Going to"
                                               autocomplete="off" class="airport-related airport-field-modal"
                                               value="<?php echo $toAirportValue; ?>"/>
                                        <input type="hidden" name="seg<?php echo $i; ?>_to_code"
                                               value="<?php echo $toAirportCodeValue; ?>"/>
                                    </div>
                                    <div class="form-field-from-date form-field-wrap">
                                        <?php if (Mv_Ota_Surfer_Helper::isMobile()) : ?>
                                            <i class="fa fa-calendar icon"></i>
                                            <span id="mobile-calendar-mc-<?= $i ?>"
                                                  class="date-field-from mobile-calendar-toggle mobile-calendar-multicity-toggle"
                                                  data-trigger-id="<?= $i ?>"
                                                  data-is-multicity="true">
                                                <?php if ($search_params['seg' . $i . '_date']) : ?>
                                                    <?= date($mobileDatesFormat, strtotime($search_params['seg' . $i . '_date'])); ?>
                                                <?php else : ?>
                                                    <?= $mobileDateDefaultText ?>
                                                <?php endif; ?>
                                            </span>
                                            <input type="hidden" name="seg<?php echo $i; ?>_date"
                                                   value="<?= $dateValue ?>" class="mobile-calendar-input-<?= $i ?>">
                                        <?php else : ?>
                                            <input readonly="true" type="text" name="seg<?php echo $i; ?>_date"
                                                   placeholder="Date" autocomplete="off" class="date-field-from"
                                                   value="<?php echo $dateValue; ?>"/>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-segment form-field-segment-action">
                                        <div class="inline-search-form-add-segment-container">
                                            <a href="" class="inline-search-form-add-segment">+ Add segment</a>
                                        </div>

                                        <div class="inline-search-form-remove-segment-container">
                                            <a href="" class="inline-search-form-remove-segment">- Remove</a>
                                        </div>
                                    </div>
                                </div>

                            <?php
                            } ?>
                        </div>

                        <input type="hidden" name="type" value="<?php echo $type; ?>"/>
                    </div>
                    <div class="column search">
                        <div class="inline-passenger-flight-class">
                            <?php
                            $minNumPassengers = 1;

                            $totalPassengers = Mv_Ota_Fare_Package_Search_Params_Helper::getTotalPassengers($search_params);
                            $totalPassengers = $totalPassengers == 0 ? $minNumPassengers : $totalPassengers;

                            $numAdults = Mv_Ota_Fare_Package_Search_Params_Helper::getNumAdults($search_params);
                            $maxNumAdults = 8;
                            $numAdults = $numAdults == 0 ? $minNumPassengers : $numAdults;

                            $numChildren = Mv_Ota_Fare_Package_Search_Params_Helper::getNumChildren($search_params);
                            $maxNumChildren = 8;
                            $minNumChildren = 0;

                            $numInfants = Mv_Ota_Fare_Package_Search_Params_Helper::getNumInfants($search_params);
                            $maxNumInfants = 4;
                            $minNumInfants = 0;

                            $numInfantsLap = Mv_Ota_Fare_Package_Search_Params_Helper::getNumInfantsLap($search_params);
                            $maxNumInfantsLap = 4;
                            $minNumInfantsLap = 0;
                            ?>

                            <div class="form-field-passengers">
                                <div class="passenger-toggle-wrap">
                                    <i class="fa fa-user icon"></i>
                                    <div tabindex="0" class="passenger-toggle search-dropdown-toggle"
                                         data-dropdown="passengers">
                                        <span id="num-passengers" class="num-passengers"
                                              data-passengers="<?= $totalPassengers; ?>"><?= $totalPassengers; ?></span>
                                        <span> Passenger(s)</span>
                                        <i class="fa fa-chevron-down"></i>

                                        <div id="passengers" class="search-dropdown">
                                            <div class="passenger-type display-table">
                                                <div class="pass-left column label">Adult</div>
                                                <div class="pass-right column picker">
                                                    <div class="passenger-picker">
                                                        <input type="hidden" class="passengerType" name="num_adults"
                                                               data-field-min="<?= $minNumPassengers; ?>"
                                                               data-field-limit="<?= $maxNumAdults; ?>"
                                                               data-field-id="num_adults" value="<?= $numAdults; ?>">
                                                        <div class="display-table picker-wrapper">
                                                            <div class="column picker-button remove <?= $numAdults == $minNumPassengers ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-minus"></i>
                                                            </div>
                                                            <div class="column qty">
                                                                <span id="num_adults"><?= $numAdults; ?></span>
                                                            </div>
                                                            <div class="column picker-button add <?= $numAdults == $maxNumAdults ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="passenger-type display-table">
                                                <div class="pass-left column label">
                                                    Children
                                                    <div class="age">2-11</div>
                                                </div>
                                                <div class="pass-right column picker">
                                                    <div class="passenger-picker">
                                                        <input type="hidden" class="passengerType"
                                                               name="num_children"
                                                               data-field-min="<?= $minNumChildren; ?>"
                                                               data-field-limit="<?= $maxNumChildren; ?>"
                                                               data-field-id="num_children"
                                                               value="<?= $numChildren; ?>">
                                                        <div class="display-table picker-wrapper">
                                                            <div class="column picker-button remove <?= $numChildren == $minNumChildren ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-minus"></i>
                                                            </div>
                                                            <div class="column qty">
                                                                <span id="num_children"><?= $numChildren; ?></span>
                                                            </div>
                                                            <div class="column picker-button add <?= $numChildren == $maxNumChildren ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="passenger-type display-table">
                                                <div class="pass-left column label">
                                                    Infants
                                                    <div class="age">0-2</div>
                                                </div>
                                                <div class="pass-right column picker">
                                                    <div class="passenger-picker">
                                                        <input type="hidden" class="passengerType" name="num_infants"
                                                               data-field-min="<?= $minNumInfants; ?>"
                                                               data-field-limit="<?= $maxNumInfants; ?>"
                                                               data-field-id="num_infants" value="<?= $numInfants; ?>">
                                                        <div class="display-table picker-wrapper">
                                                            <div class="column picker-button remove <?= $numInfants == $minNumInfants ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-minus"></i>
                                                            </div>
                                                            <div class="column qty">
                                                                <span id="num_infants"><?= $numInfants; ?></span>
                                                            </div>
                                                            <div class="column picker-button add <?= $numInfants == $maxNumInfants ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="passenger-type display-table">
                                                <div class="pass-left column label">
                                                    Infants (lap)
                                                    <div class="age">0-2</div>
                                                </div>
                                                <div class="pass-right column picker">
                                                    <div class="passenger-picker">
                                                        <input type="hidden" class="passengerType"
                                                               name="num_infants_lap"
                                                               data-field-min="<?= $minNumInfantsLap; ?>"
                                                               data-field-limit="<?= $maxNumInfantsLap; ?>"
                                                               data-field-id="num_infants_lap"
                                                               value="<?= $numInfantsLap; ?>">
                                                        <div class="display-table picker-wrapper">
                                                            <div class="column picker-button remove <?= $numInfantsLap == $minNumInfantsLap ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-minus"></i>
                                                            </div>
                                                            <div class="column qty">
                                                                <span id="num_infants_lap"><?= $numInfantsLap; ?></span>
                                                            </div>
                                                            <div class="column picker-button add <?= $numInfantsLap == $maxNumInfantsLap ? 'disabled' : ''; ?>">
                                                                <i class="fa fa-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="close-passenger-picker">Done</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-filters-wrap clearfix flight-class">
                                <ul class="filter-list">
                                    <li class="item" style="margin-bottom: 0;">
                                        <?php
                                        /*prefill class values*/
                                        $classValue = $search_params['seat_class'];

                                        $classValueDefault = array('', '', '', '');

                                        if ($classValue === 'Economy') {
                                            $classValueDefault[0] = 'selected';
                                        } elseif ($classValue === 'EconomyPremium') {
                                            $classValueDefault[1] = 'selected';
                                        } elseif ($classValue === 'Business') {
                                            $classValueDefault[2] = 'selected';
                                        } elseif ($classValue === 'First') {
                                            $classValueDefault[3] = 'selected';
                                        } else {
                                            /*default option*/
                                            $classValueDefault[0] = 'selected';
                                        }
                                        ?>
                                        <a class="select-wrapper seat-class" rel="seat_class">
                                            <i class="jf-icon jf-seat flight-amenity available seats seat-icon"></i>
                                            <span class="select-first-text" rel="seat_class">Economy</span>
                                            <i class="select-down-arrow fa fa-chevron-down"></i>
                                            <select name="seat_class" class="text styled-select">
                                                <option value="Economy" <?= $classValueDefault[0]; ?>>Economy</option>
                                                <option value="EconomyPremium" <?= $classValueDefault[1]; ?>>Premium Economy</option>
                                                <option value="Business" <?= $classValueDefault[2]; ?>>Business</option>
                                                <option value="First" <?= $classValueDefault[3]; ?>>First</option>
                                            </select>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-field-button">
                            <a href="#" class="form-field-submit-button">
                                SEARCH
                            </a>
                            <button type="submit" class="form-field-submit-button2">
                                SEARCH
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-filters-wrap clearfix flight-class">
                    <div class="form-filters-left">
                        <ul class="filter-list">
                            <?php include $this->template('_search_select_affiliate'); ?>
                            <?php include $this->template('_search_recent_searches'); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
        <?php if ($showHotelSearch) : ?>
            <?php include $this->template('_hotels_search_form.php'); ?>
        <?php endif; ?>

        <?php if ($showCarsSearch) : ?>
            <?php include $this->template('_cars_search_form.php'); ?>
        <?php endif; ?>
    </div>
</div>

<?php

/*handle form error*/

if (Mv_Feedback::has('error')) { ?>

    <div class="form-errors" style="display:none;">
        <div class="error-modal-title"><i class="fa fa-exclamation-triangle"></i>Errors Found</div>
        <?php
        echo Mv_Feedback::display('li', 'error', 'hp');
        ?>
    </div>

    <script type="text/javascript">
        Modal.open({
            html: $('div.form-errors').html(),
            width: 550,
            unclosable: false
        });
    </script>

    <?php
}

/*open current segments*/

$displayNumber = isset($displayNumber) ? max((int)$displayNumber - 1, 0) : 0;

?>

<script type="text/javascript">

    $(document).ready(function () {

        <?php for ($i = 0; $i < $displayNumber; $i++) : ?>

        $('a.inline-search-form-add-segment:visible').eq(0).click();

        <?php endfor; ?>
    });

</script>
