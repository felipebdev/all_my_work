<template>
    <div class="author-profile">
        <img class="author-profile__photo" :src="photo" />
        <p class="author-profile__name">{{name}}</p>
    </div>
</template>

<script>
export default {
    name: "author-profile",
    props: {
        photo: { type: String, required: true },
        name: { type: String, required: true },
    }
}
</script>

<style lang="scss" scoped>
    .author-profile {
        display: flex;
        gap: 10px;
        align-items: center;

        &__photo {
            border-radius: 50%;
            border: 1px solid #646D85;
            display: block;
            height: 28px;
            object-fit: cover;
            object-position: center;
            width: 28px;
        }

        &__name {
            color: #FFFFFF;
            font-size: 14px;
            line-height: 1.6;
        }
    }
</style>
