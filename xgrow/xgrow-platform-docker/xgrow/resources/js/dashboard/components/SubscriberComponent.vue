<template>
  <card-container-component
    :without-background="true"
    :padding-top="0"
    :padding-bottom="0"
  >
    <div class="row">
      <subscriber-card-component
        :card-data="subscriberData.activeSubscribers.toString()"
        :info="subscriberData.activeSubscribersPercentage"
        icon="fa-user-check"
        label="Alunos Ativos"
        xxl="3"
        xl="6"
        lg="6"
        md="6"
      ></subscriber-card-component>

      <subscriber-card-component
        :card-data="subscriberData.newSubscribers.toString()"
        :info="0"
        icon="fa-user-plus"
        label="Novos Alunos"
        xxl="3"
        xl="6"
        lg="6"
        md="6"
      ></subscriber-card-component>

      <subscriber-card-component
        :card-data="subscriberData.cancelledSubscribers.toString()"
        :info="0"
        icon="fa-ban"
        label="Cancelamentos"
        xxl="3"
        xl="6"
        lg="6"
        md="6"
        icon-color="red"
      ></subscriber-card-component>

      <!-- <subscriber-card-component
        v-if="false"
        :card-data="subscriberData.automaticRegisteredPayingStudents.toString()"
        :info="0"
        icon="fa-user-cog"
        label="Alunos Pagantes Cadastrados Automaticamente"
        xxl="3"
        xl="6"
        lg="6"
        md="6"
      ></subscriber-card-component> -->
    </div>
  </card-container-component>
</template>

<script>
import CardContainerComponent from "../../components/CardContainerComponent";
import SubscriberCardComponent from "../components/SubscriberCardComponent";

export default {
  name: "subscriber-component",
  components: { CardContainerComponent, SubscriberCardComponent },
  props: {
    subscriberData: {
      required: true,
      type: Object,
    },
  },
  data() {
    return {};
  },
};
</script>

