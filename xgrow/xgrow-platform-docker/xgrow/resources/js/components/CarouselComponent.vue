<template>
  <div
    :class="[
      `col-xxl-${xxl}`,
      `col-xl-${xl}`,
      `col-lg-${lg}`,
      `col-md-${md}`,
      `col-sm-${sm}`,
      `col-${xs}`,
    ]"
  >
    <div
      id="xgrowCarousel"
      class="carousel slide xgrow-carousel"
      data-bs-ride="carousel"
    >
      <div class="carousel-inner xgrow-carousel-inner">
        <div
          class="carousel-item"
          :class="[idx == 0 ? 'active' : '']"
          v-for="(item, idx) in carouselItems"
          v-bind:key="idx"
        >
          <div class="news-item">
            <div class="background">
              <img :src="item.backgroundImg" />
            </div>
            <div class="foreground">
              <p class="title">{{ item.title }}</p>
              <p class="subtitle" v-if="item.subtitle != ''">
                {{ item.subtitle }}
              </p>
              <a
                class="button"
                :href="item.link"
                target="_blank"
                v-if="item.link != '#' && item.link != null"
                >Ver mais</a
              >
            </div>
          </div>
        </div>
      </div>

      <div class="xgrow-carousel-buttons" v-if="carouselItems.length > 1">
        <button
          class="carousel-control-prev"
          type="button"
          data-bs-target="#xgrowCarousel"
          data-bs-slide="prev"
        >
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>

        <div class="carousel-indicators">
          <button
            type="button"
            data-bs-target="#xgrowCarousel"
            :data-bs-slide-to="idx"
            :aria-label="`Slide ${idx + 1}`"
            :class="[idx == 0 ? 'active' : '']"
            v-for="(item, idx) in carouselItems"
            v-bind:key="idx"
          ></button>
        </div>

        <button
          class="carousel-control-next"
          type="button"
          data-bs-target="#xgrowCarousel"
          data-bs-slide="next"
        >
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Próximo</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import bootstrapColumns from "./config/bootstrapColumnsProps";

export default {
  name: "carousel-component",
  props: {
    carouselItems: {
      required: true,
      type: Array,
      /*
       Um array constituido de objetos "carouselItem"
       que seguem o seguinte padrão:

       {
         backgroundImg: 'https://site-xgrow.vercel.app/assets/img/banner_1_.jpg',
         title: 'Título da novidade',
         subtitle: 'Descrição da novidade.',
         link: '#',
       }

        os camposde backgroundImg e title são obrigatórios
       */
    },
    /*
     Bootstrap/responsiviness
     tags properties
     */
    ...bootstrapColumns,
  },
};
</script>

<style lang="scss" scoped>
.xgrow-carousel-inner {
  .carousel-item,
  .carousel-item-next,
  .carousel-item-prev,
  .carousel-item-start,
  .carousel-item-end,
  .carousel-item.active {
    border-radius: 5px !important;
  }

  .carousel-item {
    .news-item {
      position: relative;
      width: 100%;
      aspect-ratio: 16 / 9;
      border-radius: 5px !important;

      .background {
        position: absolute;
        border-radius: 5px !important;
        width: 100%;
        height: 100%;

        img {
          border-radius: 5px !important;
          width: 100%;
          height: 100%;
          object-fit: cover;
          object-position: center center;
          aspect-ratio: 16 / 9;
        }
      }

      .foreground {
        width: 100%;
        height: 100%;
        position: relative;
        color: #fff !important;
        border-radius: 5px !important;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 0.5rem;
        background: linear-gradient(
          180deg,
          rgba(29, 31, 35, 0) 0%,
          rgba(29, 31, 35, 0.13) 20.01%,
          rgba(29, 31, 35, 0.86) 77.12%
        );
        transition-duration: 0.2s;

        .title {
          font-size: 1rem;
          font-weight: bold;
          transition-duration: 0.2s;
        }

        .subtitle {
          font-size: 0.8rem;
          transition-duration: 0.2s;
        }

        a.button {
          margin-top: 0.6rem;
          font-size: 1rem;
          text-decoration: none;
          background-color: #93bc1e;
          border-radius: 5px;
          color: #fff;
          max-width: max-content;
          padding: 0.2rem 0.7rem;
          margin-bottom: -40px;
          transition-duration: 0.2s;
        }

        &:hover {
          background: linear-gradient(
            180deg,
            rgba(29, 31, 35, 0) -4.79%,
            rgba(29, 31, 35, 0.42257) 12.8%,
            rgba(29, 31, 35, 0.86) 37.74%
          );

          a.button {
            margin-bottom: 0;
          }
        }
      }
    }
  }
}

.xgrow-carousel-buttons {
  // Reset bootstrap properties
  button,
  .carousel-indicators {
    position: relative !important;
  }
  .carousel-indicators {
    margin-left: 1rem !important;
    margin-right: 1rem !important;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    z-index: 0;

    button {
      width: 0.75rem;
      height: 0.75rem;
      opacity: 0.3;
      background-color: #000000;
      border-radius: 50%;
      border: none;
      transition-duration: 0.2s;
      margin: 0.25rem;
      z-index: 0;

      &.active {
        background-color: #93bc1e;
        opacity: 1;

        &:hover,
        &:focus {
          border: 3px solid #000000;
        }
      }

      &:hover,
      &:focus {
        border: 3px solid #93bc1e;
      }
    }
  }

  // Change buttons symbol and color
  .carousel-control-prev {
    .carousel-control-prev-icon {
      background-image: url("/xgrow-vendor/assets/img/carousel/left.svg");
      height: 0.85rem;
    }
  }
  .carousel-control-next {
    .carousel-control-next-icon {
      background-image: url("/xgrow-vendor/assets/img/carousel/right.svg");
      height: 0.85rem;
    }
  }
  .carousel-control-prev,
  .carousel-control-next {
    opacity: 1 !important;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    border: none;
    background-color: rgba(146, 188, 30, 0.3);

    &:hover,
    &:focus {
      border: 2px solid #93bc1e;
    }
  }

  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0.75rem 0.45rem 0.45rem 0.45rem;
  max-width: 100% !important;
}
</style>
