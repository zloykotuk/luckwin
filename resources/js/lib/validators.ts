export function usernameError(value: string): string | null {
    if (!value) {
        return 'Username is required.';
    }

    if (!/^[A-Za-z0-9]+$/.test(value)) {
        return 'Only English letters and digits are allowed.';
    }

    if (value.length > 50) {
        return 'Max 50 characters.';
    }

    return null;
}

export function phonenumberError(value: string): string | null {
    if (!value) {
        return 'Phone number is required.';
    }

    const digits = value.replace(/\D/g, '');

    if (digits.length < 8 || digits.length > 15) {
        return 'Enter a valid international phone number.';
    }

    if (!/^[+]?[\d\s().-]+$/.test(value)) {
        return 'Invalid phone format.';
    }

    return null;
}
