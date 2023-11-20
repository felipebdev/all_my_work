import Driver from 'driver.js';
import 'driver.js/dist/driver.min.css';
import './tour.css';

/**
 * Check if the tour is already done by the user
 * @param {String} tourName 
 * @returns {Boolean}
 */
function checkIfTourIsDone(tourName) {
  const isDone = localStorage.getItem(`${tourName}IsDone`);
  return Boolean(isDone || false);
}

/**
 * Treat the steps for the driver.js pattern
 * @param {Array} steps 
 * @returns {Array}
 */
async function treatSteps(steps) {
  const buttons = {
    doneBtnText: 'Finalizar tour <i class="fas fa-check"></i>',
    nextBtnText: 'Próxima dica <i class="fas fa-angle-right"></i>',
    prevBtnText: '<i class="fas fa-angle-left"></i> Dica anterior',
    closeBtnText: 'Cancelar tour <i class="fas fa-times"></i>',
  };

  const stepsLen = steps.length;
  return await steps.map((step, i) => {
    let customClasses = 'xgrow-popover ';
    if (Array.isArray(step.customClasses)) {
      customClasses += step.customClasses.join(" ");
    } else {
      customClasses += step.customClasses;
    }

    return {
      element: step.elementId,
      popover: {
        className: customClasses,
        title: `<i class="fas fa-info-circle"></i> ${step.title}`,
        description: step.description,
        position: window.innerWidth > 991 ? step.position : 'bottom',
        ...buttons
      }
    }
  });
}

/**
 * Mark the tour in local storage as done
 * @param {String} tourName 
 */
function markAsDone(tourName) {
  localStorage.setItem(`${tourName}IsDone`, true);
}

/**
 * Initialize the tour
 * @param {String} tourName 
 * @param {Array} steps 
 */
async function initialize(tourName, steps) {
  if (tourName == null || tourName == '') {
    console.error("The tourName parameter cannot be null");
    return;
  }

  if (steps == null || steps == []) {
    console.error("The steps parameter cannot be null");
    return;
  }

  if (checkIfTourIsDone(tourName)) return;

  const treatedSteps = await treatSteps(steps);
  treatedSteps.at(-1).onNext = () => markAsDone(tourName)
  
  const driver = new Driver();
  driver.defineSteps(treatedSteps);
  driver.start();
}

export default { initialize, markAsDone, checkIfTourIsDone };