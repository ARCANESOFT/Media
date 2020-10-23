import { defineComponent, ref, computed } from 'vue'
import { MediaItem, MediaItems } from '../../types'
import { MEDIA_TYPES } from '../../enums'
import location from '../../store/modules/location'
import loading from '../../store/modules/loading'
import mediaTools from '../../store/modules/media-tools'
import mediaItems from '../../store/modules/media-items'
import selectedMediaItems from '../../store/modules/selected-media-items'
import { trans } from '../../helpers/translator'

export default defineComponent({
    name: 'v-media-move-item',

    setup() {
        const { isLoading, handleLoading } = loading()
        const { isRoot } = location()
        const { close: closeMediaTool } = mediaTools()
        const { loadItems: loadMediaItems, moveItem: moveMediaItem, directories, isMediaType } = mediaItems()
        const { items: selectedItems } = selectedMediaItems()

        const destination = ref<string | null>(null)
        const results = ref<any[]>([])

        // Computed
        const hasSelectedDestination = computed<boolean>(() => destination !== null)
        const showParentDestination = computed<boolean>(() => ! isRoot.value)
        const destinations = computed<MediaItems>(() => {
            const destinationsToExclude = selectedItems.value
                .filter((item: MediaItem) => isMediaType(item, MEDIA_TYPES.DIRECTORY))
                .map((item: MediaItem) => item.name)

            return directories.value.filter((item: MediaItem) => ! destinationsToExclude.includes(item.name));
        })

        const moveMedia = async (): Promise<void> => {
            if ( ! hasSelectedDestination.value)
                return

            return handleLoading(async (): Promise<void> => {
                const requests = selectedItems.value.map(
                    (item: MediaItem) => moveMediaItem(item.path, destination.value).then(response => {
                        results.value.push({
                            item,
                            status: response.status === 200 ? 'success' : 'failed',
                        })
                    })
                )

                await Promise.all(requests).then(() => {
                    loadMediaItems().then(() => {
                        closeMediaTool()
                    })
                })
            })
        }

        return {
            trans,
            isLoading,
            destination,
            directories,
            results,
            moveMedia,
            selectedMediaItems,
            hasSelectedDestination,
            showParentDestination,
            destinations,
        }
    },

    template: `
        <div class="bg-white p-3">
            <label for="destination">{{ trans('Select a Destination') }}</label>
            <div class="input-group mb-3">
                <select v-model="destination" id="destination" class="form-control" :readonly="isLoading">
                    <option value=".." v-if="showParentDestination">..</option>
                    <option v-for="destination in destinations"
                            :value="destination.name"
                            v-text="destination.name"></option>
                </select>
                <div class="input-group-append">
                    <button @click.prevent="moveMedia" type="button"
                            class="btn btn-primary"
                            :disabled="isLoading || ! hasSelectedDestination">
                        {{ trans(isLoading ? 'Loading...' : 'Move') }}
                    </button>
                </div>
            </div>

            <ul class="pl-3 mb-0">
                <li v-for="item in selectedMediaItems" :key="item.path">{{ item.name }}</li>
            </ul>
        </div>
    `,
})

