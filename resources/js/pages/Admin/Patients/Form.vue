<script setup lang="ts">
import type { PatientForm, InertiaForm } from '@/types' // Import PatientForm and InertiaForm types
import { computed, watch, ref, onMounted } from 'vue';
import { useEthiopianDate } from '@/composables/useEthiopianDate';

interface LocalErrors {
  source?: string;
  phone_number?: string;
  corporate_client_id?: string;
  policy_id?: string;
  // Add other potential local errors if they exist
}

type CorporateClientOption = { id: number; name: string } | { id: number; organization_name: string }
type InsurancePolicyOption = { id: number; service_type: string; coverage_percentage?: number; corporate_client?: { name?: string; organization_name?: string } }

const props = defineProps<{
  form: InertiaForm<PatientForm>,
  localErrors?: LocalErrors,
  corporateClients: CorporateClientOption[],
  insurancePolicies: InsurancePolicyOption[],
}>()

const emit = defineEmits(['submit'])

// Define options for dropdowns here
const genders = ['Male', 'Female']
const sources = ['TikTok', 'Website', 'Referral', 'Walk-in']

// Date conversion utilities and loop guards
const isSyncingFromGregorian = ref(false)
const isSyncingFromEthiopian = ref(false)
const { convertGregorianToEthiopian, convertEthiopianToGregorian } = useEthiopianDate()

// Computed properties for date handling
const dateOfBirth = computed({
  get: () => props.form.date_of_birth,
  set: (value) => {
    props.form.date_of_birth = value;
    // Auto-populate Ethiopian date when Gregorian changes
    if (!value) return;
    if (isSyncingFromEthiopian.value) return;
    isSyncingFromGregorian.value = true;
    const [y, m, d] = String(value).split('-').map(Number);
    if (!y || !m || !d) { isSyncingFromGregorian.value = false; return; }
    convertGregorianToEthiopian({ year: y, month: m, day: d }).then((eth) => {
      if (eth) {
        const mm = String(eth.month).padStart(2, '0')
        const dd = String(eth.day).padStart(2, '0')
        props.form.ethiopian_date_of_birth = `${eth.year}-${mm}-${dd}`
      }
    }).finally(() => { isSyncingFromGregorian.value = false })
  }
});

const ethiopianDateOfBirth = computed({
  get: () => props.form.ethiopian_date_of_birth,
  set: (value) => {
    props.form.ethiopian_date_of_birth = value;
    // Auto-populate Gregorian date when Ethiopian changes
    if (!value) return;
    if (isSyncingFromGregorian.value) return;
    isSyncingFromEthiopian.value = true;
    const [y, m, d] = String(value).split('-').map(Number);
    if (!y || !m || !d) { isSyncingFromEthiopian.value = false; return; }
    convertEthiopianToGregorian({ year: y, month: m, day: d }).then((gr) => {
      if (gr) {
        const mm = String(gr.month).padStart(2, '0')
        const dd = String(gr.day).padStart(2, '0')
        props.form.date_of_birth = `${gr.year}-${mm}-${dd}`
      }
    }).finally(() => { isSyncingFromEthiopian.value = false })
  }
});

onMounted(() => {
  // Initialize missing counterpart date on mount
  if (props.form.date_of_birth && !props.form.ethiopian_date_of_birth) {
    const [y, m, d] = String(props.form.date_of_birth).split('-').map(Number)
    if (y && m && d) {
      convertGregorianToEthiopian({ year: y, month: m, day: d }).then((eth) => {
        if (eth) {
          const mm = String(eth.month).padStart(2, '0')
          const dd = String(eth.day).padStart(2, '0')
          props.form.ethiopian_date_of_birth = `${eth.year}-${mm}-${dd}`
        }
      })
    }
  } else if (props.form.ethiopian_date_of_birth && !props.form.date_of_birth) {
    const [y, m, d] = String(props.form.ethiopian_date_of_birth).split('-').map(Number)
    if (y && m && d) {
      convertEthiopianToGregorian({ year: y, month: m, day: d }).then((gr) => {
        if (gr) {
          const mm = String(gr.month).padStart(2, '0')
          const dd = String(gr.day).padStart(2, '0')
          props.form.date_of_birth = `${gr.year}-${mm}-${dd}`
        }
      })
    }
  }
})

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Patient Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Use accurate and up-to-date details for patient registration.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.full_name"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">
              {{ form.errors.full_name }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Fayda ID</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.fayda_id"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.fayda_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.fayda_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Source</label>
          <div class="mt-2">
            <select
              v-model="form.source"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select source</option>
              <option v-for="s in sources" :key="s" :value="s">{{ s }}</option>
            </select>
            <div v-if="form.errors.source" class="text-red-500 text-sm mt-1">
              {{ form.errors.source }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.phone_number"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="props.localErrors?.phone_number" class="text-red-500 text-sm mt-1">
              {{ props.localErrors.phone_number }}
            </div>
            <div v-else-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">
              {{ form.errors.phone_number }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Email Address</label>
          <div class="mt-2">
            <input
              type="email"
              v-model.trim="form.email"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>
        </div>
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Gender</label>
          <div class="mt-2">
            <select
              v-model="form.gender"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select gender</option>
              <option v-for="genderOption in genders" :key="genderOption" :value="genderOption">{{ genderOption }}</option>
            </select>
            <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">
              {{ form.errors.gender }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Date of Birth (Gregorian) <span class="text-red-500">*</span></label>
          <div class="mt-2">
            <input
              type="date"
              v-model="dateOfBirth"
              required
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">
              {{ form.errors.date_of_birth }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Date of Birth (Ethiopian) <span class="text-red-500">*</span></label>
          <div class="mt-2">
            <input
              type="text"
              v-model="ethiopianDateOfBirth"
              placeholder="YYYY-MM-DD"
              required
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.ethiopian_date_of_birth" class="text-red-500 text-sm mt-1">
              {{ form.errors.ethiopian_date_of_birth }}
            </div>
          </div>
        </div>

        <div class="col-span-full">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Address</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.address"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">
              {{ form.errors.address }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Emergency Contact</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.emergency_contact"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.emergency_contact" class="text-red-500 text-sm mt-1">
              {{ form.errors.emergency_contact }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Geolocation</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.geolocation"
              placeholder="e.g., 9.012345,38.765432"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.geolocation" class="text-red-500 text-sm mt-1">
              {{ form.errors.geolocation }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Employer (Corporate Client)</label>
          <div class="mt-2">
            <select
              v-model="form.corporate_client_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select employer</option>
              <option
                v-for="c in corporateClients"
                :key="c.id"
                :value="c.id"
              >
                {{ c.name ?? c.organization_name ?? '—' }}
              </option>
            </select>
            <div v-if="form.errors?.corporate_client_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.corporate_client_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Insurance Policy</label>
          <div class="mt-2">
            <select
              v-model="form.policy_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select policy</option>
              <option
                v-for="p in insurancePolicies"
                :key="p.id"
                :value="p.id"
              >
                {{ p.policy_number ?? p.name ?? p.service_type ?? '—' }}
              </option>
            </select>
            <div v-if="form.errors?.policy_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.policy_id }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
