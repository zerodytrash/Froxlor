$(document).ready(function() {

	// make rel="external" links open in a new window
	$("a[rel='external']").attr('target', '_blank');

	// Load Newsfeed
	var role = "";
	if (typeof $("#newsfeed").data("role") !== "undefined") {
		role = "&role=" + $("#newsfeed").data("role");
	}

	$.ajax({
		url : "index.php?module=NewsFeed" + role,
		type : "GET",
		success : function(data) {
			$("#newsfeed").html(data);
		},
		error : function(a, b) {
			console.log(a, b);
		}
	});

});
