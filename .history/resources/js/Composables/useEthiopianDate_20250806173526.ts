import { ref } from 'vue';
import axios from 'axios';

interface EthiopianDate {
    year: number;
    month: number;
    day: number;
}

interface GregorianDate {
    year: number;
    month: number;
    day: number;
}

interface EthiopianCalendarDay {
    id: number;
    gregorian_date: string;
    is_holiday: boolean;
    is_working_day: boolean;
    description: string | null;
    created_at: string;
    updated_at: string;
}

export function useEthiopianDate() {
    const ethiopianCalendarDays = ref<EthiopianCalendarDay[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const convertGregorianToEthiopian = async (gregorianDate: GregorianDate): Promise<EthiopianDate | null> => {
        try {
            const response = await axios.post('/api/convert-gregorian-to-ethiopian', gregorianDate);
            return response.data;
        } catch (err: any) {
            console.error('Error converting Gregorian to Ethiopian:', err);
            return null;
        }
    };

    const convertGregorianToEthiopianApi = async (gregorianDateString: string): Promise<string | null> => {
        try {
            const response = await axios.post('/api/v1/convert-to-ethiopian', { date: gregorianDateString });
            return response.data.ethiopian_date;
        } catch (err: any) {
            console.error('Error converting Gregorian to Ethiopian via API:', err);
            return null;
        }
    };

    const convertEthiopianToGregorian = async (ethiopianDate: EthiopianDate): Promise<GregorianDate | null> => {
        try {
            const response = await axios.post('/api/convert-ethiopian-to-gregorian', ethiopianDate);
            return response.data;
        } catch (err: any) {
            console.error('Error converting Ethiopian to Gregorian:', err);
            return null;
        }
    };

    const fetchEthiopianCalendarDays = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get('/api/ethiopian-calendar-days');
            ethiopianCalendarDays.value = response.data.data; // Assuming data is nested under 'data'
        } catch (err: any) {
            error.value = 'Failed to fetch Ethiopian calendar days.';
            console.error('Error fetching Ethiopian calendar days:', err);
        } finally {
            loading.value = false;
        }
    };

    return {
        ethiopianCalendarDays,
        loading,
        error,
        convertGregorianToEthiopian,
        convertEthiopianToGregorian,
        convertGregorianToEthiopianApi,
        fetchEthiopianCalendarDays,
    };
}
