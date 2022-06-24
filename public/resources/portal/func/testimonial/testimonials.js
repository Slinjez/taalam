$(':radio').change(function () {
    console.log('New star rating: ' + this.value);
    let ratingValue = this.value;
    var msg = "";
    if (ratingValue > 1) {
        msg = "Thanks! You rated this " + ratingValue + " star.";
    } else {
        msg = "We will improve ourselves. You rated this " + ratingValue + " stars.";
    }
    $('.rating-feedback').html(msg);
});