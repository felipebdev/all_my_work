<template>
  <Alert title="Instruções para importação dos dados" status="info">
    <ul>
      <li
        :key="instruction"
        v-for="instruction in instructions"
        style="list-style: disc"
        v-html="instruction"
      />
    </ul>
  </Alert>
</template>
<script>
import Alert from "./Alert";
export default {
  components: {
    Alert,
  },
  data() {
    return {
      instructions: [
        "Só é permitido o envio de arquivos CSV;",
        "Os campos obrigatórios são Nome e E-mail (nessa ordem);",
        "Verifique qual é o delimitador de dados de seu CSV antes de enviar;",
        "Antes da importação, verifique se existem filtros na planilha para não importar leads indesejados;",
        `Baixe o arquivo de exemplo <a href="/xgrow-vendor/assets/files/ModeloArquivoEnvioCupons.csv" style="color: inherit" target="_blank"><b>clicando aqui;</b></a>`,
        "A primeira linha é utilizada para o nome das colunas e não será importada para o sistema.",
      ],
    };
  },
};
</script>

<style lang="scss" scoped>
a {
  color: inherit !important;
}
</style>
