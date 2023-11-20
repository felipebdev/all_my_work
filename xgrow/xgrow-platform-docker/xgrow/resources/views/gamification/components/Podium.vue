<template>
  <div class="podium-container">
    <div
      class="winner"
      :class="[`placed-${winner.place}`]"
      v-for="(winner, idx) in winners"
      v-bind:key="idx"
    >
      <div class="photo" v-if="winner.img != ''">
        <span class="placing">{{ winner.place }}º</span>
        <img :src="winner.img" />
      </div>

      <div class="place" v-else>
        <p>{{ winner.place }}º</p>
      </div>

      <div class="info">
        <p class="name">{{ winner.name }}</p>
        <p class="coins">
          <img src="/xgrow-vendor/assets/img/gamification/coin.svg" />
          {{ winner.coins }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Podium",
  props: {
    winners: {
      required: true,
      type: Array,
    },
  },
};
</script>

<style lang="scss" scoped>
.podium-container {
  width: 100%;
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;

  .winner {
    display: flex;
    align-items: center;
    margin: 10px 5px;

    &.placed-1 {
      .place {
        background-color: #93bc1e;
      }

      .photo {
        .placing {
          background-color: #93bc1e;
        }

        img {
          border: 2px solid #93bc1e;
        }
      }
    }

    .place {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      background-color: #515663;
      border-radius: 10px;

      p {
        font-weight: 700;
        font-size: 26px;
      }
    }

    .photo {
      position: relative;
      width: 40px;
      height: 40px;
      object-fit: cover;
      object-position: center center;
      margin-right: 10px;

      .placing {
        position: absolute;
        left: -5px;
        top: -5px;
        font-size: 14px;
        font-weight: 700;
        line-height: 24.5px;
        background-color: #515663;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        border-radius: 100%;
        border: 2px solid #515663;
      }
    }

    .info {
      .name {
        font-weight: 700;
        font-size: 14px;
        line-height: 19px;
      }

      .coins {
        font-weight: 400;
        font-size: 12px;
        line-height: 17.5px;

        img {
          height: 12px;
          margin-right: 2.5px;
          margin-top: -2.5px;
        }
      }
    }
  }
}
</style>