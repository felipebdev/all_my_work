function convertFile(el, filename = 'download', title = '', header = [], content = '', data = []) {
    let text = '';
    if (el.target.dataset.type === 'pdf') {
        alert('Em implementação. Aguarde!');
    }
    if (el.target.dataset.type === 'csv') {
        text = header.join(',');
        text = text + '\n';
        data.map((item) => {
            text += `${item.title},${item.amount}\n`;
        });
        downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
    }
    if (el.target.dataset.type === 'xls') {
        text = '<table>' +
            '<thead>' +
            '<tr>';
        header.map((item) => {
            text += `<th>${item}</th>`;
        });
        text += '</tr>' +
            '</thead>' +
            '<tbody>';

        data.map((item) => {
            text += `<tr>
                              <td>${item.title}</td>
                              <td>${item.amount}</td>
                            </tr>`;
        });
        text += '</tbody>' +
            '</table>';

        downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');
    }
    if (el.target.dataset.type === 'txt') {
        text = title + '\n\n';
        header = header.join(',');
        text += header + '\n';
        data.map((item) => {
            text += `${item.title},${item.amount}\n`;
        });
        // data.map((item) => {
        //     // text += `Conteúdo: ${item.title} | Visualizações: ${item.amount}\n`;
        // });
        downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
    }
}
function downloadFile(text, format, filename) {
    const element = document.createElement('a');
    element.setAttribute('href', format + encodeURIComponent(text));
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function toKebabCase(str) {
    return str && str.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g).map(x => x.toLowerCase()).join('-');
}
