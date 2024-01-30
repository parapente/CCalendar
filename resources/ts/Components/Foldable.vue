<script setup lang="ts">
import { faCaretDown, faCaretRight } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { ref } from "vue";

const props = defineProps<{
    closed?: boolean;
}>();

const isClosed = ref(props.closed ?? false);
</script>

<template>
    <div class="flex flex-col">
        <div
            class="flex items-center gap-2 cursor-pointer"
            @click="isClosed = isClosed ? false : true"
        >
            <FontAwesomeIcon :icon="isClosed ? faCaretRight : faCaretDown" />
            <div>
                <slot name="title" />
            </div>
        </div>
        <Transition>
            <div v-if="!isClosed">
                <slot />
            </div>
        </Transition>
    </div>
</template>

<style type="text/css">
.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
