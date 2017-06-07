function renderCalendar(calendar, segments, selectDateCallback) {
	return <MobileCalendar key={`${calendar.month}-${calendar.year}`} segments={segments} selectDateCallback={selectDateCallback} year={calendar.year} month={calendar.month}/>;
}

class CalendarModal extends React.Component {

	constructor() {
		super();

		this.state = {
			displayedMonths: [], //Which months are we currently displaying
			currentSegment: 0,
			flightType: "roundtrip", //oneway, multi, roundtrip
			segments: [] //Segments are in order with the location for tooltip and date for calendar
		};

		this.selectDate = this.selectDate.bind(this);
		this.updateHiddenFields = this.updateHiddenFields.bind(this);
		this.resetCalendarState = this.resetCalendarState.bind(this);
	}

	/**
	 * Callback for a clicked date cell. Different rules depending on what kind of flight it is
	 * @param {Object} date cell 
	 * @return {null} setState to reflect new data
	 */
	selectDate(date) {
		let { segments, currentSegment, flightType } = this.state;

		//Make a copy of the existing array so we don't mutate state!
		let newSegments = [].concat(segments);
		let newSegment = {};
		let nextSegment = 0;

		date.selected = true;

		//Create new segment object and push it
		newSegments.push(date);

		//Increase the current segment we are on
		nextSegment++;

		//Push changes to state and refresh template. Populate hidden fields after changes applied
		this.setState({
			segments: newSegments,
			currentSegment: nextSegment
		}, this.updateHiddenFields);
	}

	/**
	 * Sync hidden input values with the segment values from react
	 * @return {null} sets the hidden inputs values
	 */
	updateHiddenFields(){
		let { seg0_date_mirror, seg1_date_mirror, seg2_date_mirror, seg3_date_mirror, seg4_date_mirror } = this.refs;
		let { segments } = this.state;

		//Set hidden inputs
		seg0_date_mirror.value = segments[0] ? segments[0].id : "";
		seg1_date_mirror.value = segments[1] ? segments[1].id : "";
		seg2_date_mirror.value = segments[2] ? segments[2].id : "";
		seg3_date_mirror.value = segments[3] ? segments[3].id : "";
		seg4_date_mirror.value = segments[4] ? segments[4].id : "";
	}

	/**
	 * Convert php injected segments into valid data the app can use
	 * @param {Array} segments
	 * @return {null} state friendly segment array
	 */
	mapSegments(segments){

		//Map internal UI components
		let internalSegments = segments.filter((segment) => {

			//Filter out segments that don't have any data, we don't need them internally
			if(segment.length === 0){
				return false;
			}

			return true;

		}).map((segment) => {

			//Map existing segments to valid segment objects
			let rawSegment = moment(segment);

			let newSegment = {
				id: segment,
				number: rawSegment.date(),
				monthNumber: rawSegment.month(), 
				yearNumber: rawSegment.year(),
				weekdayNumber: rawSegment.day(),
				active: true,
				selected: true,
				buffered: false
			}

			return newSegment;

		});

		return internalSegments;
	}

	/**
	 * Reset the state of the calendar
	 * @return {null} resets state
	 */
	resetCalendarState() {
		this.setState({
			segments: []
		});
	}
	
	componentDidMount() {

		//Grab segments from php generated container
		let { existingSegments, flightType } = this.props;

		//Get default data on init
		let currentDay = moment();

		//Jan = 1, Feb = 2, etc.
		let monthNumber = currentDay.month() + 1;

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
			<div className="calendar-close-button"> CLOSE </div>
			{calendarElements}
			<div className="calendar-reset-button" onClick={this.resetCalendarState}> RESET </div>
			<div>
				<input id="seg0_date_mirror" ref="seg0_date_mirror" type="hidden" value={segments[0] ? segments[0].id : ""}/>
				<input id="seg1_date_mirror" ref="seg1_date_mirror" type="hidden" value={segments[1] ? segments[1].id : ""}/>
				<input id="seg2_date_mirror" ref="seg2_date_mirror" type="hidden" value={segments[2] ? segments[2].id : ""}/>
				<input id="seg3_date_mirror" ref="seg3_date_mirror" type="hidden" value={segments[3] ? segments[3].id : ""}/>
				<input id="seg4_date_mirror" ref="seg4_date_mirror" type="hidden" value={segments[4] ? segments[4].id : ""}/>
			</div>
			</div>
		);
	}
}	