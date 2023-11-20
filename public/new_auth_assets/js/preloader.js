(function ($) {
  "use strict";

  /*============= preloader js css =============*/
  var cites = [];
  cites[0] = "Next Payday is an alternative finance company.";
  cites[1] = "We are the 'Uber' of Payday Loans.";
  cites[2] = "We provide payday loans to validly employed Nigerians through a network of investors.";
  cites[3] = "We support salary advance and payroll loans of employees in verified employers signed on the App.";
  var cite = cites[Math.floor(Math.random() * cites.length)];
  $("#preloader p").text(cite);
  $("#preloader").addClass("loading");

  $(window).on("load", function () {
    setTimeout(function () {
      $("#preloader").fadeOut(500, function () {
        $("#preloader").removeClass("loading");
      });
    }, 500);
  });
})(jQuery);
