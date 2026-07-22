import { router } from '@inertiajs/vue3';
import { defineStore } from 'pinia';
import {
    destroy,
    regenerate,
} from '@/actions/App/Http/Controllers/LinkController';
import PlayLuckyAction from '@/actions/App/Http/Controllers/PlayLuckyAction';
import ShowHistoryAction from '@/actions/App/Http/Controllers/ShowHistoryAction';
import { apiRequest } from '@/lib/api';
import { clearLinkSession, saveLinkSession } from '@/lib/linkSession';
import { home } from '@/routes';

type LinkPayload = { hash: string; url: string };
type LuckyResult = { roll: number; result: boolean; payout: number };
type HistoryEntry = { result: boolean; payout: number };

export const useLinkStore = defineStore('link', {
    state: () => ({
        hash: '',
        url: '',
        regenerating: false,
        deactivating: false,
        playing: false,
        luckyResult: null as LuckyResult | null,
        showHistory: false,
        historyLoading: false,
        historyLoaded: false,
        historyError: false,
        history: [] as HistoryEntry[],
        toast: '',
    }),
    actions: {
        init(hash: string, url: string) {
            this.hash = hash;
            this.url = url;
            this.luckyResult = null;
            this.showHistory = false;
            this.historyLoading = false;
            this.historyLoaded = false;
            this.historyError = false;
            this.history = [];
            this.toast = '';
            saveLinkSession({ hash, url });
        },
        flash(message: string) {
            this.toast = message;
            window.setTimeout(() => {
                this.toast = '';
            }, 1800);
        },
        async copyLink() {
            try {
                await navigator.clipboard.writeText(this.url);
                this.flash('Copied');
            } catch {
                this.flash('Could not copy the link');
            }
        },
        async regenerateLink() {
            this.regenerating = true;

            try {
                const link = await apiRequest<LinkPayload>(
                    regenerate(this.hash),
                );
                this.hash = link.hash;
                this.url = link.url;
                saveLinkSession(link);
                window.history.replaceState(null, '', link.url);
                this.flash('Link regenerated');
            } catch {
                this.flash('Could not regenerate the link');
            } finally {
                this.regenerating = false;
            }
        },
        async deactivateLink() {
            this.deactivating = true;

            try {
                await apiRequest<void>(destroy(this.hash));
                clearLinkSession();
                router.visit(home.url());
            } catch {
                this.flash('Could not deactivate the link');
            } finally {
                this.deactivating = false;
            }
        },
        async playLucky() {
            this.playing = true;

            try {
                const outcome = await apiRequest<LuckyResult>(
                    PlayLuckyAction(this.hash),
                );
                this.luckyResult = outcome;

                if (this.historyLoaded) {
                    this.history = [
                        { result: outcome.result, payout: outcome.payout },
                        ...this.history,
                    ].slice(0, 3);
                }
            } catch {
                this.flash('Could not play right now');
            } finally {
                this.playing = false;
            }
        },
        async toggleHistory() {
            this.showHistory = !this.showHistory;

            if (!this.showHistory || this.historyLoaded) {
                return;
            }

            this.historyLoading = true;
            this.historyError = false;

            try {
                this.history = await apiRequest<HistoryEntry[]>(
                    ShowHistoryAction(this.hash),
                );
                this.historyLoaded = true;
            } catch {
                this.historyError = true;
            } finally {
                this.historyLoading = false;
            }
        },
    },
});
