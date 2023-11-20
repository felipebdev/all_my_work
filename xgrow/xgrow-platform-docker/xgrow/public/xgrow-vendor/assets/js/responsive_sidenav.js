const btnSidenav = document.querySelector(".navbar-toggler");
const btnIcon = document.querySelector("#burguer-icon");
const sidenavCollapsed = document.querySelector("#main-nav");

btnSidenav.addEventListener("click", (e) => {
  if (e.target.getAttribute("aria-expanded") === "false") {
    // sidenavCollapsed.style.display = "none";

    sidenavCollapsed.style.height = "64px";
    sidenavCollapsed.style.overflowX = "unset"
    sidenavCollapsed.style.zIndex = 1;
  }

  if (e.target.getAttribute("aria-expanded") === "true") {
    // sidenavCollapsed.style.display = "block";

    sidenavCollapsed.style.height = "100%";
    sidenavCollapsed.style.overflowX = "auto"
    sidenavCollapsed.style.zIndex = 999;
  }
});

btnIcon.addEventListener("click", () => {
  if (btnSidenav.getAttribute("aria-expanded") === "false") {
    // sidenavCollapsed.style.display = "none";

    sidenavCollapsed.style.height = "64px";
    sidenavCollapsed.style.overflowX = "unset"
    sidenavCollapsed.style.zIndex = 1;
  }

  if (btnSidenav.getAttribute("aria-expanded") === "true") {
    // sidenavCollapsed.style.display = "block";

    sidenavCollapsed.style.height = "100%";
    sidenavCollapsed.style.overflowX = "auto"
    sidenavCollapsed.style.zIndex = 999;
  }
});
