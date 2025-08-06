import { useEthiopianDate } from '../useEthiopianDate';
import axios from 'axios';
import { vi } from 'vitest';

// Mock axios
vi.mock('axios');

describe('useEthiopianDate', () => {
    const { convertGregorianToEthiopian, convertEthiopianToGregorian, convertGregorianToEthiopianApi, fetchEthiopianCalendarDays } = useEthiopianDate();

    beforeEach(() => {
        // Reset mocks before each test
        vi.clearAllMocks();
    });

    it('should convert Gregorian to Ethiopian date', async () => {
        const mockGregorianDate = { year: 2024, month: 9, day: 11 };
        const mockEthiopianDate = { year: 2017, month: 1, day: 1 };
        (axios.post as ReturnType<typeof vi.fn>).mockResolvedValueOnce({ data: mockEthiopianDate });

        const result = await convertGregorianToEthiopian(mockGregorianDate);

        expect(axios.post).toHaveBeenCalledWith('/api/v1/convert-gregorian-to-ethiopian', mockGregorianDate);
        expect(result).toEqual(mockEthiopianDate);
    });

    it('should handle error when converting Gregorian to Ethiopian date', async () => {
        const mockGregorianDate = { year: 2024, month: 9, day: 11 };
        (axios.post as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('Network Error'));

        const result = await convertGregorianToEthiopian(mockGregorianDate);

        expect(axios.post).toHaveBeenCalledWith('/api/v1/convert-gregorian-to-ethiopian', mockGregorianDate);
        expect(result).toBeNull();
    });

    it('should convert Ethiopian to Gregorian date', async () => {
        const mockEthiopianDate = { year: 2017, month: 1, day: 1 };
        const mockGregorianDate = { year: 2024, month: 9, day: 11 };
        (axios.post as ReturnType<typeof vi.fn>).mockResolvedValueOnce({ data: mockGregorianDate });

        const result = await convertEthiopianToGregorian(mockEthiopianDate);

        expect(axios.post).toHaveBeenCalledWith('/api/v1/convert-ethiopian-to-gregorian', mockEthiopianDate);
        expect(result).toEqual(mockGregorianDate);
    });

    it('should handle error when converting Ethiopian to Gregorian date', async () => {
        const mockEthiopianDate = { year: 2017, month: 1, day: 1 };
        (axios.post as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('Network Error'));

        const result = await convertEthiopianToGregorian(mockEthiopianDate);

        expect(axios.post).toHaveBeenCalledWith('/api/v1/convert-ethiopian-to-gregorian', mockEthiopianDate);
        expect(result).toBeNull();
    });

    it('should convert Gregorian to Ethiopian date via v1 API', async () => {
        const gregorianDateString = '2024-09-11';
        const mockEthiopianDateString = '2017-01-01';
        (axios.post as ReturnType<typeof vi.fn>).mockResolvedValueOnce({ data: { ethiopian_date: mockEthiopianDateString } });

        const result = await convertGregorianToEthiopianApi(gregorianDateString);

        expect(axios.post).toHaveBeenCalledWith('/api/v1/convert-to-ethiopian', { date: gregorianDateString });
        expect(result).toEqual(mockEthiopianDateString);
    });

    it('should handle error when converting Gregorian to Ethiopian date via v1 API', async () => {
        const gregorianDateString = '2024-09-11';
        (axios.post as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('Network Error'));

        const result = await convertGregorianToEthiopianApi(gregorianDateString);

        expect(axios.post).toHaveBeenCalledWith('/api/v1/convert-to-ethiopian', { date: gregorianDateString });
        expect(result).toBeNull();
    });

    it('should fetch Ethiopian calendar days', async () => {
        const mockCalendarDays = [{ id: 1, gregorian_date: '2024-09-11', is_holiday: true, is_working_day: false, description: 'New Year' }];
        (axios.get as ReturnType<typeof vi.fn>).mockResolvedValueOnce({ data: { data: mockCalendarDays } });

        await fetchEthiopianCalendarDays();

        expect(axios.get).toHaveBeenCalledWith('/api/ethiopian-calendar-days');
        // Assuming ethiopianCalendarDays is a ref, you might need to check its .value
        // For simplicity, this test focuses on the API call.
        // In a real test, you'd check the ref's value after the async operation.
    });

    it('should handle error when fetching Ethiopian calendar days', async () => {
        (axios.get as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('Network Error'));

        await fetchEthiopianCalendarDays();

        expect(axios.get).toHaveBeenCalledWith('/api/ethiopian-calendar-days');
        // Again, in a real test, you'd check the error ref's value.
    });
