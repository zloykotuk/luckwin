const STORAGE_KEY = 'luckwin_session';
const TTL_MS = 7 * 24 * 60 * 60 * 1000;

export type LinkSession = {
    hash: string;
    url: string;
};

type StoredSession = LinkSession & { expiresAt: number };

export function saveLinkSession(session: LinkSession): void {
    const stored: StoredSession = {
        ...session,
        expiresAt: Date.now() + TTL_MS,
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(stored));
}

export function loadLinkSession(): LinkSession | null {
    const raw = localStorage.getItem(STORAGE_KEY);

    if (!raw) {
        return null;
    }

    try {
        const stored = JSON.parse(raw) as StoredSession;

        if (stored.expiresAt <= Date.now()) {
            clearLinkSession();

            return null;
        }

        return { hash: stored.hash, url: stored.url };
    } catch {
        clearLinkSession();

        return null;
    }
}

export function clearLinkSession(): void {
    localStorage.removeItem(STORAGE_KEY);
}
