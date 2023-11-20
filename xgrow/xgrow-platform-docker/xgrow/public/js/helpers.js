function resumeString(data, max) {
  max = Number.isInteger(max) ? max : 15;
  if (data.length > max) {
    resume = `<span title="${data}">${data.substr(0, max)} …</span>`
  } else {
    resume = data;
  }
  return resume
}

function GetVimeoIDbyUrl(url) {
  var id = false;
  $.ajax({
    url: 'https://vimeo.com/api/oembed.json?url=' + url,
    async: false,
    success: function (response) {
      if (response.video_id) {
        id = response.video_id;
      }
    }
  });
  return id;
}

function tryToCorrectVimeo(url) {
  vimeo_id = GetVimeoIDbyUrl(url);
  if (vimeo_id) {// usando api/vimeo
    return `https://player.vimeo.com/video/${vimeo_id}`;
  } else { //usando REGEX
    var result = url.match(/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/i);
    if (result !== null)
      return `https://player.vimeo.com/video/${result[1]}`;
  }
  errorToast('erro', 'Formato de vídeo não reconhecido');
  return '';
}

function matchYoutubeUrl(url) {
  const p = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
  return !!url.match(p);

}

function checkFormatVideo(url) {
  let type = 'inválido';

  if (matchYoutubeUrl(url)) {
    type = 'Youtube';
  } else if (url.indexOf('vimeo') > -1) {
    type = 'Vimeo';
    if (!matchVimeoUrl(url)) {
      type += ' inválido';
    }
  }
  return type;
}


function checkVimeo(url) {
  const p = /^vimeo$/;
  return !!url.match(p);

}

function matchVimeoUrl(url) {
  //var p = /^(http\:\/\/|https\:\/\/)?(www\.|player\.)?(vimeo\.com\/)(video\/)?([0-9]+)$/;
  const p = /^https\:\/\/player\.vimeo\.com\/video\/([0-9]+)$/;
  return !!url.match(p);

}


// YYYY: ano com quatro dígitos;
// MM: mês;
// DD: dia;
// T: indicação de início das horas;
// HH: horas;
// mm: minutos;
// ss: segundos;
// s: milissegundos;
// TZD: time zone, que corresponde a +hh:mm ou -hh:mm.
function formatDatePTBR(dateEN) {
  let dataAtual = new Date(dateEN);
  return (addZero((dataAtual.getDate() + 1 ).toString()) + '/' + (addZero(dataAtual.getMonth() + 1).toString()) + '/' + dataAtual.getFullYear());
}

function formatDateTimePTBR(datetimeEN) {
  let datetimeAtual = new Date(datetimeEN);
  return (addZero(datetimeAtual.getDate().toString()) + '/' + (addZero(datetimeAtual.getMonth() + 1).toString()) + '/' + datetimeAtual.getFullYear() + ' ' + addZero(datetimeAtual.getHours()) + ':' + addZero(datetimeAtual.getMinutes()) + ':' + addZero(datetimeAtual.getSeconds()));
}


function addZero(numero) {
  if (numero <= 9)
    return '0' + numero;
  else
    return numero;
}

function formatCoin(value, currency = 'BRL', addSymbol = true) {
  if (!value && value != 0) return;
  value = parseFloat(value);

  let locale = 'pt-br';
  if (currency === 'USD') locale = 'en';

  if (addSymbol) {
    return value.toLocaleString(locale, {style: 'currency', currency});
  } else {
    return value.toLocaleString(locale, {style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2});
  }
}

if (typeof pdfMakeWrapperDownloader != 'function') {
  /**
   * Generates PDF from data using only JS.
   * Uses lib pdfMake
   *
   * @param title Document title
   * @param headerCols array Headers, eg: ['Header 1', 'Header 2']
   * @param data "Matrix", eg: [['A1', 'B1'] , ['A2', 'B2']]
   * @param filename Filename for download
   */
  window.pdfMakeWrapperDownloader = function (title, headerCols, data, filename) {

    const header = headerCols.map(function (column) {
      return {text: column, style: 'tableHeader'};
    });

    const widths = headerCols.map(function (column) {
      return '*';
    });

    const body = [];
    body.push(header);

    data.map(function (row) {
      body.push(row);
    });

    const docDefinition = {};
    docDefinition.content = [
      {
        text: title,
        style: 'title'
      },
      {
        style: 'tableExample',
        table: {
          widths: widths,
          body: body
        }
      }
    ];
    docDefinition.styles = {
      title: {
        bold: true,
        fontSize: 13,
        color: 'black',
        alignment: 'center'
      },
      tableHeader: {
        bold: true,
        fontSize: 13,
        color: 'black',
        alignment: 'center'
      }
    };
    pdfMake.createPdf(docDefinition).download(filename);
  };
}

function parseDatatablesDate(rawDate) {
  const dateArray = rawDate.split('/');
  const parsedDate = dateArray[2] + dateArray[1] + dateArray[0];
  return parsedDate;
}

/**
 * Formatters
 */
var formatter = {};

/**
 * Keep only digits
 *
 * @param {string} string
 * @return {string} String with only digits
 */
formatter.onlyDigits = function (string) {
  return string.replace(/[^\d]+/g, '');
};

/**
 * Truncate strings adding ellipsis (...) to the end of string
 *
 * @param string
 * @param maxSize
 * @return {string}
 */
formatter.ellipsis = function (string, maxSize = 100) {
  return (string.length > maxSize) ? string.substr(0, maxSize - 1) + '...' : string;
};

/**
 * Convert newline ('\n' and related) to '<br>' tag
 */
formatter.newLineToBr = function (string) {
  return string.replace(/\r\n|\r|\n/g, '<br> ');
};

// Date and time formatters.
// Uses only string manipulation, preventing conversion and timezone problems

/**
 * "Smart" date/datetime converter to Brazilian date.
 *
 * @param {string} date Format YYYY-MM-DD or YYYY-MM-DD HH:MM:SS
 * @return {string} Formatted date DD/MM/YYYY
 */
formatter.toBrDate = function (date) {
  const stripped = formatter.onlyDigits(date);
  if (stripped.length === 8) {
    return formatter.dateToBrDate(date);
  } else if (stripped.length === 14) {
    return formatter.datetimeToBrDate(date);
  }

  return date;
};

/**
 * "Smart" date/datetime converter to Brazilian date and time
 *
 * @param {string} date Format YYYY-MM-DD or YYYY-MM-DD HH:MM:SS
 * @return {string} Formatted date,
 *  DD/MM/YYYY if input is date,
 *  DD/MM/YYYY HH:MM:SS if input is datetime
 */
formatter.toBrDatetime = function (date) {
  const stripped = formatter.onlyDigits(date);
  if (stripped.length === 8) {
    return formatter.dateToBrDate(date);
  } else if (stripped.length === 14) {
    return formatter.datetimeToBrDatetime(date);
  }

  return date;
};

/**
 * Split Datetime into date and time, using brazilian formatting
 *
 * @param {string} isoDatetime
 * @return {{date: string, time: string}}
 * @private
 */
formatter._datetimeToBrDateAndTime = function (isoDatetime) {
  const [date, time] = isoDatetime.split(' ');
  const dmy = date.split('-').reverse().join('/');
  return {
    date: dmy,
    time: time
  };
};

formatter.datetimeToBrDate = function (isoDatetime) {
  /**
   * Convert Datetime to Brazilian Date
   *
   * CAUTION: this method is NOT intended to work with date only
   * (YYYY-MM-DD) inputs, use dateToBrDate() instead.
   *
   * @param {string} isoDatetime Format YYYY-MM-DD HH:MM:SS
   * @return {string} Formatted date: DD/MM/YYYY
   */
  return formatter._datetimeToBrDateAndTime(isoDatetime).date;
};

/**
 * Convert Datetime to Brazilian Time
 *
 * CAUTION: this method is NOT intended to work with date only
 * (YYYY-MM-DD) inputs, use dateToBrDate() instead.
 *
 * @param {string} isoDatetime Format YYYY-MM-DD HH:MM:SS
 * @return {string} Formatted date: HH:MM:SS
 */
formatter.datetimeToBrTime = function (isoDatetime) {
  return formatter._datetimeToBrDateAndTime(isoDatetime).time;
};

/**
 * Convert Datetime to Brazilian date and time
 *
 * @param {string} isoDatetime Format YYYY-MM-DD HH:MM:SS
 * @return {string} Formatted date and time: DD/MM/YYYY HH:MM:SS
 */
formatter.datetimeToBrDatetime = function (isoDatetime) {
  const info = formatter._datetimeToBrDateAndTime(isoDatetime);
  return info.date + ' ' + info.time;
};

/**
 * Convert Date to Brazilian date
 *
 * @param {string} isoDate Format YYYY-MM-DD
 * @return {string} Formatted date: DD/MM/YYYYY
 */
formatter.dateToBrDate = function (isoDate) {
  return isoDate.split('-').reverse().join('/');
};

// Document formatters

formatter.cnpj = function (value) {
  const stripped = formatter.onlyDigits(value);

  if (!stripped) {
    return '';
  }

  return stripped.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
};

formatter.cpf = function (value) {
  const stripped = formatter.onlyDigits(value);

  if (!stripped) {
    return '';
  }

  return stripped.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
};

formatter.cnpjCpf = function (value) {
  const stripped = formatter.onlyDigits(value);

  if (!stripped) {
    return '';
  }

  if (stripped.length === 14) {
    return formatter.cnpj(stripped);
  } else if (stripped.length === 11) {
    return formatter.cpf(stripped);
  }
  return stripped;
};

/**
 * "Namespace" validator
 */
var validator = {};

validator._verifierDigit = function (digits, multipliers = []) {
  const numbers = digits.split('').map(function (digit) {
    return parseInt(digit, 10);
  });

  if (numbers.length !== multipliers.length) {
    throw new Error('Number of digits and multipliers must be the same');
  }

  const partial = numbers.map(function (number, index) {
    return number * multipliers[index];
  });

  const sum = partial.reduce(function (a, b) {
    return a + b;
  }, 0);

  const mod = sum % 11;

  const digit = (mod < 2 ? 0 : 11 - mod);

  return digit;
};

validator.onlyDigits = function (value) {
  return value.replace(/[^\d]+/g, '');
};

validator.isValidCnpj = function (value) {
  const stripped = validator.onlyDigits(value);

  if (!stripped) {
    return false;
  }

  if (stripped.length != 14) {
    return false;
  }

  const denylist = [
    '00000000000000',
    '11111111111111',
    '22222222222222',
    '33333333333333',
    '44444444444444',
    '55555555555555',
    '66666666666666',
    '77777777777777',
    '88888888888888',
    '99999999999999',
  ];

  if (denylist.includes(stripped)) {
    return false;
  }

  let numbers = stripped.substr(0, 12);

  numbers += validator._verifierDigit(numbers, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
  numbers += validator._verifierDigit(numbers, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

  return numbers.substr(-2) === stripped.substr(-2);
};

validator.isValidCpf = function (value) {
  let stripped = validator.onlyDigits(value);

  if (stripped.length !== 11) {
    return false;
  }

  const denylist = [
    '00000000000',
    '11111111111',
    '22222222222',
    '33333333333',
    '44444444444',
    '55555555555',
    '66666666666',
    '77777777777',
    '88888888888',
    '99999999999',
  ];

  if (denylist.includes(stripped)) {
    return false;
  }

  let numbers = stripped.substr(0, 9);

  numbers += validator._verifierDigit(numbers, [10, 9, 8, 7, 6, 5, 4, 3, 2]);
  numbers += validator._verifierDigit(numbers, [11, 10, 9, 8, 7, 6, 5, 4, 3, 2]);

  return numbers.substr(-2) === stripped.substr(-2);
};

validator.isValidPhoneNumber = function (value) {
  const stripped = validator.onlyDigits(value);
  return stripped.length === 10;
};

validator.isValidCelNumber = function (value) {
  const stripped = validator.onlyDigits(value);
  return stripped.length === 11;
};

/**
 * "Namespace" describe (describe constants into readable-human format)
 */
var describe = {};

describe.paymentSource = function (paymentSource, defaultIfNotFound = null) {
  const list = {
    'C': 'Checkout',
    'L': 'Área de Aprendizado',
    'A': 'Automática',
  };
  return list[paymentSource] || defaultIfNotFound;
}

function xUnescape(string) {
  const htmlUnescapes = {
    '&amp;': '&',
    '&lt;': '<',
    '&gt;': '>',
    '&quot;': '"',
    '&#39;': "'"
  };

  const reEscapedHtml = /&(?:amp|lt|gt|quot|#(0+)?39);/g;
  const reHasEscapedHtml = RegExp(reEscapedHtml.source);

  return (string && reHasEscapedHtml.test(string))
    ? string.replace(reEscapedHtml, (entity) => (htmlUnescapes[entity] || "'"))
    : (string || '');
}

function isNumber(n) {
  return /^-?[\d.]+(?:e-?\d+)?$/.test(n);
}

function convertData(data, type) {
  if (!data) {
    return "-"
  } else if (isNumber(data)) {
    switch (type) {
      case 'CNPJ':
        return data.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1 $2 $3/$4-$5");
        break
      case 'cel_phone':
        return data.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2 $3");
        break
      case 'address_zipcode':
        return data.replace(/^(\d{5})(\d{3})/, "$1-$2")
        break
      default: //cpf
        return data.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }
  }
  return data;
}

function convertStatus(status) {
  switch (status) {
    case 'active':
      return 'Ativo';
      break
    case 'canceled':
      return 'Cancelado';
      break
    case 'pending':
      return 'Pendente';
      break
    case 'pending_payment':
      return 'Pagamento pendente';
      break
    case 'failed':
      return 'Falha no pagamento';
      break
    default:
      return 'Status não encontrado';
  }
}
