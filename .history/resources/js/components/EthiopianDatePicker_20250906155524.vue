<template>
  <div class="relative" ref="datePickerRef">
    <input
      type="text"
      :value="formattedEthiopianDate"
      @focus="showCalendar = true"
      @input="handleEthiopianDateInput"
      placeholder="YYYY-MM-DD"
      class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 pr-10"
    />
    <button
      v-if="ethiopianDateInternal"
      @click="clearDate"
      class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
      aria-label="Clear date"
    >
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
    <div v-if="showCalendar" class="absolute z-10 bg-white border border-gray-300 rounded-lg shadow-lg mt-1 p-4">
      <div class="flex justify-between items-center mb-2">
        <button @click="prevMonth" class="px-2 py-1 rounded hover:bg-gray-200"><</button>
        <select v-model="currentEthiopianMonth" class="mx-2 p-1 border rounded">
          <option v-for="(month, index) in ethiopianMonths" :key="index" :value="index + 1">{{ month }}</option>
        </select>
        <select v-model="currentEthiopianYear" class="mx-2 p-1 border rounded">
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
        <button @click="nextMonth" class="px-2 py-1 rounded hover:bg-gray-200">></button>
      </div>
      <div class="grid grid-cols-7 text-center text-xs font-medium text-gray-500 mb-1">
        <span v-for="day in weekDays" :key="day">{{ day }}</span>
      </div>
      <div class="grid grid-cols-7 gap-1 text-sm">
        <span
          v-for="(day, index) in calendarDays"
          :key="index"
          class="p-1 text-center rounded cursor-pointer"
          :class="{
            'text-blue-300-400': !day.date,
            'hover:bg-cyan-100': day.date,
            'bg-cyan-500 text-white': isSelected(day),
            'font-bold': isToday(day),
          }"
          @click="selectDay(day)"
        >
          {{ day.date ? day.date : '' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useEthiopianDate } from '@/composables/useEthiopianDate';

const props = defineProps({
  modelValue: String, // Gregorian date string (YYYY-MM-DD)
});

const emit = defineEmits(['update:modelValue', 'update:ethiopianDate']);

const showCalendar = ref(false);
const ethiopianDateInternal = ref(''); // Stores Ethiopian date as YYYY-MM-DD
const { convertGregorianToEthiopian, convertEthiopianToGregorian } = useEthiopianDate();

const currentEthiopianYear = ref(null);
const currentEthiopianMonth = ref(null);

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const ethiopianMonths = [
  'Meskerem', 'Tekemt', 'Hidar', 'Tahsas', 'Ter', 'Yekatit',
  'Megabit', 'Miazia', 'Genbot', 'Sene', 'Hamle', 'Nehase', 'Pagume'
];

const formattedEthiopianDate = computed(() => {
  if (!ethiopianDateInternal.value) return '';
  const [y, m, d] = ethiopianDateInternal.value.split('-').map(Number);
  return `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
});

const currentMonthName = computed(() => {
  return currentEthiopianMonth.value ? ethiopianMonths[currentEthiopianMonth.value - 1] : '';
});

const availableYears = computed(() => {
  const currentYear = currentEthiopianYear.value || new Date().getFullYear() - 7; // Fallback if not yet initialized
  const years = [];
  for (let i = currentYear - 10; i <= currentYear + 10; i++) {
    years.push(i);
  }
  return years;
});

const calendarDays = ref([]);

const updateCalendarDays = async () => {
  try {
    if (currentEthiopianYear.value === null || currentEthiopianMonth.value === null) {
      calendarDays.value = [];
      return;
    }

    const days = [];
    // Get the first day of the current Ethiopian month
    const firstDayOfEthMonthGregorian = await convertEthiopianToGregorian({ year: currentEthiopianYear.value, month: currentEthiopianMonth.value, day: 1 });
    if (!firstDayOfEthMonthGregorian) {
      calendarDays.value = [];
      return;
    }
    const firstDayDate = new Date(firstDayOfEthMonthGregorian.year, firstDayOfEthMonthGregorian.month - 1, firstDayOfEthMonthGregorian.day);

    // Calculate the day of the week (0 for Sunday, 1 for Monday, etc.)
    // Adjust to make Monday the first day of the week (0-indexed)
    let startDay = firstDayDate.getDay();
    startDay = startDay === 0 ? 6 : startDay - 1; // Convert Sunday (0) to 6, Monday (1) to 0, etc.

    // Add leading empty cells for days before the 1st of the month
    for (let i = 0; i < startDay; i++) {
      days.push({ date: null });
    }

    // Determine days in current Ethiopian month
    let daysInEthMonth;
    if (currentEthiopianMonth.value === 13) { // Pagume
      // Check for leap year in Ethiopian calendar
      const nextYearFirstMonthFirstDayGregorian = await convertEthiopianToGregorian({ year: currentEthiopianYear.value + 1, month: 1, day: 1 });
      if (!nextYearFirstMonthFirstDayGregorian) {
        calendarDays.value = [];
        return;
      }
      const nextYearFirstMonthFirstDayDate = new Date(nextYearFirstMonthFirstDayGregorian.year, nextYearFirstMonthFirstDayGregorian.month - 1, nextYearFirstMonthFirstDayGregorian.day);
      const currentYearLastDayGregorian = new Date(nextYearFirstMonthFirstDayDate.setDate(nextYearFirstMonthFirstDayDate.getDate() - 1));
      const currentYearLastDayEthiopian = await convertGregorianToEthiopian({ year: currentYearLastDayGregorian.getFullYear(), month: currentYearLastDayGregorian.getMonth() + 1, day: currentYearLastDayGregorian.getDate() });
      if (!currentYearLastDayEthiopian) {
        calendarDays.value = [];
        return;
      }
      daysInEthMonth = currentYearLastDayEthiopian.day; // The day of Pagume
    } else {
      // For regular months, it's always 30 days
      daysInEthMonth = 30;
    }

    // Add the actual days of the month
    for (let i = 1; i <= daysInEthMonth; i++) {
      days.push({ date: i, month: currentEthiopianMonth.value, year: currentEthiopianYear.value });
    }
    calendarDays.value = days;
  } catch (error) {
    console.error('Error in updateCalendarDays:', error);
    // It's crucial to prevent a full page reload here. If an error occurs during date conversion,
    // we should handle it gracefully without crashing the component or triggering a full Inertia visit.
    // For now, we'll just log the error. If the component relies on these conversions to render,
    // it might display an incomplete calendar, but it won't reload the page.
  }
};

watch([currentEthiopianYear, currentEthiopianMonth], updateCalendarDays, { immediate: true });



async function selectDay(day) {
  if (day.date) {
    ethiopianDateInternal.value = `${day.year}-${day.month}-${day.date}`;
    showCalendar.value = false;
    // Convert selected Ethiopian date to Gregorian and emit
    const gregorian = await convertEthiopianToGregorian({ year: day.year, month: day.month, day: day.date });
    if (gregorian) {
      const gregorianFormatted = `${gregorian.year}-${String(gregorian.month).padStart(2, '0')}-${String(gregorian.day).padStart(2, '0')}`;
      emit('update:modelValue', gregorianFormatted);
      emit('update:ethiopianDate', ethiopianDateInternal.value);
    } else {
      console.error('Failed to convert Ethiopian date to Gregorian.');
    }
  }
}

function isSelected(day) {
  if (!ethiopianDateInternal.value || !day.date) return false;
  const [y, m, d] = ethiopianDateInternal.value.split('-').map(Number);
  return day.year === y && day.month === m && day.date === d;
}

async function isToday(day) {
  if (!day.date) return false;
  const todayGregorian = new Date();
  const todayEthiopian = await convertGregorianToEthiopian({ year: todayGregorian.getFullYear(), month: todayGregorian.getMonth() + 1, day: todayGregorian.getDate() });
  if (!todayEthiopian) return false;
  return day.year === todayEthiopian.year && day.month === todayEthiopian.month && day.date === todayEthiopian.day;
}

async function handleEthiopianDateInput(event) {
  const value = event.target.value;
  ethiopianDateInternal.value = value;
  // Attempt to convert and emit if the format is valid
  const dateParts = value.split('-').map(Number);
  if (dateParts.length === 3 && !isNaN(dateParts[0]) && !isNaN(dateParts[1]) && !isNaN(dateParts[2])) {
    try {
      const gregorian = await convertEthiopianToGregorian({ year: dateParts[0], month: dateParts[1], day: dateParts[2] });
      if (gregorian) {
        const gregorianFormatted = `${gregorian.year}-${String(gregorian.month).padStart(2, '0')}-${String(gregorian.day).padStart(2, '0')}`;
        emit('update:modelValue', gregorianFormatted);
        emit('update:ethiopianDate', value);
      } else {
        emit('update:modelValue', '');
        emit('update:ethiopianDate', value);
        console.error('Failed to convert Ethiopian date to Gregorian from input.');
      }
    } catch (e) {
      // Invalid Ethiopian date, do not update Gregorian
      emit('update:modelValue', '');
      emit('update:ethiopianDate', value);
    }
  } else {
      emit('update:modelValue', '');
      emit('update:ethiopianDate', null); // Emit null if input is invalid/empty
      console.log('Emitting null ethiopianDate from input due to invalid format or empty value', value);
  }
}

watch(ethiopianDateInternal, (newVal) => {
  if (newVal === '') {
    emit('update:ethiopianDate', null);
  }
});

const datePickerRef = ref(null);

const handleClickOutside = (event) => {
  if (datePickerRef.value && !datePickerRef.value.contains(event.target)) {
    showCalendar.value = false;
  }
};

// Initialize calendar with current date or modelValue
  onMounted(async () => {
    // Set initial year and month to a reasonable default (e.g., current Gregorian year - 7, month 1)
    const todayGregorian = new Date();
    let initialEthiopianYear = todayGregorian.getFullYear() - 7; // Approximate Ethiopian year
    let initialEthiopianMonth = 1; // Meskerem

    // Try to get today's Ethiopian date for initial display if no modelValue
    const todayEthiopian = await convertGregorianToEthiopian({
      year: todayGregorian.getFullYear(),
      month: todayGregorian.getMonth() + 1,
      day: todayGregorian.getDate()
    });

    if (todayEthiopian) {
      initialEthiopianYear = todayEthiopian.year;
      initialEthiopianMonth = todayEthiopian.month;
    } else {
      console.warn('Failed to get today\'s Ethiopian date, using approximate default.');
    }

    currentEthiopianYear.value = initialEthiopianYear;
    currentEthiopianMonth.value = initialEthiopianMonth;

    // If modelValue is provided, convert it and set the internal date
    if (props.modelValue) {
      const [gregYear, gregMonth, gregDay] = props.modelValue.split('-').map(Number);
      const ethFromModel = await convertGregorianToEthiopian({ year: gregYear, month: gregMonth, day: gregDay });
      if (ethFromModel) {
        ethiopianDateInternal.value = `${ethFromModel.year}-${ethFromModel.month}-${ethFromModel.day}`;
        currentEthiopianYear.value = ethFromModel.year;
        currentEthiopianMonth.value = ethFromModel.month;
      } else {
        ethiopianDateInternal.value = '';
        console.error('Failed to convert modelValue Gregorian date to Ethiopian.');
      }
    } else if (todayEthiopian) {
      // If no modelValue, and today's Ethiopian date was successfully obtained, use it
      ethiopianDateInternal.value = `${todayEthiopian.year}-${todayEthiopian.month}-${todayEthiopian.day}`;
    } else {
      // Fallback if neither modelValue nor today's Ethiopian date could be set
      ethiopianDateInternal.value = `${initialEthiopianYear}-${initialEthiopianMonth}-1`; // Default to 1st day of default month
    }

    document.addEventListener('click', handleClickOutside);
  });

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Watch for external changes to modelValue (Gregorian date)
watch(() => props.modelValue, async (newVal) => {
  if (newVal) {
    const [y, m, d] = newVal.split('-').map(Number);
    const eth = await convertGregorianToEthiopian({ year: y, month: m, day: d });
    if (eth) {
      ethiopianDateInternal.value = `${eth.year}-${eth.month}-${eth.day}`;
      currentEthiopianYear.value = eth.year;
      currentEthiopianMonth.value = eth.month;
    }
  } else {
    ethiopianDateInternal.value = '';
  }
});

function clearDate() {
  ethiopianDateInternal.value = '';
  emit('update:modelValue', null);
  emit('update:ethiopianDate', null);
}
</script>

<style scoped>
.relative {
  position: relative;
}

.pr-10 {
  padding-right: 2.5rem; /* Space for the clear button */
}

.absolute {
  position: absolute;
}

.z-10 {
  z-index: 10;
}

.bg-white {
  background-color: #fff;
}

.border {
  border-width: 1px;
  border-style: solid;
  border-color: #e2e8f0; /* gray-300 */
}

.rounded-lg {
  border-radius: 0.5rem;
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.mt-1 {
  margin-top: 0.25rem;
}

.p-4 {
  padding: 1rem;
}

.flex {
  display: flex;
}

.justify-between {
  justify-content: space-between;
}

.items-center {
  align-items: center;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.px-2 {
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.py-1 {
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}

.rounded {
  border-radius: 0.25rem;
}

.hover\:bg-gray-200:hover {
  background-color: #edf2f7; /* gray-200 */
}

.grid {
  display: grid;
}

.grid-cols-7 {
  grid-template-columns: repeat(7, minmax(0, 1fr));
}

.text-center {
  text-align: center;
}

.text-xs {
  font-size: 0.75rem;
}

.font-medium {
  font-weight: 500;
}

.text-gray-500 {
  color: #a0aec0; /* gray-500 */
}

.mb-1 {
  margin-bottom: 0.25rem;
}

.gap-1 {
  gap: 0.25rem;
}

.text-sm {
  font-size: 0.875rem;
}

.p-1 {
  padding: 0.25rem;
}

.cursor-pointer {
  cursor: pointer;
}

.text-gray-400 {
  color: #cbd5e0; /* gray-400 */
}

.hover\:bg-cyan-100:hover {
  background-color: #e0f7fa; /* cyan-100 */
}

.bg-cyan-500 {
  background-color: #00bcd4; /* cyan-500 */
}

.text-white {
  color: #fff;
}

.font-bold {
  font-weight: 700;
}
</style>
