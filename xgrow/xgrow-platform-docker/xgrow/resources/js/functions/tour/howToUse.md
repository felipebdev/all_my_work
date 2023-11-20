# How to use the __XGrow Tour__?

_before use, check if [driver.js](https://github.com/kamranahmedse/driver.js) is installed_

## __Javascript setup__
In your javascript file, import the tour main file (index.js), remember to update for your relative path
```js
import XGrowTour from 'resources/js/functions/tour';
```
## __Functions__
The XGrowTour have 3 usable functions: _initialize_, _markAsDone_, _checkIfTourIsDone_.

### initialize
```js
XGrowTour.initialize(tourName, steps)
```
For the initialize function, you need to pass two parameters, a string __tourName__ and an array __steps__. The tourName is the name of the tour you are creating, and the steps is an array of objects, and every object is a step of your tour, they have to follow this pattern below:
```js
const steps = [
    {
        elementId: '#id', // id of your HTML element
        title: 'Title',
        description: '<p>Description</p>', // can be HTML or plain text
        position: 'top', // top, bottom, left, right
        customClasses: [] // not needed attribute, is your custom css classes
    }
];
```
You don't need to call _checkIfTourIsDone_ before starting the tour, and you don't need to call _markAsDone_ in the end, the _initialize_ function already do all of this.

### markAsDone
```js
XGrowTour.markAsDone(tourName)
```
Simple function to save if the tour is already done, only parameter needed is the name of the tour

### checkIfTourIsDone
```js
XGrowTour.checkIfTourIsDone(tourName)
```
This function returns a boolean if the tour is already done or not

## __Usage Example__
```html
<template>
    <div>
        <div id="elementOne">
            First element
        </div>
        <div id="elementTwo">
            Second element
        </div>
    </div>
</template>

<script>
import XGrowTour from 'resources/js/functions/tour';

export default {
    name: "my-component",
    created: function () {
        const tourName = 'myTour';
        const steps = [
            {
                elementId: '#elementOne',
                title: 'Title of my first step',
                description: '<p>Detailed description of my first step</p>',
                position: 'top',
                customClasses: ['green-background']
            },
            {
                elementId: '#elementTwo',
                title: 'Title of my second step',
                description: '<p>Detailed description of my second step</p>',
                position: 'right',
            }
        ];

        XGrowTour.initialize(tourName, steps);
    },
}
</script>

<style>
div#driver-popover-item.green-background {
    background-color: green;
}
</style>
```