export function quotesIndex() {
    return route('quotes.index');
}

export function quotesStore() {
    return route('quotes.store');
}

export function quotesUpdate(id: number | string) {
    return route('quotes.update', id);
}

export function quotesDestroy(id: number | string) {
    return route('quotes.destroy', id);
}

export function quotesRestore(id: number | string) {
    return route('quotes.restore', id);
}
