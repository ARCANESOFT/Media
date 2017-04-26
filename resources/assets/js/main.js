/**
 * Register Vue components...
 */

import Vue from 'vue';

if ( ! window.eventHub) {
    window.eventHub = new Vue;
}

import MediaManager from './MediaManager.vue';
import MediaBrowser from './MediaBrowser.vue';
import BrowseMediaModal from './Modals/BrowseMediaModal.vue'

const MediaManagerPlugin = {
    install(Vue, options) {
        Vue.component(MediaManager.name, MediaManager);
        Vue.component(MediaBrowser.name, MediaBrowser);
        Vue.component(BrowseMediaModal.name, BrowseMediaModal);
    }
};

export default MediaManagerPlugin;
