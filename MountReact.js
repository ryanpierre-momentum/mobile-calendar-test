//Mobile Calender component wrapped in document ready for now
$( document ).ready(function() {

	//Set mount point for calendar component
	$('.form-field-mobile-date').on('click', (e) => {

		//Create a simple hook
		let hook = document.createElement("div"); 

		//Register the hook's id
		hook.setAttribute("id", "react-app-hook");

		//Append to top level so it's on top of header, footer, etc
		document.body.appendChild(hook);

		//Pass any props we need to the react app (i.e. existing segment dates chosen already)
		let test = "Ryan";
		
		//Load any components needed into the react-app container and render to our calendar hook
		ReactDOM.render(
		<div id="react-app">
		  <CalendarModal name={test}/>
		</div>,
		hook
		);

	});

});