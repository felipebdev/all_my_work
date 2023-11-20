<template>
  <Modal @close="close">
    <section class="new-item">
      <header class="new-item__header">
        <Title class="title">
          Selecione seu ícone
        </Title>

        <Row class="justify-content-center">
          <Col sm="12" md="6" lg="6" xl="6">
            <Select
              id="lib"
              label="Escolha sua biblioteca"
              placeholder="Selecione uma opção"
              :options="listLibOptions"
              v-model="selectedList"
            />
          </Col>
          <Col sm="12" md="6" lg="6" xl="6">
            <TheInput
              id="search"
              label="Procurar"
              placeholder="Insira o nome do ícone..."
              v-model="search"
            />
          </Col>
        </Row>
      </header>

      <div class="new-item__body">
        <div class="d-flex flex-wrap">
          <div
            v-for="(icon) in getListMap" :key="icon.name"
            class="p-2 icons"
            :class="{
              'icons--active':
              selectedIcon.icon && icon.name === selectedIcon.icon.name
            }"
            @click="selectIcon(icon)"
          >
            <svg style="fill: #ADDF45" :title="icon.name"
                :view-box.camel="icon.viewBox">
                <path :d="icon.svg"></path>
            </svg>
          </div>
        </div>
      </div>

      <footer class="new-item__actions">
        <DefaultButton text="Salvar" status="success" :onClick="saveIcon" />
      </footer>
    </section>
  </Modal>
</template>

<script>
import DefaultButton from '../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton'
import Modal from '../../../../../js/components/XgrowDesignSystem/Modals/Modal'
import Title from '../../../../../js/components/XgrowDesignSystem/Typography/Title'
import Subtitle from '../../../../../js/components/XgrowDesignSystem/Typography/Subtitle'
import Row from '../../../../../js/components/XgrowDesignSystem/Utils/Row'
import Col from '../../../../../js/components/XgrowDesignSystem/Utils/Col'
import Select from '../../../../../js/components/XgrowDesignSystem/Form/Select'
import TheInput from '../../../../../js/components/XgrowDesignSystem/Form/Input'
import ImageUpload from '../../../../../js/components/XgrowDesignSystem/Form/ImageUpload'

import {mapActions, mapState, mapStores} from "pinia";
import {useDesignConfigMenu} from "../../../../../js/store/design-config-menu.js"

export default {
  name: "SelectIcons",
  components: {
    DefaultButton,
    Modal,
    Title,
    Subtitle,
    Row,
    Col,
    Select,
    TheInput,
    ImageUpload
  },
  data() {
    return {
      selectedList: "",
      search: "",
      selectedIcon: {}
    }
  },
  computed: {
    ...mapStores(useDesignConfigMenu),
    ...mapState(useDesignConfigMenu, ["loadingStore", "listIcons", "listLibOptions"]),
    getListMap() {
      if (!this.selectedList) return [];

      let map = this.listIcons.filter(({name}) => name == this.selectedList)[0];
      let steps = Math.ceil(map.icons.length / 100);

      let mapped = this.recursiveRenderIcons(steps, map.icons);

      if (!this.search) return mapped;

      return mapped.filter(({name}) => name.toLowerCase().includes(this.search));
    }
  },
  methods: {
    ...mapActions(useDesignConfigMenu, ["getIconList"]),
    close() {
        document.querySelector('html').style.overflowY = 'auto';
      this.$emit('close');
    },
    recursiveRenderIcons(stop, arr, newArr = []) {
      if(stop != 0) {
        let sliced = arr.slice(stop * 100 - 100, stop * 100)

        newArr.push(...sliced);

        return this.recursiveRenderIcons(stop-1, arr, newArr);
      }

      return newArr.sort((a, b) => a.name > b.name);
    },
    selectIcon(icon) {
      this.selectedIcon = {
        name: this.selectedList,
        icon
      }
    },
    saveIcon() {
      if(!this.selectedList && Object.keys(this.selectedIcon).length == 0) {
        errorToast("Houve um erro na solicitação", "Selecione um ícone para salvar");
        return
      }

      this.$emit('selectedIcon', this.selectedIcon);
      this.$emit('close');
    }
  },
}
</script>

<style lang="scss" scoped>
  .new-item {
    padding: 40px 52px;

    &__header {
      border-bottom: 1px solid  rgba(#C4C4C4, .15);
      padding-bottom: 20px;
    }

    &__body {
      padding: 8px 0 32px;
      margin-top: 8px;
      margin-bottom: 8px;
      margin-right: 8px;
      max-height: 400px;
      overflow-y: auto;
    }

    &__actions {
      border-top: 1px solid  rgba(#C4C4C4, .15);
      padding-top: 40px;
      display: flex;
      justify-content: center;
      gap: 22px;
    }
  }

  .title {
    font-size: 18px;
    line-height: 25px;
    color: #FFFFFF;
  }

  .icons {
    width: 100%;
    max-width: 36px;
    max-height: 36px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;

    &:hover, &--active {
      border: 1px solid #ADDF45;
      background: rgba(#C4C4C4, .15);
    }
  }

</style>
