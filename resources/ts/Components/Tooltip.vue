<script setup lang="ts">
import { ref, reactive, nextTick } from "vue";

defineProps<{
    message: string;
}>();

const isVisible = ref(false);
const triggerRef = ref<HTMLDivElement | null>(null);
const tooltipStyle = reactive({
    top: "0px",
    left: "0px",
});

const showTooltip = async () => {
    isVisible.value = true;
    await nextTick(); // Wait for tooltip to render in DOM

    if (triggerRef.value) {
        const rect = triggerRef.value.getBoundingClientRect();

        // Position tooltip right above the button, centered horizontally
        tooltipStyle.top = `${rect.top + window.scrollY - 8}px`;
        tooltipStyle.left = `${rect.left + window.scrollX + rect.width / 2}px`;
    }
};

const hideTooltip = () => {
    isVisible.value = false;
};
</script>

<template>
    <div
        class="tooltip-wrapper"
        ref="triggerRef"
        @mouseenter="showTooltip"
        @mouseleave="hideTooltip"
    >
        <!-- The button or element being hovered -->
        <slot></slot>

        <!-- Teleport moves this directly under <body> when active -->
        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="isVisible"
                    class="tooltip-content"
                    :style="tooltipStyle"
                >
                    {{ message }}
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.tooltip-wrapper {
    display: inline-block;
}

.tooltip-content {
    position: absolute;
    transform: translate(-50%, -100%);
    background-color: #333;
    color: #fff;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    z-index: 9999;
    white-space: nowrap;
    pointer-events: none;
}

/* 1. Define the transition timing */
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.5s ease,
        transform 0.5s ease;
}

/* 2. Define the starting state for entering & ending state for leaving */
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    /* Adds a subtle lifting effect as it fades in */
    transform: translate(-50%, -65%);
}
</style>
