const axios = require('axios')

const bindTriggerWebhook = async (url, body) =>{
    const { data } = await axios.post(url, body)
    return data
}

const URL = 'https://hooks.zapier.com/hooks/catch/14665056/3o7xj61/'
const BODY = {
    to: 'felipebdev@gmail.com',
    fromName: 'Felipe Bonazzi',
    subject: 'XGROW - Aguardamos seu pagamento',
    body: {
        value: 1000,
        dueDate: 3,
        paymentMethod: 'boleto bancário',
    },
    signature: 'XGROW LEARNING AREA EXPERIENCE'
}

bindTriggerWebhook(URL, BODY).then((result) => console.log('result', result))