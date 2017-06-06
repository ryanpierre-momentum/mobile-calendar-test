function renderCalendar(calendar, segments, selectDateCallback) {
	return <MobileCalendar key={`${calendar.month}-${calendar.year}`} segments={segments} selectDateCallback={selectDateCallback} year={calendar.year} month={calendar.month}/>;
}

class CalendarModal extends React.Component {

	constructor() {
		super();

		this.state = {
			displayedMonths: [], //Which months are we currently displaying
			currentSegment: 0,
			segments: [] //Segments are in order with the location for tooltip and date for calendar
		};

		this.selectDate = this.selectDate.bind(this);
	}

	/**
	 * Callback for a clicked date cell
	 * @param {Object} date cell 
	 * @return {null} setState to reflect new data
	 */
	selectDate(date) {
		let { segments } = this.state;

		//Make a copy of the existing array so we don't mutate state!
		let newSegments = [].concat(segments);
		let newSegment = {};
		let nextSegment = 0;

		//If there are no segments, we can simply select the day
		if(newSegments.length === 0){

			date.selected = true;

			//Create new segment object and push it
			newSegments.push(date);

			//Increase the current segment we are on
			nextSegment++;
		}

		//Push changes to state and refresh template
		this.setState({
			segments: newSegments,
			currentSegment: nextSegment
		});
	}

	/**
	 * Convert php injected segments into valid data the app can use
	 * @param {Array} segments
	 * @return {null} state friendly segment array
	 */
	mapSegments(segments){
		return [];
	}
	
	componentDidMount() {

		//Grab segments from php injection
		let { existingSegments } = this.props;

		//Get default data on init
		let currentDay = moment();

		//Jan = 0, Feb = 1, etc.
		let monthNumber = currentDay.month();

		//1:1 with normal years, just called yearNumber for consistency
		let yearNumber = currentDay.year();

		//Create an object for that month the calendar can parse, as well as the next two
		//TODO: This is lazy for testing do this with smarter detection of year turnovers
		let currentMonthObj = {month: monthNumber, year: yearNumber}
		let nextMonthObj = {month: monthNumber + 1, year: yearNumber}
		let nextNextMonthObj = {month: monthNumber + 2, year: yearNumber}

		//Map existing segments into friendly data
		let segmentData = this.mapSegments(existingSegments);

		//Push changes to state and refresh template
		this.setState({
			displayedMonths: [currentMonthObj, nextMonthObj, nextNextMonthObj],
			segments: segmentData
		});
	}

	render() {
		let { displayedMonths, segments } = this.state;
		let calendarElements = displayedMonths.map((month) => renderCalendar(month, segments, this.selectDate));

		return (
			<div className="calendar-modal">
				{calendarElements}
			</div>
		);
	}
}	