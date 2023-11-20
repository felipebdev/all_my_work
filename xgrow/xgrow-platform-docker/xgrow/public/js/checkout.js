$(function () {
    $.ajax({
        method: 'GET',
        url: 'https://checkout-homologacao.getnet.com.br/api/proxy-auth-sandbox',
        crossDomain: true // Resolve o problema -> SCRIPT7002: XMLHttpRequest: Network Error 0x80070005, Access is denied.
    })
        .done(function(data) {
            setCheckout("Bearer " + data.access_token);
        });
});

function hashLoad() {
    return Math.random().toString(36).substring(2);
}

function randomValueNumber() {
    return (Math.floor(Math.random() * 999999999999)).toString();
}

function setCheckout(token) {
    var getnetIfrm = document.createElement('script'),
        hash = hashLoad();

    getnetIfrm.setAttribute('data-getnet-sellerid', '6eb2412c-165a-41cd-b1d9-76c575d70a28');
    getnetIfrm.setAttribute('data-getnet-token', token);
    getnetIfrm.setAttribute('data-getnet-method', 'credito');
    getnetIfrm.setAttribute('data-getnet-amount', '10.00');
    getnetIfrm.setAttribute('data-getnet-instructions', 'Teste');
    getnetIfrm.setAttribute('data-getnet-customerid', '4234324');
    getnetIfrm.setAttribute('data-getnet-orderid', '123456789');
    getnetIfrm.setAttribute('data-getnet-button-class', 'pay-button-checkouttest');
    getnetIfrm.setAttribute('data-getnet-installments', '4');
    getnetIfrm.setAttribute('data-getnet-customer-first-name', 'João');
    getnetIfrm.setAttribute('data-getnet-customer-last-name', 'da Silva');
    getnetIfrm.setAttribute('data-getnet-customer-document-type', 'CPF');
    getnetIfrm.setAttribute('data-getnet-customer-document-number', '25848505080');
    getnetIfrm.setAttribute('data-getnet-customer-email', 'teste@teste.com');
    getnetIfrm.setAttribute('data-getnet-customer-phone-number', '1134562356');
    getnetIfrm.setAttribute('data-getnet-customer-address-street', 'Av. Nações Unidas');
    getnetIfrm.setAttribute('data-getnet-customer-address-street-number', '11.541');
    getnetIfrm.setAttribute('data-getnet-customer-address-complementary', '3 andar');
    getnetIfrm.setAttribute('data-getnet-customer-address-neighborhood', 'Brooklin');
    getnetIfrm.setAttribute('data-getnet-customer-address-city', 'São Paulo');
    getnetIfrm.setAttribute('data-getnet-customer-address-state', 'SP');
    getnetIfrm.setAttribute('data-getnet-customer-address-zipcode', '04578000');
    getnetIfrm.setAttribute('data-getnet-customer-country', 'Brasil');
    getnetIfrm.setAttribute('data-getnet-items', '[{"name": "","description": "", "value": 0, "quantity": 0,"sku": ""}]');
    getnetIfrm.setAttribute('data-getnet-our-number', randomValueNumber());
    getnetIfrm.setAttribute('data-getnet-url-callback', '');

    getnetIfrm.src = 'https://checkout-homologacao.getnet.com.br/loader.js?' + hash;
    document.getElementsByTagName('body')[0].appendChild(getnetIfrm);

}
