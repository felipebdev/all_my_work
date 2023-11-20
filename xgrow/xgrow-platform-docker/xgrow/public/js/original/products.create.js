$('.nextPage').click(function () {
  let atualTab = $('.nav-tabs > .active')
  atualTab.removeClass('active');
  atualTab.next('a').addClass('active');

  let atualContent = $('.tab-content > .show.active');
  atualContent.removeClass('show active');
  atualContent.next('div').addClass('show active');
});

$('.previousPage').click(function () {
  let atualTab = $('.nav-tabs > .active')
  atualTab.removeClass('active');
  atualTab.prev('a').addClass('active');

  let atualContent = $('.tab-content > .show.active');
  atualContent.removeClass('show active');
  atualContent.prev('div').addClass('show active');
});
