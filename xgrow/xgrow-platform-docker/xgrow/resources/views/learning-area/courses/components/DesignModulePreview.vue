<template>
    <div class="dm-border">
        <div class="dm-border__inner">
            <Row>
                <Col class="mb-4">
                <Title is-form-title class="m-0">Módulos do curso</Title>
                <Subtitle class="m-0">Descrição do curso...</Subtitle>
                </Col>
                <Col v-if="view === 'thumbnails'">
                <Accordion id="accordionThumb">
                    <AccordionItem id="headingOneT" title="1" subtitle="Nome do primeiro módulo" target-id="collapseOneT"
                        accordion-id="accordionThumb" is-open>
                        <Row class="dm-module__thumbs">
                            <Col sm="12" md="6" lg="4" xl="4" class="dm-module__thumbs-thumb">
                            <img src="https://las.xgrow.com/background-default.png" alt="Aula 1 - Nome desta aula"
                                class="img-fluid" />
                            <div>
                                <Title is-form-title>Aula 1 - Nome desta aula</Title>
                                <Subtitle is-small>12 min</Subtitle>
                            </div>
                            </Col>
                            <Col sm="12" md="6" lg="4" xl="4" class="dm-module__thumbs-thumb">
                            <img src="https://las.xgrow.com/background-default.png" alt="Aula 1 - Nome desta aula"
                                class="img-fluid" />
                            <div>
                                <Title is-form-title>Aula 2 - Nome desta aula</Title>
                                <Subtitle is-small>19 min</Subtitle>
                            </div>
                            </Col>
                            <Col sm="12" md="6" lg="4" xl="4" class="dm-module__thumbs-thumb">
                            <img src="https://las.xgrow.com/background-default.png" alt="Aula 1 - Nome desta aula"
                                class="img-fluid" />
                            <div>
                                <Title is-form-title>Aula 3 - Nome desta aula</Title>
                                <Subtitle is-small>15 min</Subtitle>
                            </div>
                            </Col>
                        </Row>
                    </AccordionItem>
                    <AccordionItem id="headingTwoT" title="2" subtitle="Nome do segundo módulo" target-id="collapseTwoT"
                        accordion-id="accordionThumb">
                        <Row class="dm-module__thumbs">
                            <Col sm="12" md="6" lg="4" xl="4" class="dm-module__thumbs-thumb">
                            <img src="https://las.xgrow.com/background-default.png" alt="Aula 1 - Nome desta aula"
                                class="img-fluid" />
                            <div>
                                <Title is-form-title>Aula 1 - Nome desta aula</Title>
                                <Subtitle is-small>12 min</Subtitle>
                            </div>
                            </Col>
                            <Col sm="12" md="6" lg="4" xl="4" class="dm-module__thumbs-thumb">
                            <img src="https://las.xgrow.com/background-default.png" alt="Aula 1 - Nome desta aula"
                                class="img-fluid" />
                            <div>
                                <Title is-form-title>Aula 2 - Nome desta aula</Title>
                                <Subtitle is-small>19 min</Subtitle>
                            </div>
                            </Col>
                            <Col sm="12" md="6" lg="4" xl="4" class="dm-module__thumbs-thumb">
                            <img src="https://las.xgrow.com/background-default.png" alt="Aula 1 - Nome desta aula"
                                class="img-fluid" />
                            <div>
                                <Title is-form-title>Aula 3 - Nome desta aula</Title>
                                <Subtitle is-small>15 min</Subtitle>
                            </div>
                            </Col>
                        </Row>
                    </AccordionItem>
                </Accordion>
                </Col>
                <Col v-if="view === 'list'">
                <Accordion id="accordionLine">
                    <AccordionItem id="headingOneL" title="1" subtitle="Nome do primeiro módulo" target-id="collapseOneL"
                        accordion-id="accordionLine" is-open>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 1 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 2 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 3 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 4 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                    </AccordionItem>
                    <AccordionItem id="headingTwoL" title="2" subtitle="Nome do segundo módulo" target-id="collapseTwoL"
                        accordion-id="accordionLine">
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 1 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 2 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 3 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                        <div class="dm-module">
                            <Title is-form-title icon="fa fa-video" icon-color="#FFFFFF">
                                Aula 4 - Nome desta aula
                            </Title>
                            <div class="dm-module__play">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>
                    </AccordionItem>
                </Accordion>
                </Col>
            </Row>
        </div>
    </div>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Accordion from "../../../../js/components/XgrowDesignSystem/Accordion/Accordion";
import AccordionItem from "../../../../js/components/XgrowDesignSystem/Accordion/AccordionItem";

export default {
    name: "DesignModulePreview",
    props: {
        view: { type: String, default: 'list' }
    },
    components: { AccordionItem, Accordion, DefaultButton, PipeVertical, Subtitle, Title, Col, Row }
}
</script>

<style lang="scss" scoped>
.dm-border {
    background: #2A2E39;
    border: 5px dashed #626775;

    .dm-border__inner {
        background: #595B63;
        min-height: 500px;
        padding: 20px;
    }

    .dm-module {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px solid #2A2E39;
        background: #333844;
        padding: 10px 0;

        h1 {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 400;
            font-size: 0.75rem;
            line-height: 1.125rem;
        }

        .dm-module__play {
            width: 36px;
            height: 30px;
            background: #ADDF45;
            box-shadow: 0 6px 15px rgba(24, 23, 23, 0.15);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;

            i {
                font-size: 12px;
            }
        }
    }

    .dm-module__thumbs {
        &-thumb {
            .img-fluid {
                width: 100%;
                margin-bottom: 12px;
            }

            h1 {
                font-family: 'Open Sans', serif;
                font-style: normal;
                font-weight: 400;
                font-size: .7rem !important;
                line-height: .8rem !important;
                color: #E7E7E7;
            }

            h3 {
                font-family: 'Open Sans', serif;
                font-style: normal;
                font-weight: 500;
                font-size: .6rem !important;
                line-height: 0.7rem !important;
                color: #E7E7E7;
            }
        }
    }
}
</style>
