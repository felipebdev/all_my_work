let toastOptions = {};

toastOptions = {
  animation: true,
  delay: 5000,
};

function successToast(title, msg) {
  changeToastStatus('success');
  changeToastTexts(title, msg);
}

function errorToast(title, msg) {
  changeToastStatus('error');
  changeToastTexts(title, msg);
}

function alertToast(title, msg) {
  changeToastStatus('alert');
  changeToastTexts(title, msg);
}

function infoToast(title, msg) {
  changeToastStatus('info');
  changeToastTexts(title, msg);
}

function changeToastStatus(type) {
  const toastCard = document.getElementsByClassName('xgrow-toast')[0];
  const toastCardHeader = document.getElementsByClassName('xgrow-toast-header')[0];

  const toastTypes = ['error', 'success', 'alert', 'info'];

  toastTypes.forEach(toastType => {
    if (toastCard.classList.contains(`xgrow-toast-${toastType}`)) toastCard.classList.remove(`xgrow-toast-${toastType}`);
    if (toastCardHeader.classList.contains(`xgrow-toast-header-${toastType}`)) toastCardHeader.classList.remove(`xgrow-toast-header-${toastType}`);
  });

  toastCard.classList.add(`xgrow-toast-${type}`);
  toastCardHeader.classList.add(`xgrow-toast-header-${type}`);
}

function changeToastTexts(title, msg){
  const toastHTMLElement = document.getElementById('dialogToast');
  const toastTitle = document.getElementById('toastTitle');
  const toastMessage = document.getElementById('toastMessage');
  toastTitle.innerText = title;
  toastMessage.innerText = msg;
  const toastElement = new bootstrap.Toast(toastHTMLElement, toastOptions);
  toastElement.show();
}
