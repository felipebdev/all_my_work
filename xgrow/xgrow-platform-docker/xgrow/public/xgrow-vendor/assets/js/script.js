const setCurrentSidenavState = (currentState) => {
    localStorage.setItem("sidenav-state", currentState);
};

const getCurrentSidenavState = () => {
    return localStorage.getItem("sidenav-state");
};

// $(window).on("load", function () {
//   $(".dom-container").fadeOut("slow", function () {
//     var content = $("#wrapper").toggleClass("d-none d-flex");
//     $(this).replaceWith(content);
//     $(".dom-container").fadeIn("slow");
//   });
// });

$(document).ready(function (e) {
    if (getCurrentSidenavState() === "toggled") {
        $("#wrapper").addClass("toggled");
        $("button.btn-icon-burguer").addClass("open");
    }
});

$("button.btn-icon-burguer").click(function (e) {
    e.preventDefault();
    if ($("#wrapper").hasClass("toggled")) {
        $("#wrapper").removeClass("toggled");
        $(this).removeClass("open");
        setCurrentSidenavState("untoggled");
    } else {
        $("#wrapper").addClass("toggled");
        $(this).addClass("open");
        setCurrentSidenavState("toggled");
    }
});

$(".xgrow-sidenav").mouseenter(function (e) {
    document.getElementById("sidebar-wrapper").scrollTop = 9999999;
});

const openSidenavWithBtn = () => {
    document.getElementById("wrapper").classList.remove("toggled");
    document.querySelector("button.btn-icon-burguer").classList.remove("open");
    setCurrentState("toggled");
};

// function sleep(ms) {
//   return new Promise((resolve) => setTimeout(resolve, ms));
// }

/* Anterior */
// $(".xgrow-sidenav").mouseover(function (e) {
//   e.preventDefault();
//   $("#wrapper").removeClass("toggled");
// });

// $(".xgrow-sidenav").mouseleave(async function (e) {
//   e.preventDefault();
//   await sleep(250);
//   $("#wrapper").addClass("toggled");
// });

// const changeChevronIcon = () => {
//   const selectButton = document.querySelector("button");
//   const faIcon = selectButton.childNodes[2];

//   if (faIcon.className.includes("fa-chevron-down")) {
//     faIcon.classList.remove("fa-chevron-down");
//     faIcon.classList.add("fa-chevron-right");
//   } else {
//     faIcon.classList.remove("fa-chevron-right");
//     faIcon.classList.add("fa-chevron-down");
//   }
// };
