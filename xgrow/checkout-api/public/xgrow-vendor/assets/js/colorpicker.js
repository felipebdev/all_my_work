const optionsColorPicker = {
    appClass: 'xgrow-colorpicker',
    el: '.color-picker',
    theme: 'classic',
    comparison: false,
    lockOpacity: true,
    closeWithKey: 'Escape',
    defaultRepresentation: 'HEX',
    //Aqui pode adicionar as ultimas cores que o usuário escolheu através de alguma função
    swatches: ['#F44336', '#E91E63', '#9C27B0', '#673AB7'],
    components: {
        // Main components
        preview: true,
        opacity: true,
        hue: true,
        // Input / output Options
        interaction: {
            input: true,
        },
    },
    i18n: {
        // Strings visible in the UI
        'btn:save': 'Salvar',
        'btn:cancel': 'Cancelar',
        'btn:clear': 'Clear',
    },
};

/* Copiar o trecho abaixo no blade e colocar a quantidade total de campos de cores no lugar do 2*/
// for (let i = 0; i < 2; i++) {
//     Pickr.create(optionsColorPicker);
// }
