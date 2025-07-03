<script setup>
import { reactive, defineProps, watch } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps({
  form: Object,
  errors: Object,
})
</script>

<template>
  <div class="space-y-4">
    <!-- Name Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <InputLabel for="first_name" value="First Name" />
        <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.first_name" class="mt-1" />
      </div>
      <div>
        <InputLabel for="last_name" value="Last Name" />
        <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.last_name" class="mt-1" />
      </div>
    </div>

    <!-- Email + Phone -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <InputLabel for="email" value="Email" />
        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
        <InputError :message="errors.email" class="mt-1" />
      </div>
      <div>
        <InputLabel for="phone" value="Phone" />
        <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.phone" class="mt-1" />
      </div>
    </div>

    <!-- Position + Department -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <InputLabel for="position" value="Position" />
        <TextInput id="position" v-model="form.position" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.position" class="mt-1" />
      </div>
      <div>
        <InputLabel for="department" value="Department" />
        <TextInput id="department" v-model="form.department" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.department" class="mt-1" />
      </div>
    </div>

    <!-- Status + Hire Date -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <InputLabel for="status" value="Status" />
        <select v-model="form.status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
        <InputError :message="errors.status" class="mt-1" />
      </div>
      <div>
        <InputLabel for="hire_date" value="Hire Date" />
        <TextInput id="hire_date" v-model="form.hire_date" type="date" class="mt-1 block w-full" />
        <InputError :message="errors.hire_date" class="mt-1" />
      </div>
    </div>

    <!-- Photo Upload -->
    <div>
      <InputLabel for="photo" value="Photo" />
      <input type="file" id="photo" @change="$emit('file-change', $event)" class="mt-1 block w-full" />
      <InputError :message="errors.photo" class="mt-1" />
    </div>
  </div>
</template>
