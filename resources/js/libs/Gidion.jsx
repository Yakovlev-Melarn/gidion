export function convertDate(date) {
    var day = date.getDate();
    day = day < 10 ? "0" + day : day;
    var month = date.getMonth() + 1;
    month = month < 10 ? "0" + month : month;
    var year = date.getFullYear();
    return day + "." + month + "." + year;
}

export function getChartDay(dates, day = '') {
    if (day === 'next') {
        return dates.nextDay
    }
    if (day === 'prev') {
        return dates.prevDay
    }
    return dates.selectedDay
}
