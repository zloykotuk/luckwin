<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { clearLinkSession, loadLinkSession } from '@/lib/linkSession';
import { useRegistrationStore } from '@/stores/registration';

const store = useRegistrationStore();
const redirecting = ref(false);

onMounted(() => {
    const session = loadLinkSession();

    if (!session) {
        store.$reset();

        return;
    }

    redirecting.value = true;
    router.visit(session.url, {
        onHttpException: () => {
            clearLinkSession();
            redirecting.value = false;
            store.$reset();

            return false;
        },
    });
});

function onUsernameInput(event: Event) {
    store.setUsername((event.target as HTMLInputElement).value);
}

function onPhoneInput(event: Event) {
    store.setPhonenumber((event.target as HTMLInputElement).value);
}
</script>

<template>
    <Head title="Create your link" />

    <div
        v-if="!redirecting"
        class="flex min-h-screen flex-col items-center justify-center gap-6 bg-canvas px-5 py-8"
    >
        <div class="flex items-center gap-2.5">
            <div
                class="brand-logo-shadow flex h-[30px] w-[30px] items-center justify-center rounded-[9px] bg-brand"
            >
                <div
                    class="h-3 w-3 rounded-full border-[3px] border-white"
                ></div>
            </div>
            <span
                class="font-display text-[19px] font-bold tracking-tight text-ink"
                >Luckwin</span
            >
        </div>

        <div
            class="card-shadow animate-pop-in w-full max-w-[440px] rounded-[22px] border border-border bg-surface px-[30px] pt-[34px] pb-[30px]"
        >
            <h1
                class="mb-1.5 font-display text-[26px] font-bold tracking-tight text-ink"
            >
                Create your link
            </h1>
            <p class="mb-6 text-[14.5px] leading-relaxed text-ink-muted">
                Register to generate a unique, shareable lucky link.
            </p>

            <label
                for="username"
                class="mb-1.5 block text-[13px] font-semibold text-ink"
                >Username</label
            >
            <input
                id="username"
                type="text"
                :value="store.username"
                placeholder="letters and digits only"
                maxlength="50"
                class="field-input"
                :class="
                    store.usernameError ? 'field-input-error' : 'border-border'
                "
                @input="onUsernameInput"
                @blur="store.touchUsername()"
            />
            <p
                v-if="store.usernameError"
                class="mt-1.5 flex items-center gap-1.5 text-[12.5px] text-error"
            >
                <span class="font-bold">!</span>{{ store.usernameError }}
            </p>

            <div class="h-[18px]"></div>

            <label
                for="phonenumber"
                class="mb-1.5 block text-[13px] font-semibold text-ink"
                >Phone number</label
            >
            <input
                id="phonenumber"
                type="text"
                :value="store.phonenumber"
                placeholder="+1 555 123 4567"
                maxlength="20"
                class="field-input"
                :class="
                    store.phonenumberError
                        ? 'field-input-error'
                        : 'border-border'
                "
                @input="onPhoneInput"
                @blur="store.touchPhonenumber()"
            />
            <p
                v-if="store.phonenumberError"
                class="mt-1.5 flex items-center gap-1.5 text-[12.5px] text-error"
            >
                <span class="font-bold">!</span>{{ store.phonenumberError }}
            </p>
            <p
                v-if="store.serverError"
                class="mt-1.5 flex items-center gap-1.5 text-[12.5px] text-error"
            >
                <span class="font-bold">!</span>{{ store.serverError }}
            </p>

            <button
                type="button"
                class="register-btn-shadow mt-6 w-full rounded-2xl bg-brand py-3.5 font-display text-base font-bold text-white disabled:opacity-60"
                :disabled="store.submitting"
                @click="store.submit()"
            >
                {{ store.submitting ? 'Registering…' : 'Register' }}
            </button>
        </div>

        <p
            class="max-w-[440px] text-center text-[12.5px] leading-relaxed text-ink-soft"
        >
            Your session is remembered for 7 days — reopen and you'll land
            straight on your link.
        </p>
    </div>
</template>

<style scoped>
.field-input {
    width: 100%;
    border-radius: 0.75rem;
    border-width: 1.5px;
    background: white;
    padding: 0.75rem 0.875rem;
    font-size: 15px;
    outline: none;
    transition:
        border-color 0.15s,
        box-shadow 0.15s;
}

.field-input-error {
    border-color: oklch(0.7 0.15 25);
    box-shadow: 0 0 0 3px oklch(0.7 0.15 25 / 0.12);
}

.brand-logo-shadow {
    box-shadow: 0 4px 14px oklch(0.62 0.15 152 / 0.35);
}

.card-shadow {
    box-shadow: 0 24px 60px -30px oklch(0.4 0.02 260 / 0.35);
}

.register-btn-shadow {
    box-shadow: 0 10px 26px -10px oklch(0.62 0.15 152 / 0.6);
}
</style>
