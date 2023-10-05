jQuery(document).ready(function($) {
    $('.s_p-block').slick({
      speed: 5000,
      autoplay: true,
      autoplaySpeed: 0,
      centerMode: true,
      cssEase: 'linear',
      slidesToShow: 3,
      slidesToScroll: 2,
      variableWidth: true,
      infinite: true,
      initialSlide: 1,
      arrows: false,
      buttons: false
    });
  });

$("#tab1").click(function () {
  $(".atm_block-img2").fadeOut();
  $(".atm_block-img3").fadeOut();
  $(".atm_block-img1").fadeIn();
});
$("#tab2").click(function () {
  $(".atm_block-img2").fadeIn();
  $(".atm_block-img3").fadeOut();
  $(".atm_block-img1").fadeOut();
});
$("#tab3").click(function () {
  $(".atm_block-img2").fadeOut();
  $(".atm_block-img3").fadeIn();
  $(".atm_block-img1").fadeOut();
});
