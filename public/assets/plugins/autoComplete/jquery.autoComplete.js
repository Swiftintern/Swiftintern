// awwto-compleet, by Saksham Saxena

/*
Notes : Testing will include parsing the json file only
*/

(function ($) {
	"use strict";
	// Variable Declaration
	var i, itemList, searchBox, sTerm, poss;
	// Plugin Definition
	$.fn.awwtocompleet = function () {
		// Get and check the input element

		if ($(this).is("input[type='text']")) {
			searchBox = $(this);
		}
		else {
			throw console.log("Attached the wrong element");
		}
		function fetcher(term) {
			$.ajax({
				url: "organizations.json?name="+term,
				dataType: "json",
				complete: function (data) {
					itemList = (JSON.parse(data.responseText).organizations); //array of object
					console.log(itemList);
					return itemList;
				}
			})
		}

		// Fetch value from attached input field, search and append
		function main() {
			sTerm = searchBox.val().toLowerCase();
			fetcher(sTerm);
			for (i = 0; i < itemList.length; i++) {
				poss = (itemList[i]._name.toLowerCase()).slice(0, (sTerm.length));
				if (sTerm === poss) {
					$("ul.awwList").append("<a href=" + itemList[i]._website + "><li class='awwItem'>" + itemList[i]._name + "</li></a>");
				}
			}
		}

		// Attach 2 to onfocus, Remove 2 on blur
		searchBox.on("focus", function () {
			//1. Append div to body
			$("body").append("<div class='awwListCont'></div>");
			$("div.awwListCont").css({
					width: (searchBox.width()).toString() + "px"
				})
				//2. Make a transparent ul
			$("div.awwListCont").append("<ul class='awwList'></ul>");
		});

		searchBox.on("blur", function () {
			$("div.awwListCont").remove();
		})

		// Attach 3 to onkeyup delayed callback, 4 onkeyup everytime
		searchBox.on("keyup", function () {
			$("li.awwItem").remove();
			setTimeout(main(), 200);
		})

	};
}(jQuery));