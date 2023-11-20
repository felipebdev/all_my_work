@extends('templates.application.master')

@push('after-styles')
<link
  rel="stylesheet"
  href="{{ asset('xgrow-vendor/assets/css/pages/terms.css') }}"
/>
@endpush
@push('custom-scripts')
<script language="JavaScript">
  function llI1IIl1l1ll1lI1I11IllI1l(III11llII1II1l1lI1lll1l1l) {
    var ll1I11Ill1I1I1ll1l1Illll1 = document.getElementById(
      III11llII1II1l1lI1lll1l1l
    );
    for (
      var Il111Ill1lllIl1l11lI1I1Il = 0;
      Il111Ill1lllIl1l11lI1I1Il < ll1I11Ill1I1I1ll1l1Illll1.childNodes.length;
      ++Il111Ill1lllIl1l11lI1I1Il
    ) {
      if (
        ll1I11Ill1I1I1ll1l1Illll1.childNodes[Il111Ill1lllIl1l11lI1I1Il]
          .tagName == "TBODY"
      ) {
        ll1I11Ill1I1I1ll1l1Illll1 =
          ll1I11Ill1I1I1ll1l1Illll1.childNodes[Il111Ill1lllIl1l11lI1I1Il];
        break;
      }
    }
    return ll1I11Ill1I1I1ll1l1Illll1;
  }

  function lIIl1IlI1ll11I11l1I1Il1l1(l1I1l11lIl11I11IlllllIIl1) {
    var Il1l1I11IlI1ll1II111l11I1 = document.createElement("tr");
    if (l1I1l11lIl11I11IlllllIIl1 >= 0)
      Il1l1I11IlI1ll1II111l11I1.height = l1I1l11lIl11I11IlllllIIl1 + "px";
    return Il1l1I11IlI1ll1II111l11I1;
  }

  function ll1Il1I1lIllIlI11llll1111(
    lIIllII11I111lIIl1l1ll1I1,
    I1Illl1l1lI11lI11111lI1ll,
    IlIII1IIIllI11l111lIl1I1I,
    I1IlIl1lIIIIIIlII1lI1I1I1
  ) {
    var llI1I111IlIl1111lIIlIIIl1 = document.createElement("td");
    if (lIIllII11I111lIIl1l1ll1I1 != 1)
      llI1I111IlIl1111lIIlIIIl1.rowSpan = lIIllII11I111lIIl1l1ll1I1;
    if (I1Illl1l1lI11lI11111lI1ll != 1)
      llI1I111IlIl1111lIIlIIIl1.colSpan = I1Illl1l1lI11lI11111lI1ll;
    if (IlIII1IIIllI11l111lIl1I1I != "")
      llI1I111IlIl1111lIIlIIIl1.className = IlIII1IIIllI11l111lIl1I1I;
    if (I1IlIl1lIIIIIIlII1lI1I1I1 >= 0)
      llI1I111IlIl1111lIIlIIIl1.width = I1IlIl1lIIIIIIlII1lI1I1I1 + "px";
    return llI1I111IlIl1111lIIlIIIl1;
  }

  function l111IlI1I1llll1lIIl1111l1() {}
</script>
@endpush

@section('layout-content')

<section id="wrapper">
  <div id="main">
       <div class="xgrow-img-logo">
            <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="Logo Xgrow">
        </div>
    <div id="page_1">
      <p class="p0 ft0">Termos de Uso</p>
      <p class="p1 ft1">Versão atualizada em 29 de Abril de 2021.</p>
      <p class="p2 ft1">Olá, seja bem vindo a XGROW!</p>
      <p class="p3 ft2">
        O presente Termo de Uso regula o acesso ou uso de recursos contidos na
        Plataforma a partir de transações efetuadas na moeda Real (BRL) entre
        Usuários que, no seu cadastro na Plataforma, declarem domicílio no
        Brasil. Para os demais casos, <nobr>aplicam-se</nobr> as condições dos
        Termos de Uso da XGROW para transações internacionais.
      </p>
      <p class="p4 ft1">Agradecemos por utilizar os serviços da XGROW!</p>
      <p class="p5 ft2">
        A XGROW possui um espaço para quem quer criar ou divulgar um produto
        digital de forma simples e prática. Estes Termos regem o uso da
        Plataforma XGROW e de todos os recursos, aplicativos, serviços,
        tecnologias e softwares disponibilizados pela XGROW, exceto quando
        esclarecidos expressamente em outros termos.
      </p>
      <p class="p6 ft2">
        Para facilitar a leitura destes Termos, oferecemos um Glossário, que
        relaciona o significado das expressões iniciadas por letras maiúsculas
        nestes Termos ou nas demais Políticas da XGROW. Em especial, tratamos
        indistintamente como Plataforma XGROW ou simplesmente Plataforma tanto o
        site da XGROW e seus subdomínios, como quaisquer outros sites,
        interfaces ou aplicativos nos quais a XGROW disponibilize seus recursos.
      </p>
      <p class="p7 ft3">
        Os serviços relacionados aos recursos disponibilizados pela XGROW são
        coletivamente referidos como Serviços.
      </p>
      <p class="p8 ft2">
        Ao acessar ou usar a Plataforma, você concorda em se vincular a estes
        Termos e em cumprir as suas regras, ao concordar você declara que leu
        estes Termos de forma atenta e cuidadosa. <nobr>Lembre-se,</nobr> os
        termos possuem informações importantes
      </p>
    </div>
    <div id="page_2">
      <p class="p9 ft2">
        sobre seus direitos e obrigações relativos ao acesso ou uso dos recursos
        ou serviços da XGROW.
      </p>
      <p class="p10 ft4">VISÃO GERAL</p>
      <p class="p11 ft6">
        <span class="ft5">A. </span>Estes Termos regulam o acesso ou uso de
        recursos contidos na Plataforma a partir de transações efetuadas na
        moeda Real (BRL) entre Usuários que, no seu cadastro na Plataforma,
        declarem domicílio no Brasil. Para os demais casos,
        <nobr>aplicam-se</nobr> as condições dos Termos de Uso da XGROW para
        transações internacionais.
      </p>
      <p class="p7 ft8">
        <span class="ft7">B. </span>A empresa com quem você contrata para usar a
        Plataforma pode variar de acordo com o seu país de domicílio. Ao aceitar
        estes Termos e usar a Plataforma nas condições descritas no parágrafo
        anterior, a empresa com quem você está contratando é a XGROW TECNOLOGIA
        LTDA, pessoa jurídica de direito privado, inscrita no CNPJ/ME sob o nº.
        <nobr>40.190.903/0001-05,</nobr> sediada em Barueri, Estado de São
        Paulo, na Alameda Tocantins, nº 956, Alphaville Industrial, CEP
        <nobr>06455-020,</nobr> que atua na condição de intermediadora de
        negócios.
      </p>
      <p class="p12 ft2">
        <span class="ft9">C. </span>Se você ou sua empresa mudar o país de
        domicílio, o seu cadastro deverá ser atualizado, pois o seu acesso e uso
        da Plataforma passarão a ser regidos pelos Termos de Uso da XGROW para
        transações internacionais.
      </p>
      <p class="p13 ft3">
        <span class="ft9">D. </span>Estes Termos constituem um contrato
        vinculante entre você, como Usuário, e a XGROW, e regem seu acesso e
        uso:
      </p>
      <p class="p14 ft1">
        <span class="ft1">(a)</span
        ><span class="ft10">do site XGROW.com e seus subdomínios;</span>
      </p>
      <p class="p15 ft2">
        <span class="ft1">(b)</span
        ><span class="ft11"
          >de quaisquer outros sites, interfaces ou aplicativos nos quais a
          XGROW disponibilize seus recursos, inclusive nossos aplicativos de
          celular, tablet ou de outros dispositivos eletrônicos;</span
        >
      </p>
      <p class="p1 ft1">
        <span class="ft1">(c)</span
        ><span class="ft10"
          >de todos os serviços relacionados aos recursos disponibilizados pela
          XGROW.</span
        >
      </p>
    </div>
    <div id="page_3">
      <p class="p9 ft6">
        <span class="ft5">E. </span>Ao aceitar estes Termos, você declara que
        conhece e concorda com o seu conteúdo e com as demais Políticas da XGROW
        aplicáveis, inclusive com a Política de Privacidade. As Políticas da
        XGROW são parte integrante destes Termos e se incorporam a eles por
        referência, ainda que sejam apresentadas em textos separados. Em caso de
        conflito, as regras destes Termos devem prevalecer sobre as condições
        estabelecidas em outras Políticas da XGROW, exceto se houver previsões
        específicas estabelecendo que determinada Política prevalece. Estes
        Termos e suas atualizações também prevalecem sobre todas as propostas,
        entendimentos ou acordos anteriores, verbais ou escritos, que possam
        existir entre você e a XGROW.
      </p>
      <p class="p16 ft2">
        <span class="ft9">F. </span>Após você aceitar estes Termos, a XGROW
        concede a você automaticamente uma licença de uso não exclusiva da
        Plataforma. Os recursos contidos na Plataforma XGROW são licenciados no
        estado em que se encontram. Eles podem ser modificados, substituídos ou
        removidos da Plataforma pela XGROW a qualquer momento, sem aviso prévio.
      </p>
      <p class="p17 ft2">
        <span class="ft9">G. </span>O respeito às condições destes Termos é
        essencial para o uso legítimo dos recursos disponibilizados na
        Plataforma. Se você usar a Plataforma de modo incompatível com estes
        Termos, a XGROW pode aplicar diferentes medidas, a qualquer tempo, com
        ou sem aviso prévio. Essas medidas podem incluir, sem prejuízo de
        outras:
      </p>
      <p class="p18 ft1">
        <span class="ft1">(a)</span
        ><span class="ft10">suspensão do acesso à sua conta;</span>
      </p>
      <p class="p19 ft1">
        <span class="ft1">(b)</span
        ><span class="ft10">o cancelamento do seu cadastro ou;</span>
      </p>
      <p class="p18 ft1">
        <span class="ft1">(c)</span
        ><span class="ft10"
          >o encerramento da sua licença de uso da Plataforma.</span
        >
      </p>
      <p class="p20 ft3">
        O modo de aplicação dessas e de outras medidas é detalhada ao longo
        destes Termos.
      </p>
      <p class="p21 ft3">
        <span class="ft9">H. </span>Ao aceitar estes Termos, você autoriza que a
        XGROW reporte às autoridades competentes qualquer ato relacionado ao
        acesso ou uso da Plataforma que a
      </p>
    </div>
    <div id="page_4">
      <p class="p22 ft2">
        XGROW considere ter indícios de irregularidades ou ilegalidades. O
        Usuário reportado não pode exigir que a XGROW pague qualquer tipo de
        indenização, por dano patrimonial ou extrapatrimonial, por consequência
        dessa comunicação.
      </p>
      <p class="p23 ft2">
        <span class="ft9">I. </span>Em atenção a diretrizes expedidas por órgãos
        de alcance internacional, como o
        <span class="ft12">Office of Foreign Assets Control </span>dos Estados
        Unidos, a <span class="ft12">United Kingdom Sanctions List </span>ou a
        United Nations Security Council Sanctions List, a XGROW não transaciona
        ou opera com Usuários localizados em determinados países ou regiões, em
        atendimento às normas e boas práticas globais voltadas à prevenção da
        lavagem de dinheiro, atos fraudulentos e financiamento de atividades
        ilícitas.
      </p>
      <p class="p24 ft2">
        <span class="ft9">J. </span>A XGROW se preocupa com sua privacidade e
        tem o compromisso de <nobr>preservá-la.</nobr> O tratamento de dados
        pessoais relacionados ao seu acesso e uso da Plataforma está descrito na
        nossa Política de Privacidade.
      </p>
      <p class="p25 ft2">
        <span class="ft9">K. </span>Todo serviço de processamento de pagamento
        relacionado ao seu uso da Plataforma ou realizado por meio da Plataforma
        é prestado a você conforme estabelecido na Política de Pagamento.
      </p>
      <p class="p26 ft14">
        <span class="ft13">L. </span>Ao usar os Serviços da XGROW com relação a
        qualquer Produto, você se responsabiliza por identificar, compreender e
        cumprir todas as leis, regras e regulamentações aplicáveis. Isso pode
        incluir, entre outras, normas de natureza cível, normas sobre
        propriedade intelectual, privacidade, uso de dados pessoais, sobre o
        anúncio de seu Produto e sobre sua comercialização perante os
        Compradores. As normas aplicáveis também podem exigir providências de
        natureza regulatória, como obtenção de licenças, permissões ou
        autorizações por órgãos públicos, além de inscrições em registros
        profissionais e o respeito a regulamentações emitidas por eles. Regras
        sobre tributos ou contabilidade também podem incidir. Em caso de dúvidas
        sobre como as leis locais se aplicam aos seus negócios na Plataforma ou
        à sua relação com a XGROW, procure a sua assessoria jurídica ou
        contábil.
      </p>
    </div>
    <div id="page_5">
      <p class="p27 ft2">
        <span class="ft9">M. </span>A XGROW se reserva o direito de modificar
        estes Termos a qualquer tempo. Se estes Termos forem alterados, a XGROW
        publicará na Plataforma os Termos revisados, e informará a data da
        última atualização no seu início (“versão atualizada em”).
      </p>
      <p class="p28 ft8">
        <span class="ft7">N. </span>Se a XGROW não exercer imediatamente algum
        direito que lhe seja cabível na forma da lei ou destes Termos, por
        qualquer motivo, ou mesmo se deixar de exercer algum direito em
        determinado caso, isso não significa que a XGROW estará renunciando ao
        exercício desses direitos. A XGROW pode exercer seus direitos a qualquer
        tempo, a seu critério, e se a XGROW renunciar ao exercício de qualquer
        um de seus direitos individualmente isso não significa que estará
        renunciando ao exercício de seus direitos como um todo.
      </p>
      <p class="p29 ft2">
        <span class="ft9">O. </span>As expressões iniciadas por letras
        maiúsculas nestes Termos ou nas Políticas da XGROW têm significado
        próprio, conforme detalhado no Glossário. Para compreender em mais
        detalhes o conteúdo destes Termos e das Políticas da XGROW, recomendamos
        que você consulte o significado dessas expressões no Glossário.
      </p>
      <p class="p30 ft2">
        <span class="ft9">P. </span>O uso de exemplos nestes Termos, bem como o
        uso de expressões como “inclusive”, “incluindo” e outros termos
        semelhantes, não pode ser interpretado de modo a limitar a abrangência
        das regras que os utilizam, tendo sempre função exemplificativa e,
        portanto, não exaustiva.
      </p>
      <p class="p31 ft2">
        <span class="ft9">Q. </span>O idioma oficial destes Termos é o português
        brasileiro. Qualquer versão destes Termos em outro idioma é uma tradução
        fornecida por cortesia. Em caso de conflito, a versão em português deve
        prevalecer para as contratações com a XGROW.
      </p>
    </div>
    <div id="page_6">
      <div id="id6_1">
        <p class="p0 ft4">1. OS SERVIÇOS PRESTADOS PELA XGROW</p>
        <p class="p32 ft17">
          <span class="ft15">1.1.</span
          ><span class="ft16">Escopo dos Serviços da XGROW:</span>
        </p>
        <p class="p33 ft2">
          A Plataforma XGROW disponibiliza um conjunto de recursos online que
          permitem aos seus Usuários consumir, criar, divulgar ou comercializar
          produtos que oferecem conteúdos em diversos formatos digitais (vídeo,
          áudio, texto, software etc.). Quando os Usuários realizam uma
          transação sobre um Produto mediante a Plataforma, eles celebram um
          contrato diretamente um com o outro. A XGROW não é e não se torna
          parte, interveniente ou garantidora de qualquer relação entre os
          Usuários. A XGROW não atua como agente ou distribuidor de qualquer
          Usuário.
        </p>
        <p class="p34 ft17">
          <span class="ft15">1.2.</span
          ><span class="ft16">Relação entre os Usuários com a XGROW:</span>
        </p>
        <p class="p35 ft14">
          Ao utilizar a Plataforma como Produtor e/ou Comprador, você é um
          contratante de serviços de intermediação da XGROW, e não se torna
          empregado, colaborador, representante, agente, sócio, associado ou
          parceiro da XGROW. Você não pode se apresentar como se tivesse
          vínculos desses tipos com a XGROW (por exemplo, você não pode se
          descrever nas redes sociais como “vendedor da XGROW” ou “agente da
          XGROW”). Você deve atuar exclusivamente em seu próprio nome e em seu
          próprio benefício, e não pode atuar em nome ou em benefício da XGROW.
          Você tem liberdade de atuação e discricionariedade para divulgar e
          comercializar os Produtos dos quais você seja Produtor, respeitadas as
          normas legais, as regras destes Termos e a Política de Uso Responsável
          da XGROW, bem como para desempenhar suas atividades comerciais e
          estabelecer suas relações de trabalho como achar adequado.
        </p>
        <p class="p36 ft17">
          <span class="ft15">1.3.</span
          ><span class="ft16">Titularidade dos Produtos:</span>
        </p>
      </div>
      <div id="id6_2">
        <p class="p27 ft2">
          A condição de titular e fornecedor dos recursos disponibilizados pela
          Plataforma não significa que a XGROW cria, elabora, controla, endossa
          ou fornece qualquer Produto. Os Produtores são integralmente
          responsáveis pelo conteúdo e pelas
        </p>
      </div>
    </div>
    <div id="page_7">
      <p class="p9 ft8">
        informações relativas aos seus Produtos, inclusive suas regras de oferta
        e uso. A XGROW não se responsabiliza por quaisquer disputas ou danos, de
        qualquer natureza, que possam surgir do relacionamento entre seus
        Usuários, ou deles com terceiros. Em especial, a XGROW não é responsável
        por qualquer pretensão que você possa ter por confiar em uma informação
        fornecida por um Produtor ou veiculada em um Produto.
      </p>
      <p class="p37 ft19">
        <span class="ft15">1.4.</span
        ><span class="ft18"
          >Inexistência de Promessas de Desempenho, Ganho ou Resultados
          decorrentes do Uso da Plataforma:</span
        >
      </p>
      <p class="p38 ft20">
        A XGROW não promete nem garante que você atingirá qualquer desempenho,
        ganho ou resultado com o uso da Plataforma ou com a aquisição de
        qualquer Produto. Além disso, nenhum Produtor ou pode prometer
        desempenho, ganho ou resultado de qualquer natureza, seja decorrente do
        uso da Plataforma, de sua condição de Usuário, ou do uso ou
        comercialização dos Produtos. Assim, por exemplo, nenhum Produtor pode
        prometer que o uso da Plataforma proporciona renda extra ao Usuário, nem
        anunciar que o uso de um Produto garante benefícios à saúde do
        Comprador.
      </p>
      <p class="p39 ft17">
        <span class="ft15">1.5.</span
        ><span class="ft16"
          >Conformidade dos Produtos com as Políticas da XGROW:</span
        >
      </p>
      <p class="p33 ft8">
        A XGROW disponibiliza um espaço e recursos para quem deseja criar,
        divulgar ou comercializar um Produto de forma simples e prática,
        possibilitando que as pessoas vivam de suas paixões e ensinem o que têm
        de melhor para o mundo inteiro. Por isso, o Produtor aprovado pela
        XGROW, pode usar a Plataforma para criar, divulgar ou comercializar seus
        Produtos. Em linha com a legislação aplicável, a XGROW não se obriga a
        fazer controle prévio de conteúdo (editorial ou de qualquer outra
        natureza) dos Produtos, nem a fazer curadoria de qualquer tipo dos
        Produtos. A XGROW também não se obriga a fazer controle prévio da sua
        base de usuários.
      </p>
    </div>
    <div id="page_8">
      <p class="p0 ft17">
        <span class="ft15">1.6.</span
        ><span class="ft16"
          >Processos de Verificação conduzidos pela XGROW:</span
        >
      </p>
      <p class="p35 ft8">
        A XGROW leva a integridade da comunidade que usa a Plataforma muito a
        sério, e por isso se reserva o direito de realizar verificações antes ou
        depois do cadastro dos Usuários, da oferta de um Produto ou da inserção
        de algum conteúdo na Plataforma, bem como o direito de exigir
        informações adicionais ou até mesmo mudanças a Produtos cadastrados na
        Plataforma. Quaisquer referências a um Usuário ou Produto sendo
        “analisado” (ou linguagem similar) apenas indica que o Usuário ou
        Produto se submete a um processo de verificação cadastral, e nada mais.
        Nenhuma dessas descrições significa endosso, certificação ou garantia da
        XGROW sobre a qualidade do Produto. Essas descrições também não
        representam qualquer atestado de que o Usuário ou o Produto estão
        adequados ao disposto na legislação aplicável, nestes Termos ou nas
        Políticas da XGROW. Lembramos que os Usuários são os responsáveis por
        cumprir todas as regras aplicáveis aos Produtos cadastrados e à sua
        comercialização, e não a XGROW.
      </p>
      <p class="p40 ft17">
        <span class="ft15">1.7.</span><span class="ft21">Marketplace </span>e
        Anúncio através de Serviços de Terceiros:
      </p>
      <p class="p41 ft8">
        Para ampliar a exposição dos Produtos, a XGROW pode
        <nobr>incluí-los</nobr> em seu <span class="ft22">marketplace </span>na
        Plataforma, de acordo com critérios estabelecidos pela própria XGROW. A
        XGROW também pode mencionar ou promover os Produtos em suas
        comunicações, interna ou externa, bem como em outros sites, em
        aplicativos, em publicidade online e <nobr>off-line,</nobr> inclusive
        por meio de Serviços de Terceiros. Se qualquer dessas hipóteses ocorrer,
        a XGROW preserva sua independência em relação aos Produtos e aos
        Produtores, e essa divulgação não significa que a XGROW endossa ou
        concorda com o conteúdo dos Produtos. Além disso, os Produtores que
        tiverem seus Produtos divulgados não fazem jus a qualquer benefício,
        remuneração ou indenização por essa divulgação.
      </p>
    </div>
    <div id="page_9">
      <p class="p0 ft17">
        <span class="ft15">1.8.</span
        ><span class="ft16">Acesso à Internet e à Plataforma:</span>
      </p>
      <p class="p35 ft8">
        Devido à natureza da Internet, a XGROW não pode garantir que a
        Plataforma fique disponível e acessível ininterruptamente. Além disso, a
        XGROW pode restringir a disponibilidade da Plataforma ou de certas áreas
        ou recursos a ela relacionados, se necessário, considerando os limites
        de capacidade, a segurança ou a integridade de seus servidores, bem como
        para realizar medidas de manutenção ou aprimoramento dos seus serviços.
        A XGROW não pode ser responsabilizada pelo Usuário ou por qualquer
        terceiro em função do impedimento ou alteração na forma de acesso à
        Plataforma e aos Serviços. A XGROW pode melhorar e alterar a Plataforma
        a qualquer tempo, seja para modificar, substituir ou remover Serviços
        existentes, ou para adicionar Serviços novos.
      </p>
      <p class="p10 ft17">
        <span class="ft15">1.9.</span
        ><span class="ft16">Prestação de Serviços de Terceiros:</span>
      </p>
      <p class="p35 ft8">
        A Plataforma pode conter links para sites ou recursos de terceiros.
        Esses Serviços de Terceiros não integram o escopo dos Serviços da XGROW
        e não fazem parte da Plataforma. Por isso, eles estão sujeitos a termos
        e condições diferentes, inclusive com relação às práticas de
        privacidade. A XGROW não é responsável pela disponibilidade ou pela
        precisão dos Serviços de Terceiros, tampouco pelo seu conteúdo, pelos
        produtos ou pelos serviços disponíveis nesses Serviços de Terceiros. Os
        links para esses Serviços de Terceiros não representam endosso ou
        concordância da XGROW a seu respeito.
      </p>
      <p class="p37 ft24">
        <span class="ft15">1.10.</span
        ><span class="ft23"
          >Atendimento ao Cliente e Solução de Controvérsias entre os
          Usuários:</span
        >
      </p>
      <p class="p42 ft2">
        A XGROW dispõe de mecanismos de atendimento aos Usuários para facilitar
        a solução de problemas relativos aos serviços da XGROW. A XGROW também
        dispõe de mecanismos destinados a facilitar a comunicação para solução
        de problemas e eventuais conflitos entre você e outro usuário,
        funcionando como canal de diálogo em busca de solução consensual entre
        os Usuários. A XGROW não
      </p>
    </div>
    <div id="page_10">
      <div id="id10_1">
        <p class="p0 ft1">
          disponibiliza serviço de atendimento ao consumidor para resolver
          problemas
        </p>
        <p class="p18 ft1">
          relativos aos Produtos, o que é de responsabilidade integral dos
          Produtores.
        </p>
        <p class="p43 ft4">2. ELEGIBILIDADE, CADASTRO E ACESSO À PLATAFORMA</p>
        <p class="p44 ft17">
          <span class="ft15">2.1.</span
          ><span class="ft16"
            >Elegibilidade dos Produtores ou Colaboradores:</span
          >
        </p>
        <p class="p45 ft20">
          Para registrar uma conta na Plataforma como Produtor e/ou Colaborador,
          é necessário que você tenha pelo menos 18 anos de idade, ou seja
          emancipado, nos moldes da legislação aplicável. Ao aceitar estes
          Termos, você declara ser plenamente capaz para exercer todos os atos
          da vida civil. Se o cadastro na XGROW for efetuado em nome de pessoa
          jurídica, você declara que tem poder e todas as autorizações
          necessárias para <nobr>vinculá-la</nobr> regularmente, inclusive para
          conceder à XGROW todas as permissões e licenças referidas nestes
          Termos ou nas Políticas da XGROW.
        </p>
        <p class="p39 ft17">
          <span class="ft15">2.2.</span
          ><span class="ft16">Elegibilidade dos Compradores:</span>
        </p>
        <p class="p35 ft8">
          A idade mínima para Compradores se cadastrarem na Plataforma é de 13
          anos. Adolescentes entre 13 e 18 anos precisam estar regularmente
          autorizados pelos pais ou responsáveis para tanto. O cadastro na
          Plataforma e a aceitação destes Termos pressupõe que essa autorização
          foi concedida. A XGROW deve cancelar o cadastro de Usuário menor de 18
          anos a pedido dos pais ou responsáveis, mas isso não gera direito a
          qualquer indenização ou reembolso em razão dos Produtos adquiridos
          pelo menor durante a vigência de seu cadastro.
        </p>
        <p class="p46 ft17">
          <span class="ft15">2.3.</span
          ><span class="ft16">Usuários Restritos:</span>
        </p>
      </div>
      <div id="id10_2">
        <p class="p27 ft2">
          A XGROW pode recusar ou cancelar o cadastro de determinado Usuário se
          tomar conhecimento, por si ou por denúncia fundamentada de terceiro,
          da existência de condenação, mediante sentença transitada em julgado,
          por (a) crimes hediondos, ou
        </p>
      </div>
    </div>
    <div id="page_11">
      <p class="p47 ft2">
        equiparados a crimes hediondos; (b) envolvimento em organizações
        criminosas, lavagem de dinheiro, atos terroristas ou tráfico
        internacional de pessoas; ou (c) crimes cometidos por meios eletrônicos
        ou mecanismos cibernéticos.
      </p>
      <p class="p10 ft17">
        <span class="ft15">2.4.</span
        ><span class="ft16">Suspensão ou Encerramento da sua Conta:</span>
      </p>
      <p class="p33 ft2">
        Se a XGROW limitar seu acesso ou uso da Plataforma, suspender sua conta
        ou encerrar este Contrato, você não poderá registrar uma nova conta na
        Plataforma, nem acessar ou usar a Plataforma através da conta de outro
        Usuário.
      </p>
      <p class="p10 ft17">
        <span class="ft15">2.5.</span><span class="ft16">Cadastro:</span>
      </p>
      <p class="p35 ft8">
        Para usar a Plataforma, você se obriga a preencher adequadamente e com
        informações corretas todos os dados solicitados pela XGROW na página de
        cadastramento. A XGROW pode usar quaisquer meios legalmente admitidos
        para lhe identificar, assim como pode requerer de você a qualquer
        momento dados adicionais para complementar aqueles já fornecidos. Se
        você enviar informações inverídicas, incorretas ou incompletas, o seu
        cadastro na Plataforma pode ser suspenso ou cancelado. A XGROW pode
        recusar seu cadastro ou <nobr>cancelá-lo</nobr> se entender que há
        indícios de que você está usando ou tende a utilizar a Plataforma em
        desacordo com estes Termos.
      </p>
      <p class="p46 ft17">
        <span class="ft15">2.6.</span
        ><span class="ft16">Exatidão e Veracidade dos Dados:</span>
      </p>
      <p class="p35 ft2">
        Você é o único responsável por cadastrar dados verdadeiros, exatos e
        atualizados, e responde pelas consequências dos dados ou informações
        inverídicos, incompletos ou incorretos que fornecer no cadastro ou
        depois dele. Confira sempre as informações fornecidas à Plataforma antes
        de concluir o seu cadastro.
      </p>
    </div>
    <div id="page_12">
      <div id="id12_1">
        <p class="p0 ft17">
          <span class="ft15">2.7.</span
          ><span class="ft16">Senha de Acesso e Atividades da Conta:</span>
        </p>
        <p class="p35 ft8">
          Você é o único responsável por seu login, por sua senha, e por
          qualquer atividade conduzida por meio de sua conta na Plataforma. A
          XGROW não é responsável por quaisquer danos, patrimoniais ou
          extrapatrimoniais, resultantes do uso indevido da sua conta por
          terceiros. É imprescindível que você mantenha a confidencialidade e a
          segurança das suas credenciais de acesso à sua conta na XGROW, que são
          pessoais e intransferíveis. Não divulgue ou de qualquer maneira
          compartilhe essas credenciais a terceiros. Nenhum empregado,
          colaborador, representante, agente ou qualquer pessoa vinculada direta
          ou indiretamente à XGROW está autorizada a solicitar que você
          compartilhe sua senha de acesso. Você também não deve compartilhar
          suas credenciais de acesso com Produtores, pois as atividades que
          esses Usuários desempenham não dependem do compartilhamento desse tipo
          de informação. Você deve notificar a XGROW imediatamente se você tomar
          conhecimento ou suspeitar que suas credenciais de acesso foram
          extraviadas, furtadas, apropriadas indevidamente por terceiros,
          tiveram sua confidencialidade comprometida ou foram de qualquer forma
          utilizadas sem sua autorização.
        </p>
        <p class="p48 ft17">
          <span class="ft15">2.8.</span
          ><span class="ft16">Dados Bancários:</span>
        </p>
        <p class="p33 ft8">
          Após realizar a primeira venda, os Produtores devem fornecer dados
          bancários, indicando conta corrente de sua titularidade e no mesmo
          domicílio de seu cadastro para receber o valor das transações
          efetivadas por meio da Plataforma. O cadastro ou o repasse de valores
          devidos aos Produtores para conta bancária de terceiros não é
          permitido em nenhuma circunstância e a nenhum pretexto. Também é
          proibido o cadastro ou repasse de valores para conta bancária de mesma
          titularidade, mas situada fora do domicílio do cadastro do Produtor.
        </p>
        <p class="p49 ft17">
          <span class="ft15">2.9.</span
          ><span class="ft16">Tratamento dos Dados:</span>
        </p>
      </div>
      <div id="id12_2">
        <p class="p0 ft2">
          A Política de Privacidade da XGROW rege o tratamento dos dados
          pessoais que você fornece à XGROW durante o uso e o acesso da
          Plataforma.
        </p>
      </div>
    </div>
    <div id="page_13">
      <p class="p50 ft26">
        <span class="ft4">3.</span
        ><span class="ft25"
          >SEUS PRINCIPAIS COMPROMISSOS COM A XGROW E COM OS DEMAIS
          USUÁRIOS</span
        >
      </p>
      <p class="p51 ft17">
        <span class="ft15">3.1.</span
        ><span class="ft16">Obrigações Gerais dos Usuários da Plataforma:</span>
      </p>
      <p class="p35 ft2">
        Você deve acessar a Plataforma ou usar os Serviços da XGROW apenas para
        fins lícitos. Você deve preservar a reputação da XGROW e evitar qualquer
        prática realizada por você (ou por terceiros relacionados a você) que
        possa, direta ou indiretamente, desabonar a XGROW, seus Serviços, seus
        empregados, colaboradores, representantes, agentes, sócios ou parceiros.
      </p>
      <p class="p52 ft17">
        <span class="ft15">3.2.</span
        ><span class="ft16">Direitos de Propriedade Intelectual da XGROW:</span>
      </p>
      <p class="p35 ft8">
        As informações contidas na Plataforma, bem como as marcas, nomes
        empresariais, nomes de domínio, programas, bancos de dados, redes,
        arquivos, mídias em geral (áudio, texto, vídeo etc.) e qualquer outra
        propriedade intelectual relacionada aos Serviços da XGROW ou contida na
        Plataforma são de titularidade exclusiva da XGROW, ou foram regularmente
        cedidas ou licenciadas à XGROW. Esses elementos são protegidos pelas
        leis e tratados internacionais de propriedade intelectual. É proibido
        copiar, distribuir, usar ou publicar total ou parcialmente qualquer
        material, qualquer item da Plataforma ou qualquer Produto ofertado na
        Plataforma sem prévia autorização por escrito da XGROW.
      </p>
      <p class="p46 ft17">
        <span class="ft15">3.3.</span
        ><span class="ft16"
          >Relacionamento dos Usuários entre si e com Terceiros:</span
        >
      </p>
      <p class="p35 ft8">
        Você se compromete a manter um relacionamento saudável e harmonioso com
        os demais Usuários e com terceiros a respeito do uso da Plataforma. Você
        não pode agredir, caluniar, injuriar ou difamar outros Usuários ou
        terceiros, inclusive os empregados, colaboradores, representantes,
        agentes, sócios ou parceiros da XGROW com os quais você tenha contato.
        Se a XGROW constatar que você agrediu, assediou, discriminou ou de
        qualquer outra forma lesionou direitos dessas pessoas, da XGROW ou de
        outros Usuários, a XGROW pode, a seu critério, tomar
      </p>
    </div>
    <div id="page_14">
      <p class="p47 ft2">
        uma série de medidas previstas nestes Termos, que podem incluir a
        suspensão da sua licença de uso da Plataforma e o encerramento da
        prestação dos Serviços para você, <nobr>excluindo-o</nobr> da
        Plataforma, além de buscar concomitantemente a reparação de quaisquer
        danos, patrimoniais ou extrapatrimoniais, que você causar.
      </p>
      <p class="p40 ft17">
        <span class="ft15">3.4.</span
        ><span class="ft16">Indenizações à XGROW:</span>
      </p>
      <p class="p35 ft8">
        Ao aceitar estes Termos, você se obriga a manter a XGROW livre de
        prejuízo e a reparar quaisquer danos que você causar à XGROW, seja por
        ação ou omissão que seja imputável a você. Essa obrigação de evitar e
        reparar danos inclui danos patrimoniais e extrapatrimoniais, além de
        todas as despesas incorridas pela XGROW em decorrência de condutas
        imputáveis a você, como despesas com ações judiciais e com honorários de
        advogados e peritos, decorrentes dos Produtos comercializados. Você
        também se obriga a manter livre de prejuízo e a reparar os danos que
        você causar aos sócios da XGROW, aos seus controladores, às suas
        sociedades controladas ou coligadas, aos seus administradores,
        diretores, gerentes, empregados, agentes, colaboradores, representantes
        e procuradores.
      </p>
      <p class="p10 ft17">
        <span class="ft15">3.5.</span
        ><span class="ft16"
          >Uso Adequado da Plataforma e dos Serviços da XGROW:</span
        >
      </p>
      <p class="p33 ft2">
        A XGROW oferece um ambiente seguro para que qualquer pessoa possa
        comprar um Produto e ao produtor aprovado que possa criar, divulgar ou
        comercializar um produto, assim podendo iniciar o seu próprio negócio.
        Desse modo, e sem prejuízo de outras regras previstas nestes Termos ou
        nas Políticas da XGROW:
      </p>
      <p class="p53 ft2">
        <span class="ft1">a.</span
        ><span class="ft27"
          >Você não pode violar ou tentar violar quaisquer medidas de segurança
          da Plataforma, nem tirar proveito de eventual inconsistência de
          sistemas da</span
        >
      </p>
      <p class="p54 ft1">XGROW;</p>
      <p class="p55 ft2">
        <span class="ft1">b.</span
        ><span class="ft27"
          >Você não poderá alterar os dados de qualquer Produto depois que a
          XGROW concluir o processo de verificação cadastral, inclusive seu
          título, descritivo, condições de compra e conteúdo;</span
        >
      </p>
    </div>
    <div id="page_15">
      <p class="p56 ft2">
        <span class="ft1">c.</span
        ><span class="ft28"
          >Você não pode induzir terceiros a acreditar, por ação ou omissão sua,
          dentro ou fora da Plataforma, que você é empregado, colaborador,
          prestador de serviço, representante ou que mantém com a XGROW qualquer
          outro vínculo além de Usuário;</span
        >
      </p>
      <p class="p57 ft1">
        <span class="ft1">d.</span
        ><span class="ft29">Você não pode manipular preços de Produtos;</span>
      </p>
      <p class="p57 ft1">
        <span class="ft1">e.</span
        ><span class="ft29"
          >Você não pode interferir nas transações realizadas entre outros
          Usuários</span
        >
      </p>
      <p class="p58 ft2">
        <span class="ft1">f.</span
        ><span class="ft30"
          >Você não pode adotar quaisquer práticas de divulgação, captação de
          informação ou publicidade que sejam indesejadas, massivas,
          repetitivas, fora de contexto, enganosas, inapropriadas, caluniosas ou
          ilegais, para promover qualquer Produto cadastrado na Plataforma, como </span
        ><span class="ft12">spam, flooding, adware, malware </span>e outras
        técnicas nocivas;
      </p>
      <p class="p59 ft2">
        <span class="ft1">g.</span
        ><span class="ft27"
          >Você não pode declarar nem sugerir que a Plataforma é um meio de
          obter rendimentos fácil, rápido, ou que não requer trabalho ou
          esforço;</span
        >
      </p>
      <p class="p60 ft2">
        <span class="ft1">h.</span
        ><span class="ft27"
          >Você não pode utilizar qualquer sistema para o envio de requisições
          de acesso e utilização dos Serviços que supere, em um dado período de
          tempo, o que seria normalmente possível responder, levando ao
          impedimento de acesso, deteriorando ou de qualquer forma alterando a
          experiência de utilização da Plataforma e de seus conteúdos;</span
        >
      </p>
      <p class="p61 ft2">
        <span class="ft1">i.</span
        ><span class="ft31"
          >Você se obriga a não utilizar qualquer sistema automatizado,
          inclusive robôs, </span
        ><span class="ft12">spiders, scripts ou offline readers</span>, que
        acessem os Serviços e venham a <nobr>utilizá-los</nobr> de forma
        contrária ao previsto nestes Termos ou na legislação em vigor.
      </p>
      <p class="p62 ft26">
        <span class="ft4">4.</span
        ><span class="ft32"
          >O USO DOS RECURSOS E OS CONTEÚDOS QUE PODEM SER COMERCIALIZADOS
          MEDIANTE A PLATAFORMA</span
        >
      </p>
      <p class="p63 ft17">
        <span class="ft15">4.1.</span
        ><span class="ft16">Recursos da Plataforma:</span>
      </p>
      <p class="p51 ft1">
        A XGROW oferece uma plataforma totalmente integrada e com soluções para
      </p>
      <p class="p18 ft1">
        escalar qualquer negócio virtual, desde a hospedagem até a entrega
        digital do
      </p>
    </div>
    <div id="page_16">
      <p class="p47 ft8">
        Produto, incluindo o pagamento global e seguro das transações entre os
        Usuários. O objetivo é que o Produtor consiga focar em desenvolver
        produtos de qualidade e criar relacionamentos mais próximos com seus
        Compradores. Para isso, além de recursos para criação, divulgação,
        entrega e pagamento pelo Produto, a XGROW também disponibiliza recursos
        que auxiliam as vendas de forma simplificada, cabendo aos Produtores
        escolherem quais desejam integrar seus Produtos.
      </p>
      <p class="p64 ft17">
        <span class="ft15">4.2.</span
        ><span class="ft16">Conteúdo do Produtor:</span>
      </p>
      <p class="p35 ft8">
        Ao usar qualquer recurso da Plataforma para criar, divulgar ou
        comercializar um Produto, o Produtor declara ser o titular exclusivo de
        todos os direitos de propriedade intelectual relativos ao Produto e ao
        seu conteúdo, ou estar regularmente autorizado pelos titulares desses
        direitos para <nobr>fazê-lo.</nobr> O Produtor também garante que o
        Produto não viola quaisquer direitos de propriedade intelectual de
        terceiros, inclusive de imagem ou direitos autorais, sendo o Produto
        obra original do Produtor, ou tendo o Produtor recebido as licenças ou
        cessões de direitos pertinentes dos titulares de direitos sobre o
        conteúdo integrante do Produto. Além disso, o Produtor deve ter todos os
        direitos para conceder à XGROW as licenças necessárias para a XGROW
        desempenhar suas atividades relativas ao uso da Plataforma com relação
        ao Produto, na forma destes Termos e das Políticas da
      </p>
      <p class="p65 ft1">XGROW.</p>
      <p class="p66 ft24">
        <span class="ft15">4.3.</span
        ><span class="ft33"
          >Exclusividade dos Produtos criados, divulgados ou comercializados
          mediante a Plataforma:</span
        >
      </p>
      <p class="p42 ft8">
        Ao usar qualquer recurso da Plataforma para criar, divulgar ou
        comercializar um Produto, o Produtor declara ter o direito de
        comercializar o Produto em caráter de exclusividade, e assume a
        obrigação de <nobr>comercializá-lo</nobr> pela Plataforma com
        exclusividade. Assim, o Produtor não pode autorizar terceiros a explorar
        qualquer Produto concorrentemente, nem deixar de tomar medidas para
        impedir que terceiros os explorem em concorrência com a XGROW.
      </p>
    </div>
    <div id="page_17">
      <p class="p0 ft17">
        <span class="ft15">4.4.</span
        ><span class="ft16">Uso Responsável da Plataforma:</span>
      </p>
      <p class="p35 ft8">
        O propósito da XGROW é possibilitar que as pessoas empreendam e se
        eduquem com mais facilidade e, assim, se desenvolvam tanto na área
        profissional quanto na pessoal. Para atingir esse objetivo, a XGROW
        adota alguns valores e princípios, que devem ser observados por todos
        seus Usuários: o respeito ao próximo, aos Usuários e às leis locais é um
        deles, assim como a proteção à liberdade de expressão e a neutralidade e
        isonomia das redes de Internet. Por isso, a XGROW não aceita conteúdos
        que violem essas diretrizes. Ao aceitar estes Termos, você se obriga a
        respeitar a Política de Uso Responsável da XGROW, que exemplifica os
        formatos ou conteúdos que a XGROW não deseja na Plataforma. Em termos
        gerais, você não pode usar a Plataforma para criar, divulgar ou
        comercializar Produtos de formato ou conteúdo que:
      </p>
      <p class="p67 ft1">
        <span class="ft1">a.</span
        ><span class="ft29"
          >violem a legislação aplicável ou direitos de terceiros;</span
        >
      </p>
      <p class="p68 ft2">
        <span class="ft1">b.</span
        ><span class="ft27"
          >incitem violência, ódio, discriminação, ou que possam causar prejuízo
          aos Usuários ou à XGROW;</span
        >
      </p>
      <p class="p69 ft1">
        <span class="ft1">c.</span
        ><span class="ft34"
          >prejudiquem a experiência dos Usuários na Plataforma;</span
        >
      </p>
      <p class="p70 ft3">
        <span class="ft1">d.</span
        ><span class="ft35"
          >estejam em desacordo com o modelo de negócios da XGROW ou que sejam
          contrários aos valores da empresa.</span
        >
      </p>
      <p class="p39 ft17">
        <span class="ft15">4.5.</span
        ><span class="ft16">Conteúdo Disponibilizado ao Comprador:</span>
      </p>
      <p class="p35 ft8">
        Ao adquirir um Produto, o Comprador está adquirindo o direito de acesso
        ao seu conteúdo, nos termos estabelecidos na página do produto. A compra
        do Produto não concede ao Comprador direitos de propriedade intelectual
        sobre o Produto. O Comprador não está autorizado a comercializar ou
        ceder a terceiros o Produto, no todo ou em parte, ainda que a título
        gratuito. O Produtor ou a XGROW podem cessar de forma temporária ou
        definitiva o acesso do Comprador ao Produto, inclusive por bloqueio de
        acesso ao Produto ou bloqueio de Usuário, se for verificada a
        inobservância destes Termos ou a suspeita de fraude. Uma fraude pode ser
        caracterizada pelo fornecimento ou compartilhamento de senha, assim como
        por
      </p>
    </div>
    <div id="page_18">
      <p class="p0 ft1">
        outras condutas, como o download, transmissão, retransmissão ou
        armazenamento
      </p>
      <p class="p18 ft1">não autorizado do Produto.</p>
      <p class="p71 ft26">
        <span class="ft4">5.</span
        ><span class="ft36"
          >AS PRINCIPAIS REGRAS APLICÁVEIS AOS PRODUTORES</span
        >
      </p>
      <p class="p72 ft17">
        <span class="ft15">5.1.</span><span class="ft16">Exclusividade:</span>
      </p>
      <p class="p51 ft1">
        Todos os Produtos devem ser comercializados na Plataforma em caráter
        exclusivo.
      </p>
      <p class="p73 ft2">
        <span class="ft1">É</span
        ><span class="ft37"
          >vedado ao Produtor exibir, divulgar ou comercializar qualquer
          Produto, total ou parcialmente, por outras plataformas, meios ou
          suportes, online ou offline, a qualquer título, de maneira onerosa ou
          gratuita.</span
        >
      </p>
      <p class="p10 ft17">
        <span class="ft15">5.2.</span
        ><span class="ft38"
          >Proibição de Uso de Marca ou Associação Inadequada da</span
        >
      </p>
      <p class="p18 ft17">XGROW:</p>
      <p class="p35 ft2">
        Você não pode usar qualquer marca, logo ou nome comercial da XGROW para
        divulgar qualquer Produto, e nem indicar direta ou indiretamente que a
        XGROW se associou, aprovou ou certificou o Produto. Você deve usar e
        acessar a Plataforma apenas nos limites necessários para usar os
        Serviços da XGROW na criação, divulgação ou comercialização do Produto.
      </p>
      <p class="p52 ft17">
        <span class="ft15">5.3.</span
        ><span class="ft16"
          >Principais Licenças que o Produtor concede à XGROW:</span
        >
      </p>
      <p class="p35 ft8">
        Ao cadastrar um Produto na Plataforma, o Produtor automaticamente
        concede à XGROW licença de uso de todos os direitos de propriedade
        intelectual relativos ao Produto, inclusive direitos autorais, de modo
        irrevogável, irretratável, sublicenciável, isenta de royalties ou de
        qualquer outra remuneração. Essa licença é concedida de forma não
        exclusiva, sendo válida em todos os territórios do mundo, para todos os
        fins e efeitos legais, e para todas as modalidades de exploração, com o
        objetivo de a XGROW usar, reproduzir, processar, adaptar, modificar,
        traduzir, incluir legendas,
      </p>
    </div>
    <div id="page_19">
      <p class="p9 ft8">
        publicar, transmitir, exibir ou distribuir o Produto em qualquer mídia
        ou suporte, por qualquer meio de distribuição, conhecido ou a ser
        desenvolvido, dentro e fora da Plataforma. Essa licença também permite
        que a XGROW disponibilize o Produto a terceiros, Usuários ou não, que
        mantenham com a XGROW parceria, contrato ou outro arranjo para fins de
        marketing, que podem pressupor o uso, divulgação, promoção, publicidade,
        comercialização, venda, distribuição, transmissão ou publicação do
        Produto. A integração dos Serviços da XGROW a qualquer Produto não
        significa que o Produto ou os direitos de propriedade intelectual a ele
        associados passam a ser de titularidade da XGROW. O Produtor continua
        sendo titular de todos os direitos e obrigações relacionados ao Produto
        criado, divulgado ou comercializado na Plataforma.
      </p>
      <p class="p40 ft17">
        <span class="ft15">5.4.</span
        ><span class="ft16">Proteção de Direitos relativos ao Produto:</span>
      </p>
      <p class="p35 ft8">
        O objetivo da XGROW é criar um espaço em que o Produtor possa
        compartilhar suas habilidades e conhecimentos com o mundo e gerar renda
        com isso. Por isso, é importante para a XGROW que os direitos dos
        Produtores sobre os Produtos que cadastram na Plataforma sejam
        respeitados. Para tanto, ao cadastrar qualquer Produto na Plataforma, o
        Produtor cede automaticamente à XGROW o direito de tomar quaisquer
        medidas, judiciais ou extrajudiciais, para a XGROW mover as pretensões
        que entender aplicáveis para proteger os direitos de propriedade
        intelectual relativos ao Produto, patrimoniais ou morais, bem como cede
        os direitos de exigir e receber qualquer indenização decorrente da sua
        violação. Isso inclui todos os poderes necessários para que a XGROW tome
        as medidas que entender cabíveis, aos seus custos, como por exemplo
        mover ação judicial, ingressar como parte em ação judicial movida por
        terceiros, realizar acordos, interpor recursos, dar e receber quitação,
        bem como todos os demais poderes habitualmente exigíveis para o
        exercício e a proteção de direitos em juízo.
      </p>
    </div>
    <div id="page_20">
      <p class="p0 ft17">
        <span class="ft15">5.5.</span
        ><span class="ft16">Dados Pessoais dos Usuários:</span>
      </p>
      <p class="p33 ft14">
        O Produtor deve tratar os dados pessoais dos Usuários aos quais tiver
        acesso em decorrência dos Serviços da XGROW em total respeito à Política
        de Privacidade e às leis aplicáveis, incluindo a Lei Geral de Proteção
        de Dados (Lei Federal nº 13.709/18). Se o Produtor solicitar dados
        pessoais aos Usuários fora da Plataforma, o Produtor deve assegurar a
        existência de uma base legal válida para tratar esses dados. Isso inclui
        a obrigação de obter o consentimento expresso desses Usuários, nos
        termos da legislação aplicável. O Produtor deve informar imediatamente à
        XGROW qualquer incidente de segurança ocorrido ou que possa ter ocorrido
        no tratamento de dados pessoais dos Usuários. O Produtor deve excluir
        todos os dados e informações de terceiros que tenha recebido ou
        armazenado em virtude do uso da Plataforma e de seus recursos dentro de
        prazo razoável e adequado, respondendo integralmente pela observância da
        legislação de proteção de dados pessoais aplicável.
      </p>
      <p class="p74 ft17">
        <span class="ft15">5.6.</span
        ><span class="ft16">Transparência e Dever de Informação:</span>
      </p>
      <p class="p35 ft2">
        Além das informações de cadastro, dos dados bancários e dos demais dados
        da conta, o Produtor deve fornecer à XGROW e a terceiros, Usuários ou
        não, todas as informações razoavelmente esperadas relativas aos
        Produtos. Essas informações devem ser prestadas de maneira completa,
        inequívoca, objetiva, transparente e atualizada, dentro e fora da
        Plataforma, notadamente quanto:
      </p>
      <p class="p75 ft3">
        a. ao assunto, composição, descrição, objetivo, características,
        qualidade e quantidade do Produto;
      </p>
      <p class="p76 ft3">
        <span class="ft1">b.</span
        ><span class="ft35"
          >ao preço, formas de pagamento, condições de parcelamento e eventuais
          promoções e descontos;</span
        >
      </p>
      <p class="p77 ft1">
        <span class="ft1">c.</span
        ><span class="ft34"
          >aos prazos e formas de exercício de garantia ou direito de
          arrependimento;</span
        >
      </p>
      <p class="p57 ft1">
        <span class="ft1">d.</span
        ><span class="ft29"
          >às formas, prazos, validade e períodos de entrega e acesso;</span
        >
      </p>
      <p class="p57 ft1">
        <span class="ft1">e.</span
        ><span class="ft29">às atualizações e versões, se aplicável;</span>
      </p>
      <p class="p78 ft1">
        <span class="ft1">f.</span
        ><span class="ft39"
          >aos requisitos obrigatórios e certificação, se aplicável;</span
        >
      </p>
      <p class="p68 ft2">
        <span class="ft1">g.</span
        ><span class="ft27"
          >aos deveres que devem ser cumpridos pelos Usuários e limites aos seus
          direitos;</span
        >
      </p>
    </div>
    <div id="page_21">
      <p class="p79 ft2">
        <span class="ft1">h.</span
        ><span class="ft27"
          >a qualquer outra informação relevante ou decorrente de obrigação
          legal sobre o aproveitamento e a comercialização do Produto.</span
        >
      </p>
      <p class="p80 ft17">
        <span class="ft15">5.6.1.</span
        ><span class="ft16">Isenção de Responsabilidade:</span>
      </p>
      <p class="p35 ft2">
        Além de eventuais advertências exigíveis em razão de leis, regulamentos
        ou convenções que disponham sobre a publicidade e anúncios de produtos,
        a XGROW pode sugerir ou requerer a inserção de advertências para
        determinadas categorias de produtos, ou para alguns produtos
        específicos, no intuito de garantir maior transparência sobre os riscos
        e as expectativas de entrega de Produtos cadastrados na Plataforma.
      </p>
      <p class="p81 ft17">
        <span class="ft15">5.7.</span
        ><span class="ft16">Produtos Duplicados:</span>
      </p>
      <p class="p33 ft2">
        O Produtor não pode cadastrar o mesmo Produto em duplicidade,
        <nobr>comercializando-o</nobr> mediante dois ou mais cadastros
        diferentes, sem que seus respectivos anúncios ofereçam meios de
        pagamentos distintos entre si.
      </p>
      <p class="p46 ft17">
        <span class="ft15">5.8.</span
        ><span class="ft16">Responsabilidade por Terceiros Relacionados:</span>
      </p>
      <p class="p35 ft8">
        Os Produtores são integral e solidariamente responsáveis por ações ou
        omissões praticadas por Terceiros Relacionados. São considerados
        Terceiros Relacionados quaisquer pessoas, naturais ou jurídicas, que
        estejam direta ou indiretamente relacionadas a um Produto, como
        especialistas, atores, personagens ou
        <nobr>garotos-propaganda.</nobr> Ao cadastrar um Produto na Plataforma,
        os Produtores garantem que os Terceiros Relacionados conhecem, concordam
        e se obrigam a cumprir estes Termos e as Políticas da XGROW, pois eles
        se aplicam a essas pessoas. Os Produtores se responsabilizam
        integralmente por obter e manter eficazes, durante todo o período de
        vida do Produto na Plataforma, os direitos para a exploração da imagem e
        dos demais direitos de personalidade dos Terceiros Relacionados, na
        extensão necessária para a comercialização do Produto. Na
      </p>
    </div>
    <div id="page_22">
      <p class="p9 ft8">
        hipótese de um Terceiro Relacionado ser um menor de 18 anos, o Produtor
        deve garantir que tem as respectivas autorizações dos responsáveis e
        órgãos competentes por emitir permissões nesse sentido. A participação
        de menores de 18 anos em textos, imagens ou sons contidos nos Produtos
        deve ser muito restrita, somente quando essencial para a apresentação do
        tema proposto, e o Produtor deve cumprir e se responsabilizar pelo
        cumprimento de todas as normas aplicáveis sobre o uso de trabalho
        infantil, além do estabelecido nestes Termos. A XGROW pode aplicar
        medidas perante qualquer Usuário ou Produto, inclusive
        <nobr>removê-los</nobr> da Plataforma, com base em ações ou omissões de
        Terceiros Relacionados que violem estes Termos.
      </p>
      <p class="p82 ft17">
        <span class="ft15">5.9.</span><span class="ft16">Os Coprodutores:</span>
      </p>
      <p class="p35 ft8">
        Qualquer Produtor pode cadastrar Usuários da Plataforma como
        Coprodutores de determinado Produto, desde que esses sejam plenamente
        capazes, autorizando cada Coprodutor a praticar atos relativos a
        determinado Produto sem que seja necessário o compartilhamento das
        credenciais de acesso do Produtor à Plataforma. Ao efetuar esse
        cadastro, cabe ao Produtor definir o conjunto de atos facultados a cada
        Coprodutor, bem como os percentuais a que cada Coprodutor faz jus pela
        venda de determinado Produto. Os Produtores são integral e
        solidariamente responsáveis perante terceiros e perante a XGROW pelas
        atividades desempenhadas pelos Coprodutores relativas aos seus Produtos.
        Da mesma maneira, os Coprodutores são integral e solidariamente
        responsáveis perante terceiros e perante a XGROW pelas atividades
        desempenhadas pelos Produtores relativas aos Produtos dos quais são
        cadastrados como Coprodutores. Ao aceitar estes Termos, os Produtores
        isentam a XGROW de responsabilidade por quaisquer danos, patrimoniais ou
        extrapatrimoniais, decorrentes de ações ou omissões praticadas pelos
        Coprodutores, e os Coprodutores isentam a XGROW de responsabilidade por
        quaisquer danos, patrimoniais ou extrapatrimoniais, decorrentes de ações
        ou omissões praticadas pelos Produtores.
      </p>
    </div>
    <div id="page_23">
      <p class="p0 ft17">
        <span class="ft15">5.10.</span
        ><span class="ft40">Colaboradores dos Produtores:</span>
      </p>
      <p class="p35 ft14">
        Qualquer Produtor pode convidar Usuários da Plataforma para serem seus
        Colaboradores, desde que esses sejam plenamente capazes, podendo o
        Colaborador fazer a gestão da conta do Produtor, sem que seja necessário
        o compartilhamento das credenciais de acesso do Produtor à Plataforma. O
        Colaborador não faz jus a nenhum pagamento a ser efetuado pela XGROW,
        cabendo aos Produtores e aos Colaboradores estabelecerem entre si as
        condições das suas relações. Os Produtores são integral e solidariamente
        responsáveis perante terceiros e perante a XGROW pelas atividades
        desempenhadas pelos Colaboradores em sua conta ou relativas aos seus
        Produtos. Ao aceitar estes Termos, os Produtores isentam a XGROW de
        responsabilidade por quaisquer danos, patrimoniais ou extrapatrimoniais,
        decorrentes de ações ou omissões praticadas pelos Colaboradores.
      </p>
      <p class="p83 ft17">
        <span class="ft15">5.11.</span
        ><span class="ft16">Obrigações perante os Compradores:</span>
      </p>
      <p class="p33 ft2">
        Lembramos que, quando os Usuários fazem qualquer transação sobre um
        Produto mediante a Plataforma, eles celebram um contrato diretamente um
        com o outro. Assim, entre outros aspectos, o Produtor é responsável:
      </p>
      <p class="p84 ft3">
        a. por garantir que o Comprador se beneficie do Produto tal como lhe foi
        ofertado no momento da compra;
      </p>
      <p class="p85 ft2">
        b. pela entrega do Produto ao Comprador, especialmente se o uso for
        disponibilizado por meio de ferramentas externas à Plataforma;
      </p>
      <p class="p86 ft2">
        <span class="ft1">c.</span
        ><span class="ft28"
          >por solucionar quaisquer problemas que ocorram com o Produto após a
          entrega ao Comprador, em especial os problemas técnicos;</span
        >
      </p>
      <p class="p69 ft1">
        <span class="ft1">d.</span
        ><span class="ft29">por fornecer suporte adequado ao Comprador;</span>
      </p>
      <p class="p87 ft2">
        <span class="ft1">e.</span
        ><span class="ft27"
          >por responder aos eventuais contatos da equipe de suporte da XGROW
          para viabilizar o atendimento adequado aos Usuários. Em especial, o
          Produtor deve respeitar os prazos de exercício de direito de
          arrependimento e de garantia exigidos por lei, se aplicáveis, bem como
          qualquer prazo adicional que oferecer aos Compradores, </span
        ><nobr>obrigando-se</nobr> a aceitar eventual solicitação de
        cancelamento da venda e reembolso por meio da Plataforma ou de seus
      </p>
    </div>
    <div id="page_24">
      <p class="p88 ft2">
        canais de atendimento e de suporte. Para tanto, o Produtor desde já
        autoriza a XGROW a realizar a dedução dos valores de reembolso devidos
        pelo Produtor a qualquer Comprador. A XGROW pode deduzir esses valores
        dos créditos existentes ou futuros na conta do Produtor.
      </p>
      <p class="p43 ft4">6. A EXPERIÊNCIA DE COMPRA DO COMPRADOR</p>
      <p class="p44 ft17">
        <span class="ft15">6.1.</span
        ><span class="ft16"
          >Responsabilidade pela Experiência do Comprador:</span
        >
      </p>
      <p class="p35 ft14">
        A XGROW preza pela autonomia das pessoas, acreditando que seus Usuários
        devem ter o poder de decisão sobre quais conteúdos desejam adquirir. Por
        isso, a XGROW não se obriga a fazer controle prévio (editorial ou de
        qualquer outra natureza) ou curadoria dos produtos. A XGROW pode fazer
        verificações sobre o cadastro dos Produtos, mas não é capaz de
        determinar se os Produtos observam todas as determinações legais, se são
        adequados para os fins a que se propõem, nem é capaz de determinar a
        veracidade, a exatidão e a completude das informações prestadas pelos
        Produtores. Ao adquirir algum Produto, o Comprador deve verificar a
        adequação do Produto e do Produtor aos fins buscados por ocasião da
        compra, bem como a veracidade das demais informações e características
        prestadas em relação ao Produto. Além disso, o Comprador assume
        voluntariamente todo e qualquer risco associado à aquisição do Produto.
        Isso inclui o risco de ser exposto a assunto que considere ofensivo,
        indecente ou censurável, ou de que o conhecimento veiculado possa
        resultar em efeitos danosos ou adversos, físicos ou mentais, pelos quais
        o Comprador assume total responsabilidade.
      </p>
      <p class="p89 ft17">
        <span class="ft15">6.2.</span
        ><span class="ft16">Diligência sobre as Informações Prestadas:</span>
      </p>
      <p class="p33 ft2">
        O Comprador é integralmente responsável por todas as informações que
        prestar e pelas declarações que fizer aos Produtores ou a qualquer outra
        pessoa por ocasião do uso da Plataforma, em razão da compra ou em
        decorrência do uso de qualquer Produto.
      </p>
    </div>
    <div id="page_25">
      <p class="p0 ft4">7. A REMUNERAÇÃO DA XGROW</p>
      <p class="p44 ft17">
        <span class="ft15">7.1.</span
        ><span class="ft16">Tarifas do Produtor:</span>
      </p>
      <p class="p33 ft8">
        O uso pelo Produtor dos recursos que a Plataforma disponibiliza está
        sujeito ao pagamento das Tarifas de Licença e de Intermediação, que são
        cobradas por ocasião da venda de cada Produto, no momento em que a
        transação é aprovada, e deduzidas do valor final cobrado pelo Produtor
        de cada Comprador. Essas tarifas, seus respectivos meios de pagamento,
        faturamento e tipo de pagamento estão estabelecidos na Política de
        Pagamentos, e podem ser alteradas a qualquer tempo, a critério da XGROW,
        mediante notificação prévia.
      </p>
      <p class="p90 ft17">
        <span class="ft15">7.2.</span
        ><span class="ft16">Tarifas do Comprador:</span>
      </p>
      <p class="p35 ft2">
        Os Compradores podem acessar e usar os serviços da XGROW gratuitamente.
        No entanto, a XGROW pode cobrar valores decorrentes de operações
        financeiras realizadas por esses Usuários, como o parcelamento ou uso de
        diferentes meios de pagamento por Comprador, conforme estabelecido na
        <a href="https://www.hotmart.com/legal/pt-BR/payments-policy/br"
          >Política de Pagamentos.</a
        >
      </p>
      <p class="p91 ft4">8. LIMITES DE RESPONSABILIDADE DA XGROW</p>
      <p class="p44 ft17">
        <span class="ft15">8.1.</span
        ><span class="ft16">Limites da Responsabilidade da XGROW:</span>
      </p>
      <p class="p35 ft8">
        Lembramos que, ao usar a Plataforma como Produtor ou Comprador, você é
        um contratante dos serviços de intermediação prestados pela XGROW. A
        XGROW não cria, elabora, controla, endossa ou fornece qualquer Produto
        para você. Os Produtores são os responsáveis pelo conteúdo e pelas
        informações relativas aos seus Produtos. Ao aceitar estes Termos e usar
        a Plataforma, você o faz voluntariamente, por sua conta e risco. Por
        isso, excetuadas as hipóteses de responsabilidade determinadas por lei,
        a XGROW não responde por danos de qualquer natureza decorrentes do
        acesso ou uso da Plataforma, sejam patrimoniais ou extrapatrimoniais,
        diretos ou indiretos, prejuízos efetivos ou lucros cessantes. A XGROW
        não responde, a título exemplificativo e não exaustivo, por:
      </p>
      <p class="p67 ft1">
        a. danos decorrentes da inadequação dos Produtos aos fins a que se
        destinam;
      </p>
    </div>
    <div id="page_26">
      <p class="p92 ft2">
        <span class="ft1">b.</span
        ><span class="ft27"
          >danos decorrentes de defeitos ou vícios de qualidade ou quantidade
          dos Produtos;</span
        >
      </p>
      <p class="p93 ft2">
        <span class="ft1">c.</span
        ><span class="ft28"
          >danos decorrentes de riscos relativos ao uso de Produto, inclusive
          potencial nocividade ou periculosidade;</span
        >
      </p>
      <p class="p94 ft1">
        <span class="ft1">d.</span
        ><span class="ft29"
          >reclamações decorrentes de insatisfações pela baixa qualidade dos
          Produtos;</span
        >
      </p>
      <p class="p95 ft2">
        <span class="ft1">e.</span
        ><span class="ft27"
          >danos decorrentes de erro, incompletude, inverdade ou inadequação de
          informações prestadas na divulgação de Produto mediante recursos
          disponibilizados pela Plataforma;</span
        >
      </p>
      <p class="p96 ft3">
        <span class="ft1">f.</span
        ><span class="ft41"
          >danos decorrentes do descumprimento, pelos Usuários, dos seus deveres
          legais ou contratuais perante outros Usuários;</span
        >
      </p>
      <p class="p97 ft43">
        <span class="ft1">g.</span
        ><span class="ft42"
          >danos decorrentes de erro, incompletude, inverdade ou inadequação de
          informações prestadas durante o processamento de transações de
          pagamento;</span
        >
      </p>
      <p class="p98 ft1">
        <span class="ft1">h.</span
        ><span class="ft29">danos decorrentes dos Serviços de Terceiros;</span>
      </p>
      <p class="p99 ft2">
        <span class="ft1">i.</span
        ><span class="ft31"
          >danos resultantes dos serviços de terceiros no processamento de
          conversão e saque de moeda;</span
        >
      </p>
      <p class="p100 ft2">
        <span class="ft1">j.</span
        ><span class="ft31"
          >danos decorrentes do uso indevido da Plataforma por terceiros, sejam
          Produtores ou Compradores, a que título for, especialmente em caso de
          divulgação em outras plataformas de Produtos previamente
          adquiridos;</span
        >
      </p>
      <p class="p101 ft2">
        <span class="ft1">k.</span
        ><span class="ft28"
          >danos decorrentes dos materiais, informações ou conteúdos publicados
          em sites de terceiros, tampouco pela disponibilidade destes sites,
          competindo ao Usuário usar seu próprio discernimento, precaução e
          senso comum ao acessar sites de terceiros, devendo conferir as
          respectivas políticas de privacidade e os termos de uso
          aplicáveis;</span
        >
      </p>
      <p class="p102 ft3">
        <span class="ft1">l.</span
        ><span class="ft44"
          >danos decorrentes de dificuldades técnicas ou falhas nos sistemas,
          servidores ou na internet;</span
        >
      </p>
      <p class="p100 ft2">
        <span class="ft1">m.</span
        ><span class="ft45"
          >danos decorrentes de ataques de vírus ao equipamento do Usuário
          ocorridos durante o uso da Plataforma, ou como consequência da
          transferência de dados, arquivos, imagens, textos ou áudio.</span
        >
      </p>
    </div>
    <div id="page_27">
      <p class="p0 ft17">
        <span class="ft15">8.2.</span
        ><span class="ft16"
          >Exclusão da XGROW do Polo Passivo de Eventual Disputa:</span
        >
      </p>
      <p class="p45 ft20">
        Ao aceitar estes Termos, você desde já concorda com a exclusão da XGROW
        do polo passivo de qualquer tipo de reclamação ou processo, judicial ou
        extrajudicial, que você iniciar contra outros Usuários ou terceiros a
        respeito do uso da Plataforma. Essa exclusão também se aplica em favor
        dos sócios da XGROW, dos seus controladores, das suas sociedades
        controladas ou coligadas, dos seus administradores, diretores, gerentes,
        empregados, agentes, colaboradores, representantes e procuradores.
      </p>
      <p class="p89 ft4">9. INFRAÇÕES AOS TERMOS E ÀS POLÍTICAS DA XGROW</p>
      <p class="p32 ft17">
        <span class="ft15">9.1.</span
        ><span class="ft16">Infrações aos Termos:</span>
      </p>
      <p class="p35 ft2">
        Se você deixar de observar qualquer condição destes Termos ou de
        qualquer Política da XGROW, total ou parcialmente, a XGROW pode aplicar
        diferentes medidas, a critério da XGROW, de forma isolada ou cumulativa,
        com ou sem prévio aviso, a qualquer tempo e pelo período que lhe
        aprouver. Entre essas medidas, sem prejuízo de outras não previstas
        nestes Termos, a XGROW pode aplicar:
      </p>
      <p class="p78 ft1">
        <span class="ft1">a.</span><span class="ft29">advertência;</span>
      </p>
      <p class="p78 ft1">
        <span class="ft1">b.</span
        ><span class="ft29"
          >limitação, remoção ou encerramento de acesso ao Produto;</span
        >
      </p>
      <p class="p57 ft1">
        <span class="ft1">c.</span
        ><span class="ft34"
          >retirada ou diminuição de visibilidade do Produto da XGROW;</span
        >
      </p>
      <p class="p58 ft2">
        <span class="ft1">d.</span
        ><span class="ft27"
          >limitação de acesso, suspensão ou encerramento de qualquer benefício
          ou categoria especial oferecida pela Plataforma relacionada ao
          Produtor, à sua conta, ou a determinado Produto;</span
        >
      </p>
      <p class="p103 ft1">
        <span class="ft1">e.</span
        ><span class="ft29">rebaixamento de categoria de Usuário;</span>
      </p>
      <p class="p104 ft2">
        <span class="ft1">f.</span
        ><span class="ft30"
          >limitação, suspensão ou encerramento de acesso a alguma
          funcionalidade especial da Plataforma;</span
        >
      </p>
      <p class="p69 ft1">
        <span class="ft1">g.</span
        ><span class="ft29">remoção da página do Produto;</span>
      </p>
      <p class="p70 ft3">
        <span class="ft1">h.</span
        ><span class="ft35"
          >limitação de acesso, suspensão, bloqueio ou remoção de Produto, conta
          ou Usuário;</span
        >
      </p>
      <p class="p77 ft1">
        <span class="ft1">i.</span
        ><span class="ft46">encerramento deste Contrato.</span>
      </p>
    </div>
    <div id="page_28">
      <div id="id28_1">
        <p class="p0 ft17">
          <span class="ft15">9.2.</span
          ><span class="ft16">Medidas Preventivas:</span>
        </p>
        <p class="p35 ft8">
          A XGROW pode, a seu critério, adotar medidas preventivas para
          averiguar possíveis infrações aos Termos e às Políticas da XGROW, ou
          se entender ser necessário para proteger interesses próprios ou de
          terceiros. Essas medidas podem durar o tempo necessário para que
          eventual correção ou verificação seja feita, ou até a XGROW decidir
          pelo encerramento ou não dos Serviços da XGROW, ou pela aplicação de
          outras medidas. As medidas preventivas podem resultar na suspensão
          temporária dos Serviços da XGROW a determinado Produto ou Usuário.
          Nessa hipótese, durante todo o período de suspensão, a XGROW pode
          alterar ou retirar o acesso total ou parcial de algumas
          funcionalidades da conta do Usuário, dentre as quais, a título
          exemplificativo:
        </p>
        <p class="p105 ft2">
          <span class="ft1">a.</span
          ><span class="ft27"
            >a XGROW pode suspender a remessa de valores transacionados oriundos
            de vendas realizadas mediante a Plataforma, ou impedir o Usuário de
            realizar qualquer resgate de valores acumulados na sua conta,
            conforme a Política de Pagamento da XGROW;</span
          >
        </p>
        <p class="p106 ft2">
          <span class="ft1">b.</span
          ><span class="ft27"
            >a XGROW pode impedir o Usuário de alterar dados cadastrais, dados
            bancários e os Produtos cadastrados. A XGROW também pode cancelar
            links de vendas e ofertas ativas em caso de suspensão dos serviços
            da XGROW. Nessas hipóteses, o Usuário não tem direito a indenização
            de quaisquer danos, patrimoniais ou extrapatrimoniais, decorrentes
            das medidas preventivas aplicadas pela XGROW.</span
          >
        </p>
        <p class="p91 ft17">
          <span class="ft15">9.3.</span
          ><span class="ft16">Outras Medidas Judiciais ou Extrajudiciais:</span>
        </p>
        <p class="p33 ft2">
          A XGROW pode adotar quaisquer outras medidas, judiciais ou
          extrajudiciais, que entender cabíveis, em nome próprio ou mediante
          terceiros legitimados, sem prejuízo da adoção isolada ou cumulativa
          das outras mencionadas nos itens anteriores.
        </p>
      </div>
      <div id="id28_2">
        <p class="p0 ft26">
          <span class="ft4">10.</span
          ><span class="ft47"
            >SUSPENSÃO OU ENCERRAMENTO DOS SERVIÇOS DA XGROW</span
          >
        </p>
      </div>
    </div>
    <div id="page_29">
      <p class="p0 ft17">
        <span class="ft15">19.1.</span
        ><span class="ft40"
          >Suspensão ou Encerramento dos Serviços da XGROW:</span
        >
      </p>
      <p class="p35 ft8">
        Os valores e princípios da XGROW são amparados no respeito ao próximo e
        à liberdade, e estamos sempre empenhados em facilitar o
        empreendedorismo, o conhecimento e a educação para que mais pessoas
        atinjam desenvolvimento pessoal e profissional. Por isso, a XGROW se
        reserva o direito de tomar diversas ações destinadas a proteger a
        Plataforma ou nossa comunidade, mediante requerimento justificado de
        terceiro ou por iniciativa própria. Essas medidas podem incluir
        suspender ou interromper a prestação de Serviços, encerrar a licença de
        uso concedida a Usuário, tornar Produto indisponível,
        <nobr>removê-lo</nobr> da Plataforma, bem como quaisquer outras medidas
        previstas ou não na Seção 10 destes Termos. A XGROW pode decidir tomar
        essas e outras medidas a qualquer tempo, com ou sem aviso prévio, de
        modo isolado ou cumulativo. Qualquer decisão da XGROW a esse respeito
        ocorrerá a seu exclusivo critério, por motivos que podem incluir:
      </p>
      <p class="p67 ft1">
        <span class="ft1">a.</span
        ><span class="ft29"
          >a inobservância destes Termos ou das Políticas da XGROW;</span
        >
      </p>
      <p class="p78 ft1">
        <span class="ft1">b.</span
        ><span class="ft29"
          >a prática de ato que contrarie de alguma maneira os valores ou
          princípios da</span
        >
      </p>
      <p class="p107 ft1">XGROW;</p>
      <p class="p87 ft14">
        <span class="ft1">c.</span
        ><span class="ft48"
          >qualquer conduta, praticada dentro ou fora da Plataforma, que possa
          impactar negativamente a XGROW, ou que possa levar a empresa ou seus
          negócios a descrédito público, desonra, escândalo ou ridículo. O fato
          de qualquer Usuário ter sido habilitado anteriormente durante o
          processo de verificação cadastral não impede a XGROW de tomar as
          medidas que julgar adequadas, quando entender cabível. Ao aceitar
          estes Termos, você reconhece que não tem direito a indenização de
          qualquer dano, patrimonial ou extrapatrimonial, se a XGROW adotar
          qualquer medida baseada em lei, nestes Termos ou nas Políticas da
          XGROW, inclusive se você tiver seu cadastro ou qualquer Produto
          recusado, se tiver o acesso à sua conta suspenso ou interrompido, se a
          XGROW encerrar a licença conferida a você para uso da Plataforma, ou
          se você tiver um Produto removido da Plataforma ou tornado
          indisponível.</span
        >
      </p>
    </div>
    <div id="page_30">
      <p class="p0 ft17">
        <span class="ft15">10.2.</span
        ><span class="ft40">Encerramento Imotivado:</span>
      </p>
      <p class="p33 ft2">
        Ao aceitar este Contrato, a XGROW lhe concede automaticamente uma
        licença de uso não exclusiva da Plataforma, por prazo indeterminado.
        Desse modo, a XGROW pode encerrar este Contrato e a respectiva licença
        de uso a qualquer momento, a critério da XGROW.
      </p>
      <p class="p89 ft17">
        <span class="ft15">10.3.</span
        ><span class="ft40">Encerramento Motivado:</span>
      </p>
      <p class="p51 ft1">
        A XGROW pode encerrar este Contrato imediatamente, sem aviso prévio,
        caso:
      </p>
      <p class="p57 ft1">
        <span class="ft1">a.</span
        ><span class="ft29"
          >você viole estes Termos ou quaisquer condições das Políticas da
          XGROW;</span
        >
      </p>
      <p class="p78 ft1">
        <span class="ft1">b.</span
        ><span class="ft29">para prevenir fraudes;</span>
      </p>
      <p class="p57 ft1">
        <span class="ft1">c.</span
        ><span class="ft34"
          >para impedir a ocorrência ou o agravamento de danos a si ou a
          terceiros;</span
        >
      </p>
      <p class="p104 ft2">
        <span class="ft1">d.</span
        ><span class="ft27"
          >em razão de estrito cumprimento de dever legal, por decisão judicial
          ou determinação de órgão governamental ou agência regulatória.</span
        >
      </p>
      <p class="p108 ft50">
        <span class="ft1">10.3.1.</span
        ><span class="ft49"
          >Encerramento Imediato por Condutas Inadequadas:</span
        >
      </p>
      <p class="p109 ft2">
        A XGROW leva a integridade da comunidade que usa a Plataforma muito a
        sério. Assim, a XGROW pode aplicar quaisquer das medidas previstas na
        Seção 10 destes Termos, ou, se entender necessário, pode inclusive
        encerrar este Contrato imediatamente, sem aviso prévio se:
      </p>
      <p class="p110 ft1">
        <span class="ft1">a.</span
        ><span class="ft51"
          >você receber repetidamente avaliações ou comentários negativos;</span
        >
      </p>
      <p class="p111 ft2">
        <span class="ft1">b.</span
        ><span class="ft52"
          >a XGROW tomar ciência de reclamações sobre conduta inapropriada sua
          praticada de maneira reiterada ou contumaz;</span
        >
      </p>
      <p class="p112 ft2">
        <span class="ft1">c.</span
        ><span class="ft53"
          >o seu Produto ou se você, como Produtor, receber um volume de
          reclamações nos canais de atendimento, de solicitações de cancelamento
          ou de estorno que a XGROW entender incompatível com a qualidade que se
          espera de Usuários ou Produtos cadastrados na Plataforma;</span
        >
      </p>
      <p class="p113 ft1">
        <span class="ft1">d.</span
        ><span class="ft51"
          >você, como Produtor, deixar de prestar suporte aos Compradores;</span
        >
      </p>
      <p class="p114 ft2">
        <span class="ft1">e.</span
        ><span class="ft52"
          >você se recusar a corrigir, incluir ou excluir informação relevante
          sobre o Produto ou sobre você mesmo na Plataforma;</span
        >
      </p>
    </div>
    <div id="page_31">
      <p class="p115 ft2">
        <span class="ft1">f.</span
        ><span class="ft54"
          >os índices de chargebacks, reclamações e contestações referentes às
          transações de pagamento da sua conta forem elevados;</span
        >
      </p>
      <p class="p112 ft2">
        <span class="ft1">g.</span
        ><span class="ft52"
          >você fornecer informações incompletas, desatualizadas, incorretas,
          fraudulentas ou imprecisas em seu processo de cadastro, sobre o
          Produto ou para receber atendimento pela XGROW;</span
        >
      </p>
      <p class="p116 ft2">
        <span class="ft1">h.</span
        ><span class="ft52"
          >se você fizer algo nocivo e a XGROW entender que o encerramento do
          Contrato seja medida necessária, em bases razoáveis, para proteger a
          saúde, a segurança, a reputação, a honra e os direitos da XGROW, de
          seus empregados, colaboradores, investidores, sócios, administradores,
          dos seus Usuários ou de terceiros afetados por ação ou omissão que
          você praticar.</span
        >
      </p>
      <p class="p117 ft17">
        <span class="ft15">10.4.</span
        ><span class="ft40">Efeitos do Encerramento:</span>
      </p>
      <p class="p33 ft2">
        Se a XGROW encerrar este Contrato, interrompendo a prestação dos
        Serviços da XGROW, em qualquer modalidade e por qualquer razão, você
        deve observar as condições a seguir:
      </p>
      <p class="p32 ft50">
        <span class="ft1">10.4.1.</span
        ><span class="ft49">Acesso à Conta XGROW:</span>
      </p>
      <p class="p118 ft2">
        Se este Contrato for encerrado, você não terá direito a uma restauração
        da sua conta na Plataforma, nem à manutenção dos seus Produtos na
        Plataforma. Você também não poderá registrar uma nova conta na
        Plataforma, nem acessar ou usar a Plataforma através da conta de outro
        Usuário.
      </p>
      <p class="p90 ft50">
        <span class="ft1">10.4.2.</span
        ><span class="ft49">Fornecimento de Dados:</span>
      </p>
      <p class="p109 ft2">
        A XGROW não é obrigada a fornecer aos Usuários dados adicionais aos
        quais eles já tenham acesso em decorrência do uso regular da Plataforma,
        em observância da Política de Privacidade. O Produtor ou Colaborador
        deve observar todas as regras da Política de Privacidade a respeito da
        exclusão de dados pessoais dos Usuários sob sua responsabilidade em caso
        de encerramento deste Contrato.
      </p>
      <p class="p90 ft50">
        <span class="ft1">10.4.3.</span
        ><span class="ft49">Produtos de Assinatura:</span>
      </p>
      <p class="p119 ft2">
        A XGROW pode, a seu critério e pelo tempo que lhe aprouver, continuar
        processando os pagamentos para os produtos de assinatura, que são os
        produtos
      </p>
    </div>
    <div id="page_32">
      <p class="p9 ft8">
        em que o pagamento é feito de forma periódica e sucessiva por meio de
        débitos na fatura de cartão de crédito do Comprador. Em caso de migração
        dos Compradores de um Produto para outra modalidade de pagamento, ou
        para outra plataforma, o Produtor deve providenciar essa migração,
        dentro do prazo que lhe for conferido, com os dados dos Usuários aos
        quais tiver acesso por ocasião do uso da Plataforma. A XGROW não
        fornecerá qualquer dado adicional àqueles já disponibilizados ao
        produtor para que a migração seja feita.
      </p>
      <p class="p32 ft50">
        <span class="ft1">10.4.4.</span
        ><span class="ft49">Resgate de Saldo da Conta XGROW:</span>
      </p>
      <p class="p109 ft2">
        Havendo saldo na conta do Usuário no momento do encerramento do
        Contrato, os valores devem ser transferidos para a conta bancária
        indicada no cadastro em até 30 dias, após a solicitação de
        transferência.
      </p>
      <p class="p120 ft50">
        <span class="ft1">10.4.5.</span
        ><span class="ft49">Reembolso a Compradores Afetados:</span>
      </p>
      <p class="p118 ft2">
        Se o encerramento do Contrato de um Produtor afetar o acesso de
        Compradores a um determinado Produto, a XGROW somente se obriga a
        reembolsar os Compradores até o limite do saldo existente na conta de
        pagamento do Produtor.
      </p>
      <p class="p98 ft50">
        <span class="ft1">10.4.6.</span
        ><span class="ft49">Retenção de Valores pela XGROW:</span>
      </p>
      <p class="p118 ft2">
        Se a XGROW encerrar o Contrato, por qualquer motivo, com um Produtor, a
        XGROW pode reter um montante do saldo do Produtor na Plataforma por
        período de até 180 dias contados a partir da última transação realizada.
        Essa retenção deve ter como finalidade assegurar os direitos de regresso
        e compensação da XGROW por quaisquer despesas que ela suportar e que
        sejam de responsabilidade do Produtor. Após esse período, a XGROW deve
        liberar o saldo na conta bancária de cadastro do Produtor.
      </p>
      <p class="p121 ft50">
        <span class="ft1">10.4.7.</span
        ><span class="ft49">Suporte aos Usuários após o Encerramento:</span>
      </p>
      <p class="p109 ft2">
        O suporte ao Produtor e o respectivo acesso ao gerente de conta, caso
        existente, serão automaticamente encerrados com o encerramento do
        Contrato. Havendo necessidade, os Usuários interessados devem endereçar
        quaisquer dúvidas ou
      </p>
    </div>
    <div id="page_33">
      <p class="p122 ft2">
        problemas remanescentes ou decorrentes do encerramento ao serviço de
        suporte ao cliente da XGROW.
      </p>
      <p class="p123 ft50">
        <span class="ft1">10.4.8.</span
        ><span class="ft49">Acordos Específicos com a XGROW:</span>
      </p>
      <p class="p119 ft3">
        Se você tiver este Contrato encerrado e tiver algum outro acordo
        específico pactuado com a XGROW, este outro acordo também será encerrado
        imediatamente.
      </p>
      <p class="p124 ft17">
        <span class="ft15">10.5.</span
        ><span class="ft40">Cláusulas Sobreviventes ao Encerramento:</span>
      </p>
      <p class="p125 ft2">
        As Seções 5, 6, 7 e 9 destes Termos devem sobreviver a qualquer
        encerramento deste Contrato.
      </p>
      <p class="p126 ft26">
        <span class="ft55">11.</span
        ><span class="ft56">MECANISMOS DE DENÚNCIAS E SOLUÇÃO DE DISPUTAS</span>
      </p>
      <p class="p51 ft17">
        <span class="ft15">11.1.</span
        ><span class="ft16">Mecanismos de Denúncias:</span>
      </p>
      <p class="p33 ft8">
        A XGROW tem um
        <a href="https://hotmart1.typeform.com/to/Q34kSn">canal de denúncias </a
        >para que quaisquer interessados possam reportar violação de direitos e
        outras irregularidades na Plataforma, inclusive infrações a estes Termos
        e às Políticas da XGROW. Essas denúncias são apuradas com rigor, e podem
        resultar em consequências diversas aos envolvidos. Por isso, é muito
        importante que o canal de denúncias seja utilizado com ética. Em caso de
        dúvida sobre a ocorrência de infração, recomendamos que busque
        orientação jurídica antes de enviar qualquer notificação, além de
        avaliar se existem formas de provar o que pretende informar.
      </p>
      <p class="p37 ft24">
        <span class="ft15">11.2.</span
        ><span class="ft57"
          >Denúncias sobre violação a direitos de propriedade intelectual:</span
        >
      </p>
      <p class="p15 ft2">
        Se a sua denúncia for relativa a violação de direitos de propriedade
        intelectual, você deve seguir todas as orientações do canal de
        denúncias, incluindo o compartilhamento de informações capazes de provar
        os fatos denunciados, em especial: (a) suas informações de contato, para
        que possamos nos comunicar com
      </p>
    </div>
    <div id="page_34">
      <div id="id34_1">
        <p class="p0 ft2">
          você; (b) informações sobre o conteúdo denunciado, com o detalhamento
          do ocorrido e os documentos demonstrativos dos fatos narrados, no que
          for possível;
        </p>
        <p class="p22 ft2">
          <span class="ft1">(c)</span
          ><span class="ft58"
            >informações sobre a titularidade dos direitos de propriedade
            intelectual que possam ter sido violados; e (d) declaração de
            ausência de autorização de uso de propriedade intelectual, assinada
            por você em via física ou eletrônica.</span
          >
        </p>
        <p class="p127 ft2">
          <span class="ft1">11.2.1.</span
          ><span class="ft59"
            >A denúncia deve ser enviada pela pessoa cujos direitos de
            propriedade intelectual foram violados, ou por representante munido
            de procuração com poderes para tanto. A XGROW não aceita denúncias
            enviadas por terceiros.</span
          >
        </p>
        <p class="p25 ft2">
          <span class="ft1">11.2.2.</span
          ><span class="ft27"
            >Ao encaminhar uma denúncia, o denunciante declara estar ciente e
            concorda que a XGROW compartilhará suas informações com o
            denunciado, e que o denunciado poderá contatar o denunciante para
            esclarecer os fatos e dirimir a questão.</span
          >
        </p>
        <p class="p28 ft8">
          <span class="ft1">11.2.3.</span
          ><span class="ft60"
            >No prazo de 10 dias contados do encaminhamento da denúncia pela
            XGROW, o denunciado pode apresentar a sua defesa, informando os
            motivos que desqualificam a denúncia apresentada e comprovando a
            titularidade do direito supostamente violado. O denunciado deve
            enviar: (a) informações de contato; (b) informações sobre o conteúdo
            denunciado; (c) informações sobre a titularidade dos direitos de
            propriedade intelectual sobre o Produto denunciado, incluindo a
            documentação comprobatória pertinente; e (d) declaração de
            discordância sobre a denúncia de violação a direitos de propriedade
            intelectual, </span
          ><nobr>responsabilizando-se</nobr> por quaisquer danos causados pela
          continuidade da comercialização ou do acesso ao Produto objeto da
          denúncia.
        </p>
        <p class="p128 ft24">
          <span class="ft15">11.3.</span
          ><span class="ft61"
            >Denúncias sobre divulgação de cenas de nudez ou atos sexuais de
            caráter privado:</span
          >
        </p>
      </div>
      <div id="id34_2">
        <p class="p27 ft2">
          Se a sua denúncia for relativa a violação da intimidade decorrente da
          divulgação, sem autorização de seus participantes, de imagens, de
          vídeos ou de outros materiais contendo cenas de nudez ou de atos
          sexuais de caráter privado, a
        </p>
      </div>
    </div>
    <div id="page_35">
      <p class="p9 ft2">
        XGROW promoverá a indisponibilização desse conteúdo, no âmbito e nos
        limites técnicos do seu serviço, de forma diligente, em até 10 dias
        corridos da data do recebimento pela XGROW de todas as informações
        necessárias para que a XGROW possa tomar essa providência. Para isso,
        você deve indicar precisamente:
      </p>
      <p class="p73 ft2">
        <span class="ft1">(a)</span
        ><span class="ft62"
          >a URL onde está o material objeto da denúncia; e (b) os meios que
          possibilitem à XGROW identificar a vítima no material denunciado,
          conforme sua descrição. É possível denunciar apenas conteúdo que
          envolva você, um familiar ou outra pessoa de quem você seja o
          representante legal, com as comprovações de vínculo ou parentesco.
          Materiais que envolvam outras pessoas devem ser denunciados por elas,
          seus familiares ou responsáveis.</span
        >
      </p>
      <p class="p91 ft17">
        <span class="ft15">11.4.</span
        ><span class="ft16">Providências a serem adotadas pela XGROW:</span>
      </p>
      <p class="p33 ft2">
        Qualquer denúncia deve ser encaminhada contendo todas as informações
        exigidas nos itens anteriores. A XGROW não iniciará qualquer processo de
        verificação se verificar a ausência de qualquer daquelas informações.
      </p>
      <p class="p90 ft17">
        <span class="ft15">11.5.</span
        ><span class="ft16">Vedação ao anonimato:</span>
      </p>
      <p class="p51 ft1">
        A XGROW não dará prosseguimento a denúncias anônimas.
      </p>
      <p class="p48 ft17">
        <span class="ft15">11.6.</span
        ><span class="ft16">Responsabilidade do denunciante:</span>
      </p>
      <p class="p35 ft8">
        O envio intencional de denúncias falsas ou enganosas pode levar à
        aplicação de qualquer das medidas previstas na Seção 10 destes Termos,
        inclusive o bloqueio definitivo de conta, se o denunciante for Usuário
        da Plataforma, além da sua responsabilidade por danos, de acordo com a
        legislação aplicável. Esclarecemos que denunciantes de
        <nobr>má-fé</nobr> podem ser investigados pelas autoridades competentes
        e que XGROW cooperará com as investigações oficiais nesse sentido. O
        denunciante responde integralmente por quaisquer danos que causar ao
        denunciado, à XGROW ou a terceiros em razão de denúncias infundadas,
        principalmente nos casos em que a XGROW, diante da denúncia, e a seu
        exclusivo
      </p>
    </div>
    <div id="page_36">
      <div id="id36_1">
        <p class="p50 ft2">
          critério, bloquear preventiva ou definitivamente a comercialização ou
          o acesso a um Produto.
        </p>
        <p class="p80 ft17">
          <span class="ft15">11.7.</span
          ><span class="ft16">Processo de Apuração de Denúncias:</span>
        </p>
        <p class="p35 ft8">
          Se a XGROW entender que uma denúncia não tem fundamentos ou elementos
          de prova suficientes para iniciar processo interno de apuração, a
          XGROW pode arquivar a denúncia e encerrar a ocorrência reportada. Se a
          XGROW entender que a apuração dos fatos é necessária, ela deve
          encaminhar a denúncia ao denunciado ou ao responsável pelo Produto
          denunciado, sem prejuízo de a XGROW adotar, preventivamente e a seu
          exclusivo critério, quaisquer das medidas previstas na Seção 10 destes
          Termos, de modo isolado ou cumulativo. O denunciado pode responder a
          denúncia em até 10 dias contados do seu encaminhamento pela
        </p>
        <p class="p129 ft1">XGROW.</p>
        <p class="p6 ft8">
          <span class="ft1">11.7.1.</span
          ><span class="ft63"
            >A XGROW pode iniciar procedimento de investigação interna, a
            qualquer tempo, sempre que entender necessário à apuração dos fatos
            denunciados, sobretudo diante (a) de reclamações cujo esclarecimento
            seja capaz de solucionar a potencial disputa entre os Usuários, (b)
            de fatos que não tenham sido narrados de forma clara, mas que tragam
            indícios de irregularidades praticadas pelo denunciado ou (c) de
            denúncias com inconsistências ou indícios indicativos de </span
          ><nobr>má-fé,</nobr> fraude ou dolo do denunciante. Os resultados das
          investigações internas conduzidas pela XGROW poderão ser
          compartilhados com as autoridades competentes, sempre que exigido por
          lei ou necessário. Se o compartilhamento não for obrigatório por lei,
          cabe exclusivamente à XGROW definir se irá divulgar ou não quaisquer
          resultados de sua apuração.
        </p>
        <p class="p82 ft17">
          <span class="ft15">11.8.</span
          ><span class="ft16">Avaliação das Denúncias pela XGROW:</span>
        </p>
      </div>
      <div id="id36_2">
        <p class="p27 ft2">
          Se o denunciado não responder a denúncia em 10 dias, a XGROW pode
          aplicar, em caráter definitivo, quaisquer das medidas previstas na
          Seção 10 destes Termos, de modo isolado ou cumulativo, se entender
          cabíveis, dando ciência ao denunciante e
        </p>
      </div>
    </div>
    <div id="page_37">
      <p class="p9 ft8">
        comunicando as medidas ao Usuário afetado. A XGROW também pode aplicar
        quaisquer das medidas previstas na Seção 10 destes Termos, de modo
        isolado ou cumulativo, se, diante da resposta do denunciado, concluir
        que houve infração aos Termos, violação de direitos ou outras
        irregularidades praticadas na Plataforma. Se a XGROW, por si ou por
        terceiros por ela determinados, entender que a questão denunciada é
        razoavelmente controversa, a XGROW pode instaurar um procedimento de
        solução de disputas entre os Usuários.
      </p>
      <p class="p49 ft17">
        <span class="ft15">11.9.</span
        ><span class="ft16">Solução de Disputas entre Usuários:</span>
      </p>
      <p class="p35 ft8">
        Os serviços da XGROW são prestados para conectar os Usuários entre si.
        As disputas que surgem dessas relações devem ser resolvidas entre os
        Usuários diretamente, inclusive aquelas relativas à qualidade, às
        garantias e aos aspectos técnicos dos Produtos. Com o intuito de
        facilitar o diálogo para solução de disputas, além do canal de
        denúncias, a XGROW oferece serviço de suporte aos Usuários, sem custo, e
        que atende por meio do <nobr>e-mail</nobr> suporte@xgrow.com. Uma vez
        acionado, o serviço de suporte pode entrar em contato com os Usuários
        envolvidos numa disputa. Você concorda que deve aplicar seus melhores
        esforços para responder aos chamados do serviço de suporte, com foco na
        solução do problema, no menor tempo possível e dentro dos prazos
        estabelecidos pela XGROW. Se não for possível a composição entre os
        Usuários acerca de uma disputa, a XGROW pode (mas não se obriga a)
        encerrar a disputa mediante decisão própria, pautada na
        <nobr>boa-fé</nobr> e em parâmetros de equidade, considerando as regras
        destes Termos e das Políticas da XGROW. A XGROW pode delegar a solução
        de qualquer disputa a terceiros por ela determinados, inclusive
        mecanismos de autorregulamentação. A decisão da XGROW a respeito da
        disputa, ou de quem ela designar, deve ser acatada de modo integral e
        irrecorrível pelos Usuários envolvidos.
      </p>
    </div>
    <div id="page_38">
      <p class="p0 ft4">12. DISPOSIÇÕES FINAIS</p>
      <p class="p32 ft17">
        <span class="ft15">12.1.</span
        ><span class="ft40">Foro e Lei Aplicável:</span>
      </p>
      <p class="p33 ft2">
        O foro da Comarca de Barueri/SP fica eleito como o único competente, com
        exclusão de qualquer outro, por mais privilegiado que seja, para dirimir
        quaisquer controvérsias referentes a estes Termos e às Políticas da
        XGROW. Estes Termos e as Políticas da XGROW são regidos pelas leis do
        Brasil, com exclusão de qualquer outra, a eles não se aplicando a
        Convenção das Nações Unidas sobre Contratos de Compra e Venda
        Internacional de Mercadorias.
      </p>
      <p class="p117 ft17">
        <span class="ft15">12.2.</span><span class="ft40">Dúvidas:</span>
      </p>
      <p class="p35 ft8">
        O site da XGROW é a sua fonte primária com relação a esses Termos, às
        Políticas da XGROW, aos Serviços da XGROW ou a qualquer programa,
        funcionalidade, recurso ou questões em geral relacionados à Plataforma.
        Nele você pode encontrar, inclusive, muito material educativo gratuito,
        além de dicas e guias de melhores práticas. Nenhum terceiro tem o
        direito ou autorização da XGROW para falar em nome dela, e todas as
        práticas estabelecidas ou sugeridas por essas pessoas devem ser
        consideradas não oficiais. Se as informações contidas no site não forem
        suficientes para esclarecer suas dúvidas, entre em contato com o nosso
        suporte.
      </p>
    </div>
  </div>
</section>
@endsection
