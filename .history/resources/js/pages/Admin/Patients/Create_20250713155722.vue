<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import InputLabel from '@/components/InputLabel.vue'
import TextInput from '@/components/TextInput.vue'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { format } from 'date-fns'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { cn } from '@/lib/utils' // Assuming you have a utility for class concatenation


const props = defineProps<{
  form: any // Use a more specific type if possible, e.g., PatientForm
}>()

// Options for dropdowns
const genders = ['Male', 'Female', 'Other', 'Prefer not to say']
const sources = ['TikTok', 'Website', 'Referral', 'Walk-in']

</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <InputLabel for="full_name" value="Full Name" />
      <TextInput
        id="full_name"
        v-model="form.full_name"
        type="text"
        class="mt-1 block w-full"
        required
        autofocus
      />
      <InputError class="mt-2" :message="form.errors.full_name" />
    </div>

    <div>
      <InputLabel for="fayda_id" value="Fayda ID" />
      <TextInput
        id="fayda_id"
        v-model="form.fayda_id"
        type="text"
        class="mt-1 block w-full"
      />
      <InputError class="mt-2" :message="form.errors.fayda_id" />
    </div>

    <div>
      <InputLabel for="source" value="Source" />
      <select
        id="source"
        v-model="form.source"
        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
      >
        <option :value="null">Select source</option>
        <option v-for="sourceOption in sources" :key="sourceOption" :value="sourceOption">{{ sourceOption }}</option>
      </select>
      <InputError class="mt-2" :message="form.errors.source" />
    </div>

    <div>
      <InputLabel for="phone_number" value="Phone Number" />
      <TextInput
        id="phone_number"
        v-model="form.phone_number"
        type="text"
        class="mt-1 block w-full"
      />
      <InputError class="mt-2" :message="form.errors.phone_number" />
    </div>

    <div>
      <InputLabel for="email" value="Email Address" />
      <TextInput
        id="email"
        v-model="form.email"
        type="email"
        class="mt-1 block w-full"
      />
      <InputError class="mt-2" :message="form.errors.email" />
    </div>
    <div>
      <InputLabel for="gender" value="Gender" />
      <select
        id="gender"
        v-model="form.gender"
        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
      >
        <option :value="null">Select gender</option>
        <option v-for="genderOption in genders" :key="genderOption" :value="genderOption">{{ genderOption }}</option>
      </select>
      <InputError class="mt-2" :message="form.errors.gender" />
    </div>

    <div>
      <InputLabel for="date_of_birth" value="Date of Birth" />
      <Popover>
        <PopoverTrigger as-child>
          <button
            :class="cn(
              'mt-1 flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
              !form.date_of_birth && 'text-muted-foreground',
            )"
          >
            <span>{{ form.date_of_birth ? format(new Date(form.date_of_birth), 'PPP') : 'Pick a date' }}</span>
            <CalendarIcon class="h-4 w-4 opacity-50" />
          </button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
          <Calendar
            v-model:value="form.date_of_birth"
            @update:model-value="(date) => { form.date_of_birth = date ? format(date, 'yyyy-MM-dd') : null }"
            mode="single"
            :initial-focus="true"
          />
        </PopoverContent>
      </Popover>
      <InputError class="mt-2" :message="form.errors.date_of_birth" />
    </div>

    <div class="col-span-1 md:col-span-2">
      <InputLabel for="address" value="Address" />
      <TextInput
        id="address"
        v-model="form.address"
        type="text"
        class="mt-1 block w-full"
      />
      <InputError class="mt-2" :message="form.errors.address" />
    </div>

    <div>
      <InputLabel for="emergency_contact" value="Emergency Contact" />
      <TextInput
        id="emergency_contact"
        v-model="form.emergency_contact"
        type="text"
        class="mt-1 block w-full"
      />
      <InputError class="mt-2" :message="form.errors.emergency_contact" />
    </div>

    <div>
      <InputLabel for="geolocation" value="Geolocation" />
      <TextInput
        id="geolocation"
        v-model="form.geolocation"
        type="text"
        class="mt-1 block w-full"
        placeholder="e.g., 9.012345,38.765432"
      />
      <InputError class="mt-2" :message="form.errors.geolocation" />
    </div>
  </div>
</template>