// const setSwitchOn = () => {
//   document.getElementById("theme-slider").checked = "true";
// };

// const setSwitchOff = () => {
//   document.getElementById("theme-slider").removeAttribute("checked");
// };

const setTheme = (selectedTheme) => {
  document.documentElement.className = selectedTheme;
  localStorage.setItem("theme", selectedTheme);
};

const setXgrowLogo = (newLogo) => {
  // let domLogo = document.getElementById("dom-logo");
  let wideLogo = document.getElementById("wide-logo");
  let wideLogoMobile = document.getElementById("wide-logo-mobile");

  if (newLogo.includes("dark")) {
    // domLogo.setAttribute(
    //   "src",
    //   "/xgrow-vendor/assets/img/logo_wide_darkmode.svg"
    // );
    wideLogo.setAttribute(
      "src",
      "/xgrow-vendor/assets/img/logo_wide_darkmode.svg"
    );
    wideLogoMobile.setAttribute(
      "src",
      "/xgrow-vendor/assets/img/logo_wide_darkmode.svg"
    );
  } else {
    // domLogo.setAttribute(
    //   "src",
    //   "/xgrow-vendor/assets/img/logo_wide_lightmode.svg"
    // );
    wideLogo.setAttribute(
      "src",
      "/xgrow-vendor/assets/img/logo_wide_lightmode.svg"
    );
    wideLogoMobile.setAttribute(
      "src",
      "/xgrow-vendor/assets/img/logo_wide_lightmode.svg"
    );
  }
};

const selectTheme = (option) => {
  let theme = ["dark-mode", "light-mode"];
  setTheme(theme[option]);
  setXgrowLogo(theme[option]);
  // option === 1 ? setSwitchOn() : setSwitchOff();
};

const toggleTheme = () => {
  if (localStorage.getItem("theme").includes("dark")) {
    selectTheme(1);
  } else {
    selectTheme(0);
  }
};

// Immediately invoked function to set the theme on initial load
(async function () {
  if (!localStorage.getItem("theme")) {
    selectTheme(0);
  } else {
    localStorage.getItem("theme").includes("light")
      ? selectTheme(1)
      : selectTheme(0);
  }
})();
