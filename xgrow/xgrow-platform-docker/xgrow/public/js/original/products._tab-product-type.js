$('.btnPlan').click(function (e) {
  const type = e.currentTarget.dataset.plan;
  const typeField = $('#type');
  const value = type === 'single' ? typeField.val('P') : typeField.val('R');
  URLSearchParams.append('type', value)
  $('#informations-tab').removeClass('d-none');
  $('#informations-tab').addClass('active');
})
