<script setup lang="ts">
import { ref, computed } from 'vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
  holidays: Array<{ date: string; name: string | null }>;
}>();

const currentDate = ref(new Date());

const monthNames = [
  'Meskerem', 'Tekemt', 'Hidar', 'Tahsas', 'Tir', 'Yekatit',
  'Megabit', 'Miazia', 'Ginbot', 'Sene', 'Hamle', 'Nehase', 'Pagume'
];

const currentMonth = computed(() => currentDate.value.getMonth());
const currentYear = computed(() => currentDate.value.getFullYear());

const daysInMonth = computed(() => {
  const year = currentYear.value;
  const month = currentMonth.value;
  if (month === 12) {
    const isLeap = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    return isLeap ? 6 : 5;
  }
  return 30;
});

const firstDayOfMonth = computed(() => {
  return (new Date(currentYear.value, currentMonth.value, 1).getDay() + 6) % 7;
});

const calendarGrid = computed(() => {
  const grid = [];
  let day = 1;
  for (let i = 0; i < 6; i++) {
    const week = [];
    for (let j = 0; j < 7; j++) {
      if ((i === 0 && j < firstDayOfMonth.value) || day > daysInMonth.value) {
        week.push(null);
      } else {
        week.push(day++);
      }
    }
    grid.push(week);
    if (day > daysInMonth.value) break;
  }
  return grid;
});

function prevMonth() {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
}

function nextMonth() {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
}

function isHoliday(day: number) {
  const date = new Date(currentYear.value, currentMonth.value, day);
  return props.holidays.some(h => new Date(h.date).toDateString() === date.toDateString());
}
</script>

<template>
  <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
      <button @click="prevMonth" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
        <ChevronLeft class="w-6 h-6" />
      </button>
      <h2 class="text-lg font-semibold">{{ monthNames[currentMonth] }} {{ currentYear }}</h2>
      <button @click="nextMonth" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
        <ChevronRight class="w-6 h-6" />
      </button>
    </div>
    <div class="grid grid-cols-7 gap-1 text-center">
      <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="font-semibold text-sm">{{ day }}</div>
      <template v-for="(week, i) in calendarGrid" :key="i">
        <div v-for="(day, j) in week" :key="j" class="p-2 rounded-full" :class="{ 'bg-red-500 text-white': day && isHoliday(day) }">
          {{ day }}
        </div>
      </template>
    </div>
  </div>
</template>
