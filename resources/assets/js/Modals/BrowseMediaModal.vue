<script>
    import events from './../events';
    import { translator } from './../mixins';
    import MediaManager from './../MediaManager.vue';

    export default {
        name: 'media-browser-modal',

        props: ['id'],

        mixins: [translator],

        components: {
            MediaManager
        },

        data() {
            return {
                modal: null,
                modalOpened: false,
                selected: null
            };
        },

        mounted() {
            const that = this;

            $(() => {
                that.modal = $(that.$el);

                that.modal.on('hidden.bs.modal', () => {
                    that.modalOpened = false;
                });

                window.eventHub.$on(events.MEDIA_MODAL_BROWSER_OPEN, (data) => {
                    if (that.id === data.modalId) {
                        that.openModal();
                    }
                });

                window.eventHub.$on(events.MEDIA_ITEM_SELECTED, (media) => {
                    that.selected = (media && media.isFile()) ? media : null;
                });
            });
        },

        computed: {
            isSelected() {
                return ! this.isNotSelected;
            },

            isNotSelected() {
                return this.selected === null;
            }
        },

        methods: {
            openModal() {
                this.modalOpened = true;
                this.modal.modal('show');
            },

            closeModal() {
                this.modal.modal('hide');
            },

            selectMedia() {
                this.closeModal();

                window.eventHub.$emit(events.MEDIA_MODAL_BROWSER_SELECT, {
                    url: this.selected.url,
                    modalId: this.id
                });
            }
        }
    }
</script>

<template>
    <div class="media-browser-modal modal fade">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Media browser</h4>
                </div>
                <div class="modal-body no-padding">
                    <media-manager :readonly="true" v-if="modalOpened"></media-manager>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                        {{ lang.get('actions.close') }}
                    </button>
                    <transition name="fade">
                        <button type="button" class="btn btn-primary" v-if="isSelected" @click.prevent="selectMedia">
                            {{ lang.get('actions.select') }}
                        </button>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>
