<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ShowHeader from '@/components/ShowHeader.vue'
import { Edit3, Printer, Share2, ChevronDown, Mail } from 'lucide-vue-next'
import { format } from 'date-fns'
import { 
  Dialog, 
  DialogContent, 
  DialogHeader, 
  DialogTitle, 
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu'
import { useToast } from '@/components/ui/toast/use-toast'

interface PrescriptionItem {
  id: number
  medication_name: string
  dosage: string
  frequency: string
  duration: string
  notes: string
}

interface Prescription {
  id: number
  patient_id: number
  created_by_staff_id: number
  prescribed_date: string
  status: string
  instructions: string
  share_token: string
  items: PrescriptionItem[]
  patient?: {
    full_name: string
  }
  created_by?: {
    full_name: string
  }
}

interface Props {
  prescription: Prescription
}

const props = defineProps<Props>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Prescriptions', href: route('admin.prescriptions.index') },
  { title: `#${props.prescription?.id ?? ''}`, href: null },
]

const formattedDate = computed(() => {
  if (!props.prescription?.prescribed_date) return '-'
  try {
    return format(new Date(props.prescription.prescribed_date), 'PPP')
  } catch {
    return props.prescription.prescribed_date
  }
})

function printSingle() {
  setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100)
}

// Share functionality
function share(platform: string) {
  const shareUrl = route('prescriptions.share', props.prescription.share_token)
  const text = `Prescription for ${props.prescription.patient?.full_name || 'Patient'}`
  
  const urls: Record<string, string> = {
    wa: `https://wa.me/?text=${encodeURIComponent(text + ' ' + shareUrl)}`,
    tw: `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(shareUrl)}`,
    tg: `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(text)}`,
  }
  
  if (urls[platform]) {
    window.open(urls[platform], '_blank')
  }
}

function copyShareLink() {
  const shareUrl = route('prescriptions.share', props.prescription.share_token)
  navigator.clipboard.writeText(shareUrl).then(() => {
    const { toast } = useToast()
    toast({ 
      title: 'Link copied', 
      description: 'Prescription link copied to clipboard',
      variant: 'default'
    })
  })
}

// Email sharing functionality
const isEmailShareOpen = ref(false)
const isSendingEmail = ref(false)
const emailSuccessMessage = ref('')
const emailErrorMessage = ref('')

const emailForm = ref({
  email: '',
  message: ''
})

async function sendEmailShare() {
  if (!emailForm.value.email) {
    emailErrorMessage.value = 'Email address is required'
    return
  }
  
  isSendingEmail.value = true
  emailSuccessMessage.value = ''
  emailErrorMessage.value = ''
  
  try {
    const response = await fetch(route('admin.prescriptions.shareEmail', props.prescription.id), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(emailForm.value)
    })
    
    const data = await response.json()
    
    if (response.ok) {
      emailSuccessMessage.value = data.message || 'Email sent successfully'
      emailForm.value.email = ''
      emailForm.value.message = ''
      
      // Close the dialog
      isEmailShareOpen.value = false
      
      // Show success toast
      const { toast } = useToast()
      toast({ 
        title: 'Email sent', 
        description: 'Prescription shared via email successfully',
        variant: 'default'
      })
    } else {
      emailErrorMessage.value = data.message || 'Failed to send email'
    }
  } catch (error) {
    emailErrorMessage.value = 'An error occurred while sending the email'
    console.error('Email share error:', error)
  } finally {
    isSendingEmail.value = false
  }
}
</script>

<template>
  <Head title="Prescription Details" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative">
        <ShowHeader title="Prescription Details" :subtitle="`Prescription #${prescription.id}`">
          <template #actions>
            <Link :href="route('admin.prescriptions.index')" class="btn-glass btn-glass-sm">Back</Link>
          </template>
        </ShowHeader>

        <div class="p-6 space-y-6">
          <div class="hidden print:block text-center mb-4 print:mb-2">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Prescription</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <div class="text-sm text-gray-500">Prescribed Date</div>
              <div class="font-medium">{{ formattedDate }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Status</div>
              <div class="font-medium capitalize">{{ prescription.status }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Created By</div>
              <div class="font-medium">{{ prescription.created_by?.full_name || '-' }}</div>
            </div>
          </div>

          <div class="mb-6">
            <div class="text-sm text-gray-500">Instructions</div>
            <div class="whitespace-pre-wrap">{{ prescription.instructions || '-' }}</div>
          </div>

          <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Items</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm print-table">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
                  <tr>
                    <th class="px-4 py-2">Medication</th>
                    <th class="px-4 py-2">Dosage</th>
                    <th class="px-4 py-2">Frequency</th>
                    <th class="px-4 py-2">Duration</th>
                    <th class="px-4 py-2">Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="it in prescription.items || []" :key="it.id" class="border-b dark:border-gray-700">
                    <td class="px-4 py-2">{{ it.medication_name }}</td>
                    <td class="px-4 py-2">{{ it.dosage }}</td>
                    <td class="px-4 py-2">{{ it.frequency }}</td>
                    <td class="px-4 py-2">{{ it.duration }}</td>
                    <td class="px-4 py-2">{{ it.notes }}</td>
                  </tr>
                  <tr v-if="!prescription.items || prescription.items.length === 0">
                    <td class="px-4 py-3 text-center text-gray-400" colspan="5">No items.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
          <div class="flex justify-end gap-2">
            <Link :href="route('admin.prescriptions.edit', prescription.id)" class="btn-glass btn-glass-sm">
              <Edit3 class="icon" />
              <span class="hidden sm:inline">Edit</span>
            </Link>
            <button @click="printSingle" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print</span>
            </button>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <button class="btn-glass btn-glass-sm">
                  <Share2 class="icon" />
                  <span class="hidden sm:inline">Share</span>
                  <ChevronDown class="w-4 h-4" />
                </button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem @click="() => share('wa')">
                  <span>WhatsApp</span>
                </DropdownMenuItem>
                <DropdownMenuItem @click="() => share('tw')">
                  <span>Twitter</span>
                </DropdownMenuItem>
                <DropdownMenuItem @click="() => share('tg')">
                  <span>Telegram</span>
                </DropdownMenuItem>
                <DropdownMenuItem @click="copyShareLink">
                  <span>Copy Link</span>
                </DropdownMenuItem>
                <DropdownMenuItem @click="isEmailShareOpen = true">
                  <Mail class="w-4 h-4 mr-2" />
                  <span>Email</span>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
          <p>Generated on: {{ new Date().toLocaleString() }}</p>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Email Share Dialog -->
  <Dialog :open="isEmailShareOpen" @update:open="isEmailShareOpen = $event">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Share via Email</DialogTitle>
        <DialogDescription>Send this prescription to an email address</DialogDescription>
      </DialogHeader>
      <div class="space-y-4">
        <div v-if="emailSuccessMessage" class="text-sm text-green-600 bg-green-50 p-2 rounded">
          {{ emailSuccessMessage }}
        </div>
        <div v-if="emailErrorMessage" class="text-sm text-red-600 bg-red-50 p-2 rounded">
          {{ emailErrorMessage }}
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email Address</label>
          <input 
            v-model="emailForm.email" 
            type="email" 
            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
            placeholder="recipient@example.com" 
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Message (Optional)</label>
          <textarea 
            v-model="emailForm.message" 
            rows="3"
            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
            placeholder="Add a personal message..."
          ></textarea>
        </div>
      </div>
      <DialogFooter>
        <Button variant="outline" @click="isEmailShareOpen = false" :disabled="isSendingEmail">Cancel</Button>
        <Button @click="sendEmailShare" :disabled="isSendingEmail">
          {{ isSendingEmail ? 'Sending...' : 'Send Email' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>