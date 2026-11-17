export function parseQuantity(value: string, stockQuantity: number): number | null {
    if (!/^[1-9]\d*$/.test(value)) return null;
    const parsed = Number(value);
    return Number.isSafeInteger(parsed) && parsed <= stockQuantity ? parsed : null;
}
