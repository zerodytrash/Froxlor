$(document).ready(function() {

	// make rel="external" links open in a new window
	$("a[rel='external']").attr('target', '_blank');

	// open specific tab if specified
	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
	}

	// Change hash for page-reload
	$('.nav-tabs a').on('shown.bs.tab', function(e) {
		window.location.hash = e.target.hash;
	});

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

	/**
	 * settings search
	 */
	$("#gs_text").click(function() {
		$("#gs_results").addClass("invisible");
		$("#gs_results .card-header").html("");
		$("#gs_results .card-body").html("");
	});

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	$('#gs_text').bind('keypress keydown keyup', function(e){
		if(e.keyCode == 13) { e.preventDefault(); }
	});

	$("#gs_text").keyup(function() {
		delay(function () {
			var searchtext = $("#gs_text").val();
			$("#gs_results").removeClass("invisible");
			$("#gs_results .card-header").html("Searching for " + searchtext + "...");
			if (searchtext.length <= 2) {
				$("#gs_results .card-header").html("Fehler");
				$("#gs_results .card-body").html("<div class=\"alert alert-danger\" role=\"alert\">Searchtext too short...</div>");
				return;
			}
			$.ajax({
				url: "index.php?module=AdminSettings&view=jqSearchSetting",
				type: "POST",
				data: {searchtext: searchtext },
				success: function(data) {
					$("#gs_results .card-body").html(data);
					console.log(data);
				},
				error : function(a, b) {
					console.log(a, b);
				}
			});
		}, 500);
	});
});
