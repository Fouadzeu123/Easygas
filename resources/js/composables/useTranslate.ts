import { usePage } from '@inertiajs/vue3';

export function useTranslate() {
    const page = usePage();

    const __ = (key: string, replacements: Record<string, string> = {}) => {
        const translations = (page.props.translations as Record<string, any>) || {};
        
        // Support dot notation (e.g., 'Status.Pending')
        let translation = key.split('.').reduce((obj, i) => (obj ? obj[i] : null), translations) as any as string | null;
        
        if (!translation) {
            translation = key;
        }

        Object.keys(replacements).forEach((r) => {
            translation = (translation as string).replace(`:${r}`, replacements[r]);
        });

        return translation;
    };

    return { __ };
}
