var timeLeft = 5;
var elem = $('.timer');
var timerId = setInterval(countdown, 1000);
function countdown() {
    if (timeLeft === -1) {
        clearTimeout(timerId);
        location.reload();
    } else {
        elem.html(timeLeft);
        timeLeft--;
    }
}
