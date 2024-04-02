function scrollToSection(sectionId) {
    $('html, body').animate({
      scrollTop: $(sectionId).offset().top
    }, 1000);
  }