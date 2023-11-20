let edtPassword = document.getElementsByName('password')[0];
if (!!edtPassword === false) edtPassword = document.getElementsByName('user_password')[0];

let divPasswordPolicies = document.getElementsByClassName('password-policies')[0];
let divPolicyLength = document.getElementsByClassName('policy-length')[0];
let divPolicyNumber = document.getElementsByClassName('policy-number')[0];
let divPolicyLetter = document.getElementsByClassName('policy-letter')[0];
let divPolicySpecial = document.getElementsByClassName('policy-special')[0];

edtPassword.value = '';

edtPassword.addEventListener('focus', function () {
  divPasswordPolicies.classList.add('active');
});

edtPassword.addEventListener('blur', function () {
  divPasswordPolicies.classList.remove('active');
  // verifyPassword(edtPassword);
});

edtPassword.addEventListener('keyup', function () {
  let password = edtPassword.value.trim();
  (/[0-9]/.test(password)) ? divPolicyNumber.classList.add('active') : divPolicyNumber.classList.remove('active');
  (/[a-zA-Z]/.test(password)) ? divPolicyLetter.classList.add('active') : divPolicyLetter.classList.remove('active');
  (/[^A-Za-z0-9]/.test(password)) ? divPolicySpecial.classList.add('active') : divPolicySpecial.classList.remove('active');
  (password.length > 4) ? divPolicyLength.classList.add('active') : divPolicyLength.classList.remove('active');
});

// function verifyPassword(input) {
//   if (!(/[0-9]/.test(input.value)) ||
//     !(/[a-zA-Z]/.test(input.value)) ||
//     !(/[^A-Za-z0-9]/.test(input.value)) ||
//     input.value.length < 5) {
//     alert('Verifique os critérios de senhas. Algo está faltando.');
//   }
// }
