function renderMobileCalendarCell(date, selectionCallback){
	let style = "mobile-calendar-cell";
	let handler = date.active ? (e) => {selectionCallback(date)} : (e) => {e.preventDefault()};

	//Selected days need the selected style
	if(date.selected){
		style+= " selected";
	}

	//Days from different months need a different appearance and callback (Currently it just hides them...)
	else{
		if(!date.active){
			style+= " inactive";
		}
	}

	//Selected days need the selected style
	if(date.buffered){
		style+= " buffered";
	}

	//Weekends are smaller and need a different class added
	if(date.weekdayNumber === 0 || date.weekdayNumber === 6){
		style+= " weekend";
	}

	//Inactive days are currently just not shown, but this can be made into a toggle for how we want to display them later
	return <div key={`${date.id}`} className={style} onClick={handler}> {date.active ? date.number : null} </div>;
}

class MobileCalendar extends React.Component {

	constructor() {
		super();

		this.state = {
			currentMonthName: "", //For display
			currentYearName: "", //For display
			currentMonthNumber: 0, //For passing data to host component
			currentYearNumber: 0, //For passing data to host component
			visibleDates: [], //Array of date objects that describe the current calendar state
			selectedDates: [] //Array of all selected date data to pass to hidden inputs and ui
		}

		//Bind functions that need access to state
		this.getMonthObject = this.getMonthObject.bind(this);
		this.getCalendarData = this.getCalendarData.bind(this);
	}

	/**
	 * Gets the required info about a specific month from a moment object
	 * @param {momentJsDate} Moment.js object from calling moment() 
	 * @return {Object} All required month info
	 */
	getMonthObject(momentJsDate){

		//Sunday = 0, Monday = 1, etc.
		let firstDay = momentJsDate.date(1).day();

		//Jan = 0, Feb = 1, etc.
		let monthNumber = momentJsDate.month();

		//1:1 with normal years, just called yearNumber for consistency
		let yearNumber = momentJsDate.year();

		//Count the number of days in the month
		let numDays = momentJsDate.daysInMonth();

		//Set initial number of weeks for most cases
		let numWeeks = 5;

		//Calculate exceptions - Friday with 31 days or Saturday with 30 or 31 days
		if((firstDay === 5 && numDays === 31) || (firstDay === 6 && numDays >= 30)){
			numWeeks = 6;
		}

		//Calculate exceptions - Friday with 31 days
		else if(numDays < 29){
			numWeeks = 4;
		}

		return {firstDay: firstDay, monthNumber: monthNumber, yearNumber: yearNumber, numDays: numDays, numWeeks: numWeeks};
	}

	/**
	 * Generates all the date cells in a given month in a given year on init
	 * @param {Number} Year 
	 * @param {Number} Month (true month #, not 0-based! i.e. April = 4)
	 * @param {Array} Existing segments we need to render
	 * @return {Array} CalendarData
	 */
	getCalendarData(year, month, existingSegments) {

		//Fetch existing selections from state
		let { selectedDates, startDate, endDate } = this.state;

		//If no month or year, get the current month and year, and get the name of the month
		let hasCalendarData = year && month;
		let initMonth = hasCalendarData ? moment([year, month - 1]) : moment();
		let initMonthName = hasCalendarData ? moment([year, month - 1]).format("MMMM") : moment().format("MMMM");
		let initYearName = hasCalendarData ? moment([year, month - 1]).format("YYYY") : moment().format("YYYY");

		//Get the previous month and next month in case we need to fill in that data
		let prevMonth = hasCalendarData ? moment([year, month - 1]).subtract(1, "months") : moment().subtract(1, "months");
		let nextMonth = hasCalendarData ? moment([year, month - 1]).add(1, "months") : moment().add(1, "months");

		//Get month objects
		let initMonthData = this.getMonthObject(initMonth);
		let prevMonthData = this.getMonthObject(prevMonth);
		let nextMonthData = this.getMonthObject(nextMonth);

		//Create container for initial dates
		let dates = [];

		//Create initial dates object
		for(let i = 0; i < initMonthData.numWeeks * 7; i++){

			//Initialize a new calendar cell
			let currentCell = {};
			let currentDayNumber = 0;

			//Get the weekday number, 0 = Sunday, 1 = Monday, etc.
			let weekdayNumber = i % 7;

			//Create ghost cells before - id is formatted to be an ISO date for comparison
			if(i < initMonthData.firstDay){

				//Calculate what day it should be from the previous month to fill empty space at the beginning
				currentDayNumber = prevMonthData.numDays - Math.abs(i - initMonthData.firstDay + 1);
				currentCell = {
					id: `${prevMonthData.yearNumber}-${prevMonthData.monthNumber < 10 ? "0" : ""}${prevMonthData.monthNumber + 1}-${currentDayNumber < 10 ? "0" : ""}${currentDayNumber}`,
					number: currentDayNumber, 
					monthNumber: prevMonthData.monthNumber, 
					yearNumber: prevMonthData.yearNumber,
					weekdayNumber: weekdayNumber, 
					active: false, 
					selected: false,
					buffered: false
				};
			}

			//Create ghost cells after - id is formatted to be an ISO date for comparison
			else if(i >= initMonthData.firstDay + initMonthData.numDays){

				//Calculate what day it should be from the next month to fill empty space at the end
				currentDayNumber = Math.abs(i - (initMonthData.firstDay + initMonthData.numDays) + 1);
				currentCell = {
					id: `${nextMonthData.yearNumber}-${nextMonthData.monthNumber < 10 ? "0" : ""}${nextMonthData.monthNumber + 1}-${currentDayNumber < 10 ? "0" : ""}${currentDayNumber}`,
					number: currentDayNumber, 
					monthNumber: nextMonthData.monthNumber, 
					yearNumber: nextMonthData.yearNumber,
					weekdayNumber: weekdayNumber, 
					active: false, 
					selected: false,
					buffered: false
				};
			}

			//Create a cell for the current month of the calendar - id is formatted to be an ISO date for comparison
			else {
				currentCell = {
					id: `${initMonthData.yearNumber}-${initMonthData.monthNumber < 10 ? "0" : ""}${initMonthData.monthNumber + 1}-${((i - initMonthData.firstDay) + 1) < 10 ? "0" : ""}${(i - initMonthData.firstDay) + 1}`,
					number: (i - initMonthData.firstDay) + 1,
					monthNumber: initMonthData.monthNumber, 
					yearNumber: initMonthData.yearNumber,
					weekdayNumber: weekdayNumber, 
					active: true, 
					selected: false,
					buffered: false
				};
			}

			//Push the new date cell to the array of cells
			dates.push(currentCell);
		}

		//Push the new calendar data to state to propagate changes to DOM
		this.setState({
			currentMonthName: initMonthName,
			currentYearName: initYearName,
			currentMonthNumber: initMonthData.monthNumber,
			currentYearNumber: parseInt(initYearName),
			visibleDates: dates
		}, () => {

			//Apply selected dates that already exist and create the colored squares between segment dates
			if(existingSegments.length > 0){
				this.updateSegmentDisplay(existingSegments);
			}

		});
	}

	/**
	 * Applies the buffered state (i.e. highlighted) to anything between selected segment dates
	 * @param {Array} All of the segments that have been selected so far 
	 * @param {Array} Segment dates that are located on this particular calendar
	 * @param {Array} The old state of all the dates stored in this calendar
	 * @return {Array} The new state of the dates stored in this calendar
	 */
	highlightDatesBetweenSegments(segments, segmentsInThisMonth, visibleDates){

		let newVisibleDates = visibleDates;

		//Determine if any buffers need to be drawn to connect items in this month to previous months
		for(let i = 0; i < segmentsInThisMonth.length; i++){

			//Create a date we can use to compare from the segments in this month
			let currentDate = moment(`${segmentsInThisMonth[i].id}`);

			for(let j = 0; j < segments.length; j++){

				//Create a date we can compare to from all the segments to figure out where it is in relation to our date
				let compareDate = moment(`${segments[j].id}`);
				
				//If the date located in this month is before the segment, and the segment # is higher 
				if(currentDate.isBefore(compareDate) && i <= j){

					//Everything between these two dates in this month needs to be buffered
					newVisibleDates = newVisibleDates.map((date) => {

						let dateInQuestion = moment(date.id);

						//If it's within range, buffer it
						if(dateInQuestion.isBefore(compareDate) && dateInQuestion.isAfter(currentDate) && date.active === true){
							date.buffered = true;
						}

						else{
							date.buffered = false;
						}

						return date;
					});
				}

				//If the date located in this month is after the segment, and the segment # is lower
				if(currentDate.isAfter(compareDate) && i >= j){

					//Everything between these two dates in this month needs to be buffered
					newVisibleDates = newVisibleDates.map((date) => {

						let dateInQuestion = moment(date.id);

						//If it's within range, buffer it
						if(dateInQuestion.isAfter(compareDate) && dateInQuestion.isBefore(currentDate) && date.active === true){
							date.buffered = true;
						}

						else{
							date.buffered = false;
						}

						return date;
					});
				}
			}
		}

		return newVisibleDates;
	}

	/**
	 * Determine how segments should appear on this calendar after an update to dates
	 * @param {Array} All of the segments that have been selected so far 
	 * @return {null} Set the state of this calendar with the new data
	 */
	updateSegmentDisplay(segments){
		let { currentMonthNumber, currentYearNumber, visibleDates } = this.state;

		let newVisibleDates = visibleDates;

		//Get all the segments that fall into this month
		let segmentsInThisMonth = segments.filter((segment, idx) => {
			return segment.monthNumber === currentMonthNumber && segment.yearNumber === currentYearNumber;
		});

		//Highlight anything between segments that falls on this particular calendar
		newVisibleDates = this.highlightDatesBetweenSegments(segments, segmentsInThisMonth, newVisibleDates);

		//Check if the date needs to be reselected and removed from selection
		newVisibleDates = newVisibleDates.map((date) => {

			let dateInQuestion = moment(date.id);

			for(let k = 0; k < segmentsInThisMonth.length; k++){

				//If it's one of the selected date, unbuffer it and select it
				if(dateInQuestion.isSame(moment(segmentsInThisMonth[k].id))){
					date.buffered = false;
					date.selected = true;
				}
			}

			return date;
		});
		
		//Set state and update UI
		this.setState({
			visibleDates: newVisibleDates
		});
	}	

	/**
	 * Determine whether or not we need to update state when new props received
	 * @param {Array} Existing segments in cache
	 * @param {Array} New segments from props
	 * @return {Boolean} CalendarData
	 */
	determineIfUpdateRequired(oldSegments, newSegments){

		//If they are not equal length, clearly something changed
		if(oldSegments.length !== newSegments.length){
			return true;
		}

		//We don't need to update if these two values are equal
		return !_.isEqual(oldSegments, newSegments);
		
	}

	componentDidMount() {

		let { year, month, segments } = this.props;

		//Get current calendar on init
		this.getCalendarData(year, month, segments);

	}

	componentDidUpdate(prevProps) {

		let { year, month, segments } = this.props;

		//Figure out if any dates have changed to update the ui
		if(this.determineIfUpdateRequired(prevProps.segments, segments)){
			//this.updateSegmentDisplay(this.props.segments);
			this.getCalendarData(year, month, segments);

		}
	}

	render() {
		let {selectDateCallback} = this.props;
		let {visibleDates, currentMonthNumber, currentMonthName, currentYearName} = this.state;
		let calendarElements = visibleDates.map((date) => renderMobileCalendarCell(date, selectDateCallback));

		return (
			<div className="mobile-calendar">
				<div className="mobile-calendar-header">
					<div>{currentMonthName} {currentYearName} </div>
				</div>
				<div className="mobile-calendar-container">
					{calendarElements}
				</div>
			</div>
		);
	}
}	