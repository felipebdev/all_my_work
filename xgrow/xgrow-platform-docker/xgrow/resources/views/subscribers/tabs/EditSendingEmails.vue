<template>
  <Table id="sendingEmailTable">
    <template v-slot:title>
      <div class="xgrow-table-header w-100">
        <h5 class="title mt-3">E-mails enviados: {{ pagination.totalResults }}</h5>
        <p>Veja todos os seus e-mails enviados.</p>
      </div>
    </template>
    <template v-slot:header>
      <th>Data do envio</th>
      <th>Assunto</th>
      <th width="125px">Status</th>
    </template>
    <template v-if="emails.length > 0" v-slot:body>
      <tr :key="email.subject" v-for="email in emails">
        <td>{{ formatDateTimeSingleLine(email.sendDate) }}</td>
        <td>{{ email.subject }}</td>
        <td>
          <div class="row">
            <div id="sent-${id}" data-toggle="tooltip" :title="`E-mail enviado em: \n${formatDateTimeSingleLine(email.sendDate)}`"
            class="xgrow-fa-icons-emails-logs col-2">
                <i id="icon-sent-${id}" class="fas fa-paper-plane fa-lg" aria-hidden="true"></i>
            </div>
            <div id="received-${id}" data-toggle="tooltip" title="O e-mail ainda não foi recebido" class="xgrow-fa-icons-emails-logs col-2" style="background-color: #454954;">
                <i id="icon-received-${id}" class="fas fa fa-check fa-lg" style="color: #2A2E39;" aria-hidden="true"></i>
            </div>
            <div id="visualized-${id}" data-toggle="tooltip" title="O e-mail ainda não foi visualizado" class="xgrow-fa-icons-emails-logs col-2" style="background-color: #454954;">
                <i id="icon-visualized-${id}" class="fas fa-eye fa-lg" style="color: #2A2E39;" aria-hidden="true"></i>
            </div>
        </div>
        </td>
      </tr>
    </template>
    <template v-else v-slot:body>
      <tr>
        <td colspan="3" class="text-center">Sem resultados.</td>
      </tr>
    </template>
    <template v-slot:footer>
      <Pagination
        class="mt-4"
        :total-pages="pagination.totalPages"
        :total="pagination.totalResults"
        :current-page="pagination.currentPage"
        @page-changed="(page) => paginationChange('currentPage', page)"
        @limit-changed="(page) => paginationChange('limit', page)"
      />
    </template>
  </Table>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import formatDateTimeSingleLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeSingleLine";

export default {
  components: {
    Table,
    Pagination,
  },
  mixins: [formatDateTimeSingleLine],
  data() {
    return {
      emails: [
        {
          sendDate: "2023-01-01 12:00",
          subject: "Assunto 1",
          status: [],
        },
        {
          sendDate: "2023-02-02 12:00",
          subject: "Assunto 2",
          status: [],
        },
        {
          sendDate: "2023-02-02 13:00",
          subject: "Assunto 3",
          status: [],
        },
      ],
      pagination: {
        totalPages: 1,
        totalResults: 3,
        currentPage: 1,
        limit: 25,
      },
    };
  },
  methods: {
    async getEmails() {},
    async paginationChange(type, page) {
      this.pagination[type] = parseInt(page);
      await this.getEmails();
    },
  },
};
</script>

<style>
.table-responsive {
  overflow-x: hidden!important;
}
</style>
