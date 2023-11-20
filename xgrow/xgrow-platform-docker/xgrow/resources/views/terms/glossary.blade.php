@extends('templates.application.master')

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/glossary.css') }}">
@endpush
@push('custom-scripts')
<script language="JavaScript">
function llI1IIl1l1ll1lI1I11IllI1l(III11llII1II1l1lI1lll1l1l)
{
	var ll1I11Ill1I1I1ll1l1Illll1 = document.getElementById(III11llII1II1l1lI1lll1l1l);
	for(var Il111Ill1lllIl1l11lI1I1Il = 0; Il111Ill1lllIl1l11lI1I1Il < ll1I11Ill1I1I1ll1l1Illll1.childNodes.length; ++Il111Ill1lllIl1l11lI1I1Il)
	{
		if(ll1I11Ill1I1I1ll1l1Illll1.childNodes[Il111Ill1lllIl1l11lI1I1Il].tagName == "TBODY")
		{
			ll1I11Ill1I1I1ll1l1Illll1 = ll1I11Ill1I1I1ll1l1Illll1.childNodes[Il111Ill1lllIl1l11lI1I1Il];
			break;
		}
	}
	return ll1I11Ill1I1I1ll1l1Illll1;
}

function lIIl1IlI1ll11I11l1I1Il1l1(l1I1l11lIl11I11IlllllIIl1)
{
	var Il1l1I11IlI1ll1II111l11I1 = document.createElement("tr");
	if(l1I1l11lIl11I11IlllllIIl1 >= 0)
		Il1l1I11IlI1ll1II111l11I1.height = l1I1l11lIl11I11IlllllIIl1 + "px";
	return Il1l1I11IlI1ll1II111l11I1;
}

function ll1Il1I1lIllIlI11llll1111(lIIllII11I111lIIl1l1ll1I1, I1Illl1l1lI11lI11111lI1ll, IlIII1IIIllI11l111lIl1I1I, I1IlIl1lIIIIIIlII1lI1I1I1)
{
	var llI1I111IlIl1111lIIlIIIl1 = document.createElement("td");
	if(lIIllII11I111lIIl1l1ll1I1 != 1)
		llI1I111IlIl1111lIIlIIIl1.rowSpan = lIIllII11I111lIIl1l1ll1I1;
	if(I1Illl1l1lI11lI11111lI1ll != 1)
		llI1I111IlIl1111lIIlIIIl1.colSpan = I1Illl1l1lI11lI11111lI1ll;
	if(IlIII1IIIllI11l111lIl1I1I != "")
		llI1I111IlIl1111lIIlIIIl1.className = IlIII1IIIllI11l111lIl1I1I;
	if(I1IlIl1lIIIIIIlII1lI1I1I1 >= 0)
		llI1I111IlIl1111lIIlIIIl1.width = I1IlIl1lIIIIIIlII1lI1I1I1 + "px";
	return llI1I111IlIl1111lIIlIIIl1;
}

function l111IlI1I1llll1lIIl1111l1()
{
}

</script>
@endpush

@section('layout-content')

<body onload="l111IlI1I1llll1lIIl1111l1()">
 <section id="wrapper">
    <div id="main">
        <div class="xgrow-img-logo">
            <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="Logo Xgrow">
        </div>
        <div id="page_1">
            <p class="p0 ft0">Glossário</p>
            <p class="p1 ft1">Versão atualizada em 30 de abril de 2021</p>
            <p class="p2 ft2">
            O presente Glossário íntegra os Termos de Uso da XGROW para o Brasil. Ele
            relaciona o significado das expressões iniciadas por letras maiúsculas nos
            Termos ou nas demais Políticas da XGROW.
            </p>
            <p class="p3 ft4">
            Sugerimos que leia as definições a seguir de forma cuidadosa e atenta:
            Colaborador:
            <span class="ft3"
                >Usuário cadastrado por um Produtor para fazer a gestão da conta do
                Produtor, sem que para isso seja necessário o compartilhamento das
                credenciais de acesso do Produtor.</span
            >
            </p>
            <p class="p4 ft1">
            <span class="ft5">Comprador: </span>Usuário que compra qualquer Produto
            cadastrado na Plataforma.
            </p>
            <p class="p5 ft3">
            <span class="ft4">Conteúdo (ou Produto): </span>O conteúdo em diversos
            formatos digitais (vídeo, áudio, texto, software etc.) criado pelo
            Produtor ou licenciado ao Produtor por terceiros e disponibilizado na
            Plataforma.
            </p>
            <p class="p6 ft6">
            <span class="ft4">Contrato: </span>O contrato vinculante firmado entre
            você, como Usuário, e a XGROW, que rege seu acesso e uso à Plataforma e
            seus Serviços.
            </p>
            <p class="p7 ft3">
            <span class="ft4">Dados Cadastrais: </span>Conforme o significado contido
            no Marco Civil da Internet, são dados como a filiação, o endereço
            (inclusive o endereço eletrônico) e a qualificação pessoal, entendida como
            nome, prenome, estado civil e profissão do Usuário.
            </p>
            <p class="p8 ft6">
            <span class="ft4">Display: </span>Área por meio da qual os Produtores
            disponibilizam seus Produtos para serem divulgados na página da XGROW.
            </p>
            <p class="p9 ft8">
            <span class="ft2">XGROW: </span><span class="ft7">A </span>XGROW
            TECNOLOGIA LTDA, pessoa jurídica de direito privado, inscrita no CNPJ/ME
            sob o nº. <nobr>40.190.903/0001-05,</nobr> sediada em Barueri, Estado de
            São Paulo, na Alameda Tocantins, nº 956, Alphaville Industrial, CEP
            <nobr>06455-020.</nobr> <span class="ft2">Checkout: </span
            ><span class="ft7"
                >Sistema de pagamentos da XGROW que processa as instruções de pagamento
                realizadas pelos Compradores e as liquidas para os Produtores e
                Afiliados, conforme regras e procedimentos determinados na Política de
                Pagamento e nos Termos da XGROW.</span
            >
            </p>
        </div>
        <div id="page_2">
            <p class="p10 ft3">
            <span class="ft4">Plataforma XGROW ou Plataforma: </span>Tanto o site
            XGROW e seus subdomínios, como quaisquer outros sites, interfaces ou
            aplicativos nos quais a XGROW disponibilize seus recursos.
            </p>
            <p class="p11 ft1">
            <span class="ft5">Políticas da XGROW: </span>Coletivamente todos os termos
            de política da XGROW.
            </p>
            <p class="p12 ft3">
            <span class="ft4">Produto (ou Conteúdo): </span>O conteúdo em diversos
            formatos digitais (vídeo, áudio, texto, software etc.) criado pelo
            Produtor ou licenciado ao Produtor por terceiros e disponibilizado na
            Plataforma.
            </p>
            <p class="p13 ft3">
            <span class="ft4">Produtor: </span>Usuário aprovado pela XGROW que
            cadastra um Produto em seu nome e é titular exclusivo de todos os direitos
            de propriedade intelectual relativos ao Produto, ou que está regularmente
            autorizado pelos titulares desses direitos a criar, divulgar e
            comercializar o Produto na Plataforma.
            </p>
            <p class="p4 ft1">
            <span class="ft5">Serviços da XGROW ou Serviços: </span>Coletivamente, os
            recursos disponibilizados
            </p>
            <p class="p4 ft1">pela XGROW.</p>
            <p class="p14 ft6">
            <span class="ft4">Serviços de Terceiros: </span>Sites, aplicativos ou
            recursos de terceiros disponibilizados para os Usuários por meio da
            Plataforma.
            </p>
            <p class="p15 ft3">
            <span class="ft4">Tarifa de Intermediação: </span>Tarifa que a XGROW cobra
            do Produtor pelo uso dos recursos do Checkout, cujo valor varia conforme o
            preço de venda do Produto.
            <span class="ft4">Tarifa de Utilização: </span>Tarifa que a XGROW cobra do
            Produtor pelo uso dos recursos do Checkout, cujo valor é fixo, aplicável
            de acordo com a moeda de compra utilizada pelo Comprador na transação.
            </p>
            <p class="p16 ft3">
            <span class="ft4">Terceiros Relacionados: </span>Quaisquer pessoas,
            naturais ou jurídicas, que estejam direta ou indiretamente relacionadas a
            um Produto, como especialistas, atores, personagens ou
            <nobr>garotos-propaganda.</nobr>
            </p>
            <p class="p11 ft1">
            <span class="ft5">Termos: </span>Os Termos de Uso da XGROW para o Brasil.
            </p>
            <p class="p17 ft6">
            <span class="ft4">Usuário: </span>Qualquer pessoa que acesse ou use a
            Plataforma, independentemente de ter feito ou não o seu cadastro como
            Produtor ou Comprador.
            </p>
        </div>
    </div>
</section>
</body>
@endsection
