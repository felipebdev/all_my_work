<template>
  <div>
    <!-- User section -->
    <div class="user-info">
      <div class="profile mt-2">
        <div class="img">
          <img :src="userImg || '/images/profile.png'" alt="Foto de perfil" />
        </div>
        <div class="data">
          <p class="name">{{ userName }}</p>
          <p class="email">{{ userEmail }}</p>
        </div>
      </div>
      <div class="buttons mt-2">
        <button @click.prevent="redirect(userLink)">
          <i class="fas fa-cog"></i>
          <p>Configurações</p>
        </button>
      </div>
    </div>

    <!-- Details section -->
    <div class="details mt-3">
      <p class="title">DETALHES</p>

      <div class="details-content my-2 p-2">
        <div class="data">
          <p class="main" v-if="showDetails.earnings">
            {{ lastDaysEarningsFormated }}
          </p>
          <div class="main" v-else></div>
          <p class="info">Ganho nos últimos 7 dias</p>
        </div>
        <div class="button" v-if="showDetails.earnings">
          <button @click="toggleDetail('earnings')">
            <i class="fa fa-eye"></i>
          </button>
        </div>
        <div class="button" v-else>
          <button @click="toggleDetail('earnings')">
            <i class="fa fa-eye-slash"></i>
          </button>
        </div>
      </div>

      <div class="details-content my-2 p-2">
        <div class="data">
          <p class="main" v-if="showDetails.balance">
            {{ currentBalanceFormated }}
          </p>
          <div class="main" v-else></div>
          <p class="info">Saldo atual</p>
        </div>
        <div class="button" v-if="showDetails.balance">
          <button @click="toggleDetail('balance')">
            <i class="fa fa-eye"></i>
          </button>
        </div>
        <div class="button" v-else>
          <button @click="toggleDetail('balance')">
            <i class="fa fa-eye-slash"></i>
          </button>
        </div>
      </div>

      <div class="details-content my-2 p-2">
        <div class="data">
          <p class="main" v-if="showDetails.balanceAvailable">
            {{ balanceAvailableFormated }}
          </p>
          <div class="main" v-else></div>
          <p class="info">Saldo disponível</p>
        </div>
        <div class="button" v-if="showDetails.balanceAvailable">
          <button @click="toggleDetail('balanceAvailable')">
            <i class="fa fa-eye"></i>
          </button>
        </div>
        <div class="button" v-else>
          <button @click="toggleDetail('balanceAvailable')">
            <i class="fa fa-eye-slash"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Meta de faturamento -->
    <div class="details meta mt-3" v-if="false">
      <p class="title">META DE FATURAMENTO</p>

      <div class="meta-content my-2 p-2">
        <p class="title">Faturamento</p>
        <div class="meta-progress mt-2">
          <div class="top">
            <p>Atual</p>
            <p>Meta</p>
          </div>
          <div class="mprogress">
            <div class="background"></div>
            <div
              class="actual-progress"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
          <div class="bottom">
            <p>{{ actualProgressFormated }}</p>
            <p>{{ goalFormated }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "revenue-component",
  props: {
    // User data
    userImg: {
      required: false,
      type: String,
      default: "",
    },
    userName: {
      required: true,
      type: String,
    },
    userEmail: {
      required: true,
      type: String,
    },
    userLink: {
      required: true,
      type: String,
    },

    // Goals data
    actualProgress: {
      required: true,
      type: Number,
    },
    goal: {
      required: true,
      type: Number,
    },
  },
  data() {
    return {
      showDetails: {
        earnings: true,
        balance: true,
        balanceAvailable: true,
      },
      lastDaysEarnings: 0,
      currentBalance: 0,
      balanceAvailable: 0,
    };
  },
  computed: {
    lastDaysEarningsFormated: function () {
      return this.moneyFormat(this.lastDaysEarnings);
    },
    currentBalanceFormated: function () {
      return this.moneyFormat(this.currentBalance);
    },
    balanceAvailableFormated: function () {
      return this.moneyFormat(this.balanceAvailable / 100);
    },
    actualProgressFormated: function () {
      return this.moneyFormat(this.actualProgress);
    },
    goalFormated: function () {
      return this.moneyFormat(this.goal);
    },
    progress: function () {
      return (this.actualProgress * 100) / this.goal;
    },
  },
  methods: {
    getUserDetailsSettings: function () {
      const ls = window.localStorage;
      const settings = JSON.parse(ls.getItem("userDetailsSettings"));

      if (settings !== null) {
        this.showDetails = {
          earnings: settings.earnings,
          balance: settings.balance,
          balanceAvailable: settings.balanceAvailable,
        }
      }
    },
    getUserBalances: async function () {
      let balanceData, antecipationData;

      // Get the current balance (waiting_funds_amount) and
      // the balance available (available_amount)
      await axios
        .get("/recipient/balance/")
        .then((response) => {
          balanceData = response.data;
          const { available_amount = 0, waiting_funds_amount = 0 } = balanceData;

          this.currentBalance = waiting_funds_amount / 100;
          this.balanceAvailable = available_amount;
        })
        .catch((error) => {
          console.log(error);
        });

    },
    getLastDaysEarnigs: function () {
      const params = { period: this.getPeriod(), allDate: 0 };

      axios
        .get("/api/reports/financial/total-billing", { params })
        .then((response) => {
          this.lastDaysEarnings = response.data.data.total;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    toggleDetail: function (property) {
      // Update local storage
      const ls = window.localStorage;
      let settings = JSON.parse(ls.getItem("userDetailsSettings"));

      this.showDetails[property] = !this.showDetails[property];

      if (settings == null) {
        settings = {
          earnings: true,
          balance: true,
          balanceAvailable: true,
        };
      }

      settings[property] = this.showDetails[property];
      ls.setItem("userDetailsSettings", JSON.stringify(settings));
    },
    redirect: function (url) {
      window.location.href = url;
    },
    moneyFormat: function (amount) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(amount);
    },
    getPeriod: function () {
      const today = new Date();
      const lastDays = new Date();

      lastDays.setDate(today.getDate() - 6);

      const firstDate = `${
        lastDays.getDate() < 10 ? "0" + lastDays.getDate() : lastDays.getDate()
      }/${
        lastDays.getMonth() + 1 < 10
          ? "0" + (lastDays.getMonth() + 1)
          : lastDays.getMonth() + 1
      }/${lastDays.getFullYear()}`;

      const lastDate = `${
        today.getDate() < 10 ? "0" + today.getDate() : today.getDate()
      }/${
        today.getMonth() + 1 < 10
          ? "0" + (today.getMonth() + 1)
          : today.getMonth() + 1
      }/${today.getFullYear()}`;

      return firstDate + " - " + lastDate;
    },
  },
  async mounted() {
    this.getUserDetailsSettings();
    await this.getUserBalances();
    this.getLastDaysEarnigs();
  },
};
</script>

<style lang="scss" scoped>
@import url("/public/xgrow-vendor/assets/css/colors.css");

.data {
  p {
    overflow-wrap: break-word;
    word-break: break-all;
    white-space: pre-wrap;
  }
}

.user-info {
  width: 100%;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  flex-wrap: wrap;
  align-items: center;
  padding-bottom: 1rem;
  border-bottom: 2px solid #3c4151;

  .profile {
    display: flex;
    flex-direction: row;
    align-items: center;

    .img {
      width: 40px;
      height: 40px;
      margin-right: 10px;

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ffffff;
        box-shadow: 0px 4px 4px 0px #00000040;
      }
    }

    .data {
      p {
        overflow-wrap: break-word;
        word-break: break-all;
        white-space: pre-wrap;
      }

      .name {
        font-size: 0.875rem;
        font-weight: bold;
      }

      .email {
        font-size: 0.688rem;
        opacity: 0.8;
      }
    }
  }

  .buttons {
    button {
      background: #222429;
      border: none;
      color: #ffffff;
      width: 2rem;
      height: 2rem;
      border-radius: 0.5rem;
      transition-duration: 0.2s;

      i {
        font-size: 1rem;
      }

      &:hover,
      &:focus {
        background: #0f1013;
        border: 2px solid var(--green4);
      }

      p {
        display: none;
      }
    }
  }
}

.details {
  .title {
    color: #7a7f8d;
    font-weight: bolder;
    font-size: 0.75rem;
  }

  .details-content {
    background: #3d4353;
    border-radius: 0.313rem;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;

    .data {
      .main {
        font-weight: bold;
        font-size: 0.875rem;
      }

      div.main {
        width: 70px;
        height: 0.875rem;
        background: #ffffff;
        border-radius: 3px;
        margin: 3px 0;
      }

      .info {
        opacity: 0.8;
        font-size: 0.75rem;
      }
    }

    .button {
      button {
        background: #646d85;
        border: none;
        color: #ffffff;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        transition-duration: 0.2s;

        &:hover,
        &:focus {
          background: #383d4d;
          border: 2px solid var(--green4);
        }
      }
    }
  }
}

.meta {
  .meta-content {
    background: #3d4353;
    border-radius: 0.313rem;

    .title {
      font-size: 0.875rem;
      color: #ffffff;
      font-weight: bold;
    }

    .top {
      display: flex;
      justify-content: space-between;

      p {
        font-size: 0.75rem;
      }
    }

    .bottom {
      display: flex;
      justify-content: space-between;

      p {
        font-size: 0.688rem;
      }
    }

    .mprogress {
      width: 100%;
      margin: 0.5rem 0;

      .background {
        width: 100%;
        height: 3px;
        background: #222429;
        border-radius: 100px;
      }

      .actual-progress {
        height: 3px;
        background: var(--green4);
        border-radius: 100px;
        margin-top: -3px;
      }
    }
  }
}

@media only screen and (max-width: 900px) {
  .data {
    p {
      text-align: center;
    }
  }

  .user-info {
    flex-direction: column;

    .profile {
      flex-direction: column;
    }

    .buttons {
      button {
        width: auto;
        height: 2rem;
        padding: 0 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;

        i {
          margin-right: 0.5rem;
        }

        p {
          display: flex;
        }
      }
    }
  }

  .details {
    .details-content {
      flex-direction: column;
      align-items: center;
      justify-content: center;

      .button {
        margin: 0.5rem 0;
      }
    }
  }
}
</style>
