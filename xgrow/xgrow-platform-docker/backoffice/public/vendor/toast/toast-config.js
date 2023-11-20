const toastOptions = {
  animation: true,
  delay: 3000,
};

function successToast(title, msg) {
  changeToastStatus('success');
  changeToastTexts(title, msg);
}

function errorToast(title, msg) {
  changeToastStatus('error');
  changeToastTexts(title, msg);
}

function changeToastStatus(type) {
  const toastCard = document.getElementsByClassName('xgrow-toast')[0];
  const toastCardHeader = document.getElementsByClassName('xgrow-toast-header')[0];
  if (toastCard.classList.contains('xgrow-toast-error')) {
    toastCard.classList.remove('xgrow-toast-error');
  }
  if (toastCard.classList.contains('xgrow-toast-success')) {
    toastCard.classList.remove('xgrow-toast-success');
  }
  if (toastCardHeader.classList.contains('xgrow-toast-header-error')) {
    toastCardHeader.classList.remove('xgrow-toast-header-error');
  }
  if (toastCardHeader.classList.contains('xgrow-toast-header-success')) {
    toastCardHeader.classList.remove('xgrow-toast-header-success');
  }
  if (type === 'error') {
    toastCard.classList.add('xgrow-toast-error');
    toastCardHeader.classList.add('xgrow-toast-header-error');
  } else {
    toastCard.classList.add('xgrow-toast-success');
    toastCardHeader.classList.add('xgrow-toast-header-success');
  }
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
