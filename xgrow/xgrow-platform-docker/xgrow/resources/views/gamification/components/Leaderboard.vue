<template>
  <div class="leaderboard-container">
    <ul :style="{ maxHeight: `${maxHeight}px` }">
      <li v-for="(user, idx) in list" v-bind:key="idx">
        <p class="place">{{ user.place }}º</p>
        <p class="name">
          <img
            :src="
              user.img ||
              'xgrow-vendor/assets/img/gamification/rounded-avatar.svg'
            "
          />
          {{ user.name }}
        </p>
        <p class="coins">
          <img src="/xgrow-vendor/assets/img/gamification/coin.svg" />
          {{ user.coins }}
          Xcoins
        </p>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "Leaderboard",
  props: {
    list: {
      required: true,
      type: Array,
    },
    maxHeight: {
      required: false,
      type: Number,
      default: 279,
    },
  },
};
</script>

<style lang="scss" scoped>
.leaderboard-container {
  width: 100%;
  margin-top: 20px;

  ul {
    background-color: #3b404e;
    border-radius: 5px;
    padding: 5px 10px;
    overflow-y: auto;

    li {
      display: flex;
      align-items: center;
      padding: 10px;
      border-bottom: 1px solid #424857;

      &:last-child {
        border-bottom: none;
      }

      .place {
        color: #878ea1;
        font-weight: 600;
        font-size: 16px;
      }

      .name {
        display: flex;
        align-items: center;
        margin-left: 20px;
        flex: 1;
        font-weight: 600;
        font-size: 16px;

        img {
          width: 23px;
          height: 23px;
          object-fit: cover;
          object-position: center center;
          border: 1px solid #2a2e39;
          border-radius: 50%;
          margin-right: 5px;
        }
      }

      .coins {
        display: flex;
        align-items: center;
        font-weight: 400;
        font-size: 12px;
        color: #bababa;

        img {
          width: 12px;
          height: 12px;
          object-fit: cover;
          object-position: center center;
          border-radius: 50%;
          margin-right: 5px;
        }
      }
    }
  }
}

@media only screen and (max-width: 450px) {
  .leaderboard-container {
    ul {
      li {
        flex-wrap: wrap;
        .coins {
          width: 100% !important;
          margin-left: 36.5px;
          margin-top: 5px;
        }
      }
    }
  }
}
</style>