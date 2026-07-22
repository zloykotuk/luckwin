import { router } from '@inertiajs/vue3';
import { defineStore } from 'pinia';
import RegisterAction from '@/actions/App/Http/Controllers/RegisterAction';
import { apiRequest, ApiError } from '@/lib/api';
import { saveLinkSession } from '@/lib/linkSession';
import { phonenumberError, usernameError } from '@/lib/validators';

type LinkPayload = { hash: string; url: string };

export const useRegistrationStore = defineStore('registration', {
    state: () => ({
        username: '',
        phonenumber: '',
        usernameTouched: false,
        phonenumberTouched: false,
        submitting: false,
        serverError: null as string | null,
    }),
    getters: {
        usernameError: (state) =>
            state.usernameTouched ? usernameError(state.username) : null,
        phonenumberError: (state) =>
            state.phonenumberTouched
                ? phonenumberError(state.phonenumber)
                : null,
    },
    actions: {
        setUsername(value: string) {
            this.username = value;
            this.serverError = null;
        },
        setPhonenumber(value: string) {
            this.phonenumber = value;
            this.serverError = null;
        },
        touchUsername() {
            this.usernameTouched = true;
        },
        touchPhonenumber() {
            this.phonenumberTouched = true;
        },
        async submit() {
            this.usernameTouched = true;
            this.phonenumberTouched = true;
            this.serverError = null;

            if (
                usernameError(this.username) ||
                phonenumberError(this.phonenumber)
            ) {
                return;
            }

            this.submitting = true;

            try {
                const link = await apiRequest<LinkPayload>(RegisterAction(), {
                    username: this.username,
                    phonenumber: this.phonenumber,
                });

                saveLinkSession(link);
                router.visit(link.url);
            } catch (error) {
                this.serverError =
                    error instanceof ApiError
                        ? (error.fieldError('username') ??
                          error.fieldError('phonenumber') ??
                          error.message)
                        : 'Something went wrong. Please try again.';
            } finally {
                this.submitting = false;
            }
        },
    },
});
