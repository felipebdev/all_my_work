<template id="field-template">
    @include('audience.components.fields.field')
</template>

{{-- operator --}}

<template id="operator-template">
    @include('audience.components.operator')
</template>

{{-- input values --}}

<template id="date-input-template">
    @include('audience.components.input-values.input-date')
</template>

<template id="gender-input-template">
    @include('audience.components.input-values.input-gender')
</template>

<template id="plan-input-template">
    @include('audience.components.input-values.input-product')
</template>

<template id="states-input-template">
    @include('audience.components.input-values.input-states')
</template>

<template id="status-input-template">
    @include('audience.components.input-values.input-status')
</template>

<template id="person-input-template">
    @include('audience.components.input-values.input-person')
</template>

<template id="payment_method-input-template">
    @include('audience.components.input-values.input-payment_method')
</template>

<template id="person-input-subscriber-type">
    @include('audience.components.input-values.input-lead')
</template>

<template id="person-input-single-sale">
    @include('audience.components.input-values.input-single-sale')
</template>

<template id="person-input-subscription">
    @include('audience.components.input-values.input-subscription')
</template>

<template id="person-input-nolimit">
    @include('audience.components.input-values.input-nolimit')
</template>
