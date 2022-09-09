function notifMessage(textMsg, typeMsg = 'info') {
  if (typeMsg === 'error') {
    iconMessage = 'fas fa-times-circle';
  }else if(typeMsg === 'success'){
    iconMessage = 'fas fa-check-circle';
  }else{
    iconMessage = 'fas fa-info-circle';
  }

  Lobibox.notify(typeMsg, {
    pauseDelayOnHover: true,
    continueDelayOnInactiveTab: false,
    size: 'mini',
		rounded: true,
    icon: iconMessage,
    delay: 2500, 
    position: 'top right',
    msg: textMsg,
    sound: false,
  });
}

function read_error(xhr) {
  var textMsg = '<strong></strong>';
  var response = [];

  if (isJson(xhr.responseText)) {
    response = JSON.parse(xhr.responseText);
  }

  $.each(response, function (x, y) {
    textMsg += y + '<br>';
  });

  notifMessage(textMsg, 'error');

}

function inArray(needle, haystack) {
  var length = haystack.length;
  for(var i = 0; i < length; i++) {
      if(typeof haystack[i] === 'object') {
          if(arrayCompare(haystack[i], needle)) return true;
      } else {
          if(haystack[i] == needle) return true;
      }
  }
  return false;
}


function date_en(string) {
	  months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September' , 'October', 'November', 'December'];
 
    date = string.split("-")[2];
    month = string.split("-")[1];
    year = string.split("-")[0];
 
    return  months[Math.abs(month)] + " " + date + ", " + year;
}

function date_id_full(string) {
  	bulanIndo = ['', 
    'Januari', 
    'Februari', 
    'Maret', 
    'April', 
    'Mei', 
    'Juni', 
    'Juli', 
    'Agustus', 
    'September' , 
    'Oktober', 
    'November', 
    'Desember'
  ];

    tanggal = string.split("-")[2];
    bulan = string.split("-")[1];
    tahun = string.split("-")[0];

    return tanggal + " " + bulanIndo[Math.abs(bulan)] + " " + tahun;
}

function isJson(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

function date_id(param) {
	if (param != null && param != "" && param != undefined) {
		var year = param.substr(0, 4);
		var month = param.substr(5, 2);
		var day = param.substr(8, 2);

		var tgl = day + '/' + month + '/' + year;
		return (tgl);
	} else {
		return '';
	}
}

function inArray(needle, haystack) {
  var length = haystack.length;
  for(var i = 0; i < length; i++) {
      if(typeof haystack[i] === 'object') {
          if(arrayCompare(haystack[i], needle)) return true;
      } else {
          if(haystack[i] == needle) return true;
      }
  }
  return false;
}


$(function() {

  $(function() {
		$("#menu").metisMenu()
	})


  $(".nav-toggle-icon").on("click", function() {
		$(".wrapper").toggleClass("toggled")
	})

  $(".mobile-menu-button").on("click", function() {
		$(".wrapper").addClass("toggled")
	})

	$(".toggle-icon").click(function() {
		$(".wrapper").hasClass("toggled") ? ($(".wrapper").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($(".wrapper").addClass("toggled"), $(".sidebar-wrapper").hover(function() {
			$(".wrapper").addClass("sidebar-hovered")
		}, function() {
			$(".wrapper").removeClass("sidebar-hovered")
		}))
	})

  $(".btn-mobile-filter").on("click", function() {
		$(".filter-sidebar").removeClass("d-none");
	}),
  
    $(".btn-mobile-filter-close").on("click", function() {
		$(".filter-sidebar").addClass("d-none");
	}),




  $(".mobile-search-button").on("click", function() {

    $(".searchbar").addClass("full-search-bar")

  }),

  $(".search-close-icon").on("click", function() {

    $(".searchbar").removeClass("full-search-bar")

  }),

  


  $(document).ready(function() {
		$(window).on("scroll", function() {
			$(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
		}), $(".back-to-top").on("click", function() {
			return $("html, body").animate({
				scrollTop: 0
			}, 600), !1
		})
	})




  $(".dark-mode-icon").on("click", function() {

    if($(".mode-icon .fas").hasClass("fa-moon") ) {
        $(".mode-icon .fas").removeClass("fa-moon");
        $(".mode-icon .fas").addClass("fa-sun");
        $("html").attr("class", "dark-theme");
    } else {
        $(".mode-icon .fas").removeClass("fa-sun");
        $(".mode-icon .fas").addClass("fa-moon");
        $("html").attr("class", "light-theme")
    }

  }), 

  // Tooltops
  $(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
  })
    
});