function renderMobileCalendarCell(date){

	let style = "mobile-calendar-cell";

	if(!date.active){
		style+= " inactive";
	}

	if(date.weekdayNumber === 0 || date.weekdayNumber === 6){
		style+= " weekend";
	}

	//Weekends appear smaller than weekdays
	if(date.weekdayNumber === 0 || date.weekdayNumber === 6){
		return <div key={`${date.monthNumber}-${date.number}`} className={style}> {date.number} </div>;
	}

	return <div key={`${date.monthNumber}-${date.number}`} className={style}> {date.number} </div>;
}

class MobileCalendar extends React.Component {

	constructor() {
		super();

		this.state = {
			name: "Test Cal",
			currentMonthName: "",
			currentYearName: "",
			dates: []
		}

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

		return {firstDay: firstDay, monthNumber: monthNumber, numDays: numDays, numWeeks: numWeeks};
	}

	/**
	 * Generates all the date cells in a given month in a given year
	 * @param {Number} year 
	 * @param {Number} month (true month #, not 0-based! i.e. April = 4)
	 * @return {Array} calendarData
	 */
	getCalendarData(year, month) {

		//If no month or year, get the current month and year, and get the name of the month
		let initMonth = year && month ? moment([year, month - 1]) : moment();
		let initMonthName = year && month ? moment([year, month - 1]).format("MMMM") : moment().format("MMMM");
		let initYearName = year && month ? moment([year, month - 1]).format("YYYY") : moment().format("YYYY");

		//Get the previous month and next month in case we need to fill in that data
		let prevMonth = year && month ? moment([year, month - 1]).subtract(1, "months") : moment().subtract(1, "months");
		let nextMonth = year && month ? moment([year, month - 1]).add(1, "months") : moment().add(1, "months");

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

			//Create ghost cells before
			if(i < initMonthData.firstDay){

				//Calculate what day it should be from the previous month to fill empty space at the beginning
				currentDayNumber = prevMonthData.numDays - Math.abs(i - initMonthData.firstDay + 1);
				currentCell = {
					number: currentDayNumber, 
					monthNumber: prevMonthData.monthNumber, 
					weekdayNumber: weekdayNumber, 
					active: false, 
					selected: false
				};
			}

			//Create ghost cells after
			else if(i >= initMonthData.firstDay + initMonthData.numDays){

				//Calculate what day it should be from the next month to fill empty space at the end
				currentDayNumber = Math.abs(i - (initMonthData.firstDay + initMonthData.numDays) + 1);
				currentCell = {
					number: currentDayNumber, 
					monthNumber: nextMonthData.monthNumber, 
					weekdayNumber: weekdayNumber, 
					active: false, 
					selected: false
				};
			}

			//Create a cell for the current month of the calendar
			else {
				currentCell = {
					number: (i - initMonthData.firstDay) + 1,
					monthNumber: initMonthData.monthNumber, 
					weekdayNumber: weekdayNumber, 
					active: true, 
					selected: false
				};
			}

			//Push the new date cell to the array of cells
			dates.push(currentCell);
		}	

		//Push the new calendar data to state to propagate changes to DOM
		this.setState({
			currentMonthName: initMonthName,
			currentYearName: initYearName,
			dates: dates
		});
	}

	componentDidMount() {

		//Get current calendar on init
		this.getCalendarData(2017, 12);

	}

	render() {
		let {dates, currentMonthName, currentYearName} = this.state;
		let calendarElements = dates.map((date) => renderMobileCalendarCell(date));

		return (
			<div className="mobile-calendar-modal">
				<div className="mobile-calendar-header"> {currentMonthName} {currentYearName} </div>
				<div className="mobile-calendar-container">
					{calendarElements}
				</div>
			</div>
		);
	}
}	