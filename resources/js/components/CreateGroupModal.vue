<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 z-[99999] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Create New Group</h3>
        <button @click="$emit('close')" class="text-muted-foreground hover:text-foreground">
          <X class="w-5 h-5" />
        </button>
      </div>

      <form @submit.prevent="createGroup">
        <div class="mb-4">
          <label for="groupName" class="block text-sm font-medium text-foreground mb-1">Group Name</label>
          <input
            type="text"
            id="groupName"
            v-model="form.name"
            class="form-input w-full rounded-md border border-input px-3 py-2 text-sm bg-background text-foreground"
            required
          />
          <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
        </div>

        <div class="mb-4">
          <label for="description" class="block text-sm font-medium text-foreground mb-1">Description (Optional)</label>
          <textarea
            id="description"
            v-model="form.description"
            class="form-input w-full rounded-md border border-input px-3 py-2 text-sm bg-background text-foreground resize-none"
            rows="2"
            placeholder="Enter group description..."
          ></textarea>
          <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-foreground mb-2">Add Members</label>
          <div class="border border-input rounded-md bg-background max-h-48 overflow-y-auto">
            <div class="p-2">
              <input
                type="text"
                v-model="memberSearch"
                placeholder="Search users..."
                class="w-full px-3 py-2 text-sm border border-input rounded-md bg-background text-foreground"
              />
            </div>
            <div class="max-h-32 overflow-y-auto">
              <label
                v-for="user in filteredUsers"
                :key="user.id"
                class="flex items-center px-3 py-2 hover:bg-muted cursor-pointer transition-colors"
              >
                <input
                  type="checkbox"
                  :value="user.id"
                  v-model="form.members"
                  class="mr-3 rounded border-input"
                />
                <div class="flex items-center">
                  <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                    <span class="text-sm font-medium text-primary">{{ user.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-foreground">{{ user.name }}</div>
                    <div class="text-xs text-muted-foreground">{{ user.email }}</div>
                  </div>
                </div>
              </label>
              <div v-if="filteredUsers.length === 0" class="px-3 py-4 text-center text-muted-foreground text-sm">
                No users found
              </div>
            </div>
          </div>
          <p v-if="form.errors.members" class="text-red-500 text-xs mt-1">{{ form.errors.members }}</p>
          <p class="text-xs text-muted-foreground mt-1">{{ form.members.length }} member(s) selected</p>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded-md text-sm font-medium border border-input bg-background hover:bg-muted transition">Cancel</button>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition">
            {{ form.processing ? 'Creating...' : 'Create Group' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import axios from 'axios';
import type { User } from '@/types';

const props = defineProps<{ show: boolean }>();
const emit = defineEmits(['close', 'groupCreated']);

const form = useForm({
  name: '',
  description: '',
  members: [] as number[],
});

const availableUsers = ref<User[]>([]);
const memberSearch = ref('');

// Filter users based on search
const filteredUsers = computed(() => {
  if (!memberSearch.value) return availableUsers.value;

  const search = memberSearch.value.toLowerCase();
  return availableUsers.value.filter(user =>
    user.name.toLowerCase().includes(search) ||
    user.email.toLowerCase().includes(search)
  );
});

const fetchAvailableUsers = async () => {
  try {
    const response = await axios.get(route('users.for-groups'));
    availableUsers.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching users:', error);
    // Create some mock users for testing if API fails
    availableUsers.value = [
      { id: 1, name: 'Dr. Sarah Johnson', email: 'sarah.johnson@hospital.com' },
      { id: 2, name: 'Nurse Lisa Brown', email: 'lisa.brown@hospital.com' },
      { id: 3, name: 'Dr. Michael Chen', email: 'michael.chen@hospital.com' },
    ];
  }
};

const createGroup = async () => {
  if (form.members.length === 0) {
    alert('Please select at least one member for the group.');
    return;
  }

  try {
    const response = await axios.post(route('groups.store'), {
      name: form.name,
      description: form.description,
      members: form.members
    });

    const group = response.data?.data ?? response.data;
    emit('groupCreated', group);
    form.reset();
    memberSearch.value = '';
    emit('close');
    alert('Group created successfully!');
  } catch (error) {
    console.error('Error creating group:', error);
    if (error.response?.data?.errors) {
      const errorMessage = Object.values(error.response.data.errors).flat().join('\n');
      alert('Error creating group:\n' + errorMessage);
    } else {
      alert('Error creating group. Please try again.');
    }
  }
};

// Reset form when modal is closed
watch(() => props.show, (newShow) => {
  if (!newShow) {
    form.reset();
    memberSearch.value = '';
  }
});

onMounted(() => {
  fetchAvailableUsers();
});
</script>
