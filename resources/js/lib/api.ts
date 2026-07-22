export type RouteDefinition = { url: string; method: string };

export class ApiError extends Error {
    constructor(
        message: string,
        public errors: Record<string, string[]> = {},
    ) {
        super(message);
    }

    fieldError(field: string): string | undefined {
        return this.errors[field]?.[0];
    }
}

function xsrfToken(): string {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);

    return match ? decodeURIComponent(match[1]) : '';
}

/**
 * Calls a Laravel JSON endpoint and unwraps the JsonResource `{ data: ... }` envelope.
 */
export async function apiRequest<T>(
    route: RouteDefinition,
    body?: unknown,
): Promise<T> {
    const response = await fetch(route.url, {
        method: route.method.toUpperCase(),
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            ...(body !== undefined
                ? { 'Content-Type': 'application/json' }
                : {}),
            'X-XSRF-TOKEN': xsrfToken(),
        },
        body: body !== undefined ? JSON.stringify(body) : undefined,
    });

    if (!response.ok) {
        const payload = await response.json().catch(() => null);

        throw new ApiError(
            payload?.message ?? response.statusText,
            payload?.errors ?? {},
        );
    }

    if (response.status === 204) {
        return undefined as T;
    }

    const payload = await response.json();

    return payload.data as T;
}
