//Mobile Calender component wrapped in document ready for now
$( document ).ready(function() {

	//Set mount point for calendar component
	$('.mobile-calendar-toggle-react').on('click', (e) => {

		//Get active flight type
		let flightType = document.querySelector('.search-type-toggle.active').rel;

		//Create a simple hook
		let hook = document.createElement("div"); 

		//Register the hook's id
		hook.setAttribute("id", "react-app-hook");

		//Append to top level so it's on top of header, footer, etc
		document.body.appendChild(hook);

		//Pass any props we need to the react app (i.e. existing segment dates chosen already from hidden inputs)
		let segments = [];

		//Get any existing segment values - TODO: Don't use first (we need it because 2x seg1_date)
		segments.push($('input[name=seg0_date]').first().val());
		segments.push($('input[name=seg1_date]').first().val());
		segments.push($('input[name=seg2_date]').first().val());
		segments.push($('input[name=seg3_date]').first().val());
		segments.push($('input[name=seg4_date]').first().val());

		//Hide the footer because it's extremely annoying in calendar
		$('.site-footer').hide();
		
		//Load any components needed into the react-app container and render to our calendar hook
		ReactDOM.render(
		<div id="react-app">
		  <CalendarModal existingSegments={segments} flightType={flightType}/>
		</div>,
		hook
		);

		//Listen for close and pass any pertinent data after
		$('.calendar-close-button').on('click', (e) => {

			let displayFormat = 'ddd. MMM DD';

			//Update input state
			let hiddenSeg0 = $('input[name=seg0_date]').first().val($('#seg0_date_mirror').val());
			let hiddenSeg1 = $('input[name=seg1_date]').first().val($('#seg1_date_mirror').val());
			let hiddenSeg2 = $('input[name=seg2_date]').first().val($('#seg2_date_mirror').val());
			let hiddenSeg3 = $('input[name=seg3_date]').first().val($('#seg3_date_mirror').val());
			let hiddenSeg4 = $('input[name=seg4_date]').first().val($('#seg4_date_mirror').val());

			//Remove the react app and update hidden input state
			document.body.removeChild(hook);

			//Show footer
			$('.site-footer').show();

			//Update display fields or set to null for parsing
			let seg0 = hiddenSeg0.val().length > 0 ? moment(hiddenSeg0.val()).format(displayFormat) : null;
			let seg1 = hiddenSeg1.val().length > 0 ? moment(hiddenSeg1.val()).format(displayFormat) : null;
			let seg2 = hiddenSeg2.val().length > 0 ? moment(hiddenSeg2.val()).format(displayFormat) : null;
			let seg3 = hiddenSeg3.val().length > 0 ? moment(hiddenSeg3.val()).format(displayFormat) : null;
			let seg4 = hiddenSeg4.val().length > 0 ? moment(hiddenSeg4.val()).format(displayFormat) : null;
			
			//Return trip
			if(seg0 && seg1 && !seg2 && !seg3 && !seg4){
				$(`#mobile-calendar-mc-0`).html(`${seg0} to ${seg1}`);
			}

			//One way / multi
			else{
				//$(`#mobile-calendar-mc-${}`).html(`${seg0} to ${seg1}`);
			}
		})

	});

});