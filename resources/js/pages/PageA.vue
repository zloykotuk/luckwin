<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { useLinkStore } from '@/stores/link';

const props = defineProps<{ hash: string; url: string }>();

const store = useLinkStore();

onMounted(() => {
    store.init(props.hash, props.url);
});

const resultLabel = computed(() =>
    store.luckyResult?.result ? 'You win!' : 'No luck',
);
</script>

<template>
    <Head title="Your lucky link" />

    <div
        class="flex min-h-screen flex-col items-center justify-center gap-6 bg-canvas px-5 py-8"
    >
        <div class="animate-pop-in flex w-full max-w-[520px] flex-col gap-4">
            <!-- link card -->
            <div
                class="card-shadow rounded-[22px] border border-border bg-surface p-[26px]"
            >
                <div class="mb-3.5 flex items-baseline justify-between">
                    <h1
                        class="font-display text-xl font-bold tracking-tight text-ink"
                    >
                        Your lucky link
                    </h1>
                    <span
                        class="rounded-full bg-brand-bg px-2.5 py-1 text-xs font-semibold text-brand"
                        >Active</span
                    >
                </div>
                <div class="flex items-stretch gap-2">
                    <div
                        class="min-w-0 flex-1 truncate rounded-xl border border-border bg-canvas px-3.5 py-3 font-mono text-[13px] text-ink-muted"
                    >
                        {{ store.url }}
                    </div>
                    <button
                        type="button"
                        class="shrink-0 rounded-xl border border-border bg-white px-4 text-[13px] font-semibold text-ink"
                        @click="store.copyLink()"
                    >
                        Copy
                    </button>
                </div>
                <div class="mt-4 flex gap-2.5">
                    <button
                        type="button"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-border bg-white py-2.5 text-[13.5px] font-semibold text-ink disabled:opacity-60"
                        :disabled="store.regenerating"
                        @click="store.regenerateLink()"
                    >
                        ↻ Regenerate
                    </button>
                    <button
                        type="button"
                        class="deactivate-btn flex-1 rounded-xl py-2.5 text-[13.5px] font-semibold disabled:opacity-60"
                        :disabled="store.deactivating"
                        @click="store.deactivateLink()"
                    >
                        Deactivate
                    </button>
                </div>
            </div>

            <!-- lucky card -->
            <div
                class="lucky-card relative overflow-hidden rounded-[22px] px-[26px] py-7 text-white"
            >
                <h2
                    class="mb-1 font-display text-[19px] font-bold tracking-tight"
                >
                    Feeling lucky?
                </h2>
                <p class="mb-5 text-[13.5px] text-white/70">
                    One tap. Win or lose. See what fate gives you.
                </p>

                <div
                    v-if="store.luckyResult"
                    class="result-card animate-pop-in rounded-2xl p-4"
                    :class="
                        store.luckyResult.result
                            ? 'result-card-win'
                            : 'result-card-lose'
                    "
                >
                    <div class="mb-1.5 flex items-center gap-2.5">
                        <span
                            class="h-2.5 w-2.5 rounded-full"
                            :class="
                                store.luckyResult.result
                                    ? 'bg-[oklch(0.72_0.16_145)]'
                                    : 'bg-[oklch(0.68_0.16_25)]'
                            "
                        ></span>
                        <span
                            class="font-display text-[22px] font-bold tracking-tight"
                            >{{ resultLabel }}</span
                        >
                    </div>
                    <div class="text-[13.5px] text-white/70">
                        Rolled
                        <b class="font-mono text-white/90">{{
                            store.luckyResult.roll
                        }}</b>
                        · Payout
                        <b
                            class="font-mono"
                            :class="
                                store.luckyResult.result
                                    ? 'text-[oklch(0.72_0.16_145)]'
                                    : 'text-white/70'
                            "
                            >${{ store.luckyResult.payout.toFixed(2) }}</b
                        >
                    </div>
                </div>

                <button
                    type="button"
                    class="lucky-btn mt-[18px] flex w-full items-center justify-center gap-2 rounded-2xl py-4 font-display text-base font-bold disabled:opacity-60"
                    :disabled="store.playing"
                    @click="store.playLucky()"
                >
                    <span class="animate-coin-spin inline-block">🪙</span> I'm
                    feeling lucky
                </button>
            </div>

            <!-- history -->
            <div
                class="card-shadow rounded-[22px] border border-border bg-surface px-6 py-[22px]"
            >
                <button
                    type="button"
                    class="flex w-full items-center justify-between bg-transparent"
                    @click="store.toggleHistory()"
                >
                    <span class="font-display text-[17px] font-bold text-ink"
                        >History</span
                    >
                    <span
                        class="flex items-center gap-1.5 text-[13px] text-ink-muted"
                    >
                        last 3 results
                        <span
                            class="inline-block transition-transform"
                            :class="{ 'rotate-180': store.showHistory }"
                            >▾</span
                        >
                    </span>
                </button>

                <div v-if="store.showHistory" class="mt-4 flex flex-col gap-2">
                    <p
                        v-if="store.historyLoading"
                        class="py-3.5 text-center text-[13.5px] text-ink-soft"
                    >
                        Loading…
                    </p>
                    <p
                        v-else-if="store.historyError"
                        class="py-3.5 text-center text-[13.5px] text-error"
                    >
                        Couldn't load history — try again.
                    </p>
                    <template v-else-if="store.history.length">
                        <div
                            v-for="(entry, index) in store.history"
                            :key="index"
                            class="flex items-center justify-between rounded-xl bg-canvas px-3.5 py-3"
                        >
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :class="
                                        entry.result
                                            ? 'bg-[oklch(0.62_0.15_145)]'
                                            : 'bg-[oklch(0.62_0.16_25)]'
                                    "
                                ></span>
                                <span class="text-[14px] font-semibold">{{
                                    entry.result ? 'Win' : 'Lose'
                                }}</span>
                            </div>
                            <span
                                class="font-mono text-[13.5px] font-medium"
                                :class="
                                    entry.result
                                        ? 'text-[oklch(0.5_0.13_145)]'
                                        : 'text-ink-muted'
                                "
                            >
                                ${{ entry.payout.toFixed(2) }}
                            </span>
                        </div>
                    </template>
                    <p
                        v-else
                        class="py-3.5 text-center text-[13.5px] text-ink-soft"
                    >
                        No plays yet — hit the lucky button.
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="store.toast"
            class="toast animate-pop-in fixed bottom-6 left-1/2 -translate-x-1/2 rounded-full px-5 py-2.5 text-[13.5px] font-medium text-white"
        >
            {{ store.toast }}
        </div>
    </div>
</template>

<style scoped>
.card-shadow {
    box-shadow: 0 24px 60px -34px oklch(0.4 0.02 260 / 0.35);
}

.deactivate-btn {
    border: 1px solid oklch(0.86 0.04 25);
    background: oklch(0.99 0.008 25);
    color: oklch(0.53 0.17 25);
}

.lucky-card {
    background: linear-gradient(
        160deg,
        var(--color-navy-start) 0%,
        var(--color-navy-end) 100%
    );
    box-shadow: 0 24px 60px -30px oklch(0.3 0.05 260 / 0.6);
}

.result-card {
    border: 1px solid;
}

.result-card-win {
    background: oklch(0.3 0.02 260 / 0.5);
    border-color: oklch(0.5 0.12 145 / 0.5);
}

.result-card-lose {
    background: oklch(0.3 0.02 260 / 0.5);
    border-color: oklch(0.5 0.12 25 / 0.4);
}

.lucky-btn {
    border: none;
    background: var(--color-gold);
    color: oklch(0.22 0.04 85);
    box-shadow: 0 8px 24px -8px oklch(0.72 0.16 85 / 0.6);
}

.toast {
    background: var(--color-ink);
    box-shadow: 0 12px 30px -10px oklch(0.2 0.02 260 / 0.5);
    z-index: 50;
}
</style>
