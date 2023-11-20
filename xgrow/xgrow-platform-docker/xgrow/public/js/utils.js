function downloadFile(text, format, filename) {
    const blob = new Blob([new Uint8Array([0xEF, 0xBB, 0xBF]), text], {type: format});
    const element = document.createElement('a');
    let a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
}

function toKebabCase(str) {
    return str && str.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g).map(x => x.toLowerCase()).join('-');
}
