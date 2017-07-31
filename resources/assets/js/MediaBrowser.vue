<script>
    import config from './config';
    import events from './events';
    import { coerce } from './utilities';
    import { translator } from './mixins';
    import BrowseMediaModal from './Modals/BrowseMediaModal.vue';

    export default {
        name: 'media-browser',

        mixins: [translator],

        components: {
            BrowseMediaModal
        },

        props: {
            name: {
                type: String,
                required: true
            },
            type: {
                type: String,
                'default': 'text'
            },
            value: {
                type: String,
                'default': ''
            },
            className: {
                type: String,
                'default': 'form-control'
            },
            placeholder: {
                type: String,
                'default': null
            },
            disabled: {
                type: Boolean,
                coerce: coerce.boolean,
                'default': false
            },
            readonly: {
                type: Boolean,
                coerce: coerce.boolean,
                'default': false
            },
            required: {
                type: Boolean,
                coerce: coerce.boolean,
                'default': false
            }
        },

        data() {
            return {
                modalId: '',
                url: ''
            }
        },

        mounted() {
            this.modalId = 'browse-modal-'+Math.random().toString(16).slice(2);
            this.url = this.value;

            window.eventHub.$on(events.MEDIA_MODAL_BROWSER_SELECT, (data) => {
                if (this.modalId === data.modalId) {
                    this.url = data.url;
                }
            });
        },

        methods: {
            openBrowserModal() {
                window.eventHub.$emit(events.MEDIA_MODAL_BROWSER_OPEN, {
                    modalId: this.modalId
                });
            }
        }
    }
</script>

<template>
    <div>
        <div class="input-group">
            <input :name="name" :type="type" :value="url" :class="className"
                   :placeholder="placeholder" :disabled="disabled" :readonly="readonly" :required="required">

            <span class="input-group-btn">
                <button class="btn btn-default" type="button" @click.prevent="openBrowserModal()">
                    <slot>{{ lang.get('actions.browse') }}</slot>
                </button>
            </span>
        </div>

        <browse-media-modal :id="modalId"></browse-media-modal>
    </div>
</template>
