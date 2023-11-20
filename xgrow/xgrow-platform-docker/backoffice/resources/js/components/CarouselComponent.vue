<template>
    <div :id="id" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li v-for="(item, index) in items" data-target="#carouselExampleIndicators3" :data-slide-to="index" class="active"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div v-for="(item, index) in items"  class="carousel-item " :class="{active : index == 0}">
                <img class="img-responsive" :src="item.src" :alt="item.alt">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="text-white">{{ item.title }}</h3>
                    <p>{{ item.text }}</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" :href="'#' + id" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only" v-html="previousLabel"> </span>
        </a>
        <a class="carousel-control-next" :href="'#' + id" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</template>


<script>
    export default {

        props: {
            items: {
                required: true,
                default: []
            },
            previousLabel: {
                default: 'Previous'
            },
            id: {
                default: function (){
                    return Math.random().toString(36).substring(7)
                }
            }
        },

    }
</script>
