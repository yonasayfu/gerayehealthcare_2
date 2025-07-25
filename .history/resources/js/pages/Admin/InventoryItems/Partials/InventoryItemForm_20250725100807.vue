<script setup lang="ts">
import { Label } from 'reka-ui/Label'
import { Input } from 'reka-ui/Input'
import { Textarea } from 'reka-ui/Textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from 'reka-ui/Select'
import { Calendar } from 'reka-ui/Calendar'
import { Popover, PopoverContent, PopoverTrigger } from 'reka-ui/Popover'
import { Button } from 'reka-ui/Button'
import { CalendarIcon } from 'lucide-vue-next'
import { format } from 'date-fns'
import { cn } from '@/lib/utils' // Utility for conditional classes
import type { InventoryCategory, Department, Staff } from '@/types'; // Import types

// Define the form data structure based on the backend validation rules
interface InventoryForm {
  item_code: string;
  name: string;
  description: string;
  category_id: number | null;
  department_id: number | null;
  serial_number: string;
  barcode: string;
  location: string;
  status: string;
  condition_status: string;
  purchase_date: Date | null;
  purchase_cost: number | null;
  current_value: number | null;
  warranty_expiry: Date | null;
  supplier: string;
  expiry_date: Date | null;
  last_maintenance: Date | null;
  next_maintenance: Date | null;
  maintenance_notes: string;
  is_assignable: boolean;
  minimum_stock_level: number | null;
  photo: File | null; // For new photo upload
  clear_photo?: boolean; // Flag to clear existing photo
  // For existing files (on edit)
  existing_photo_path?: string | null;
}

const props = defineProps<{
  form: InventoryForm; // The Inertia form object
  errors: Record<string, string>; // Validation errors from Inertia
  categories: InventoryCategory[]; // List of inventory categories
  departments: Department[]; // List of departments
  item_statuses: string[]; // List of item statuses from backend
  condition_statuses: string[]; // List of condition statuses from backend
}>();

// Emits for handling file input changes, as they need to update the parent form object
const emit = defineEmits(['update:photoFile']);

const handlePhotoFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  emit('update:photoFile', target.files?.[0] || null);
};

const clearPhoto = () => {
  props.form.photo = null;
  <div class="space-y-6">
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <Label for="item_code">Item Code <span class="text-red-500">*</span></Label>
        <Input id="item_code" type="text" v-model="form.item_code" :class="{ 'border-red-500': errors.item_code }" required />
        <p v-if="errors.item_code" class="text-sm text-red-500 mt-1">{{ errors.item_code }}</p>
      </div>

      <div>
        <Label for="name">Item Name <span class="text-red-500">*</span></Label>
        <Input id="name" type="text" v-model="form.name" :class="{ 'border-red-500': errors.name }" required />
        <p v-if="errors.name" class="text-sm text-red-500 mt-1">{{ errors.name }}</p>
      </div>

      <div>
        <Label for="category_id">Category <span class="text-red-500">*</span></Label>
        <Select v-model.number="form.category_id">
          <SelectTrigger :class="{ 'border-red-500': errors.category_id }">
            <SelectValue placeholder="Select a category" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="errors.category_id" class="text-sm text-red-500 mt-1">{{ errors.category_id }}</p>
      </div>

      <div>
        <Label for="department_id">Department</Label>
        <Select v-model.number="form.department_id">
          <SelectTrigger :class="{ 'border-red-500': errors.department_id }">
            <SelectValue placeholder="Select a department" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="null">N/A</SelectItem>
            <SelectItem v-for="department in departments" :key="department.id" :value="department.id">
              {{ department.name }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="errors.department_id" class="text-sm text-red-500 mt-1">{{ errors.department_id }}</p>
      </div>

      <div>
        <Label for="serial_number">Serial Number</Label>
        <Input id="serial_number" type="text" v-model="form.serial_number" :class="{ 'border-red-500': errors.serial_number }" />
        <p v-if="errors.serial_number" class="text-sm text-red-500 mt-1">{{ errors.serial_number }}</p>
      </div>

      <div>
        <Label for="barcode">Barcode</Label>
        <Input id="barcode" type="text" v-model="form.barcode" :class="{ 'border-red-500': errors.barcode }" />
        <p v-if="errors.barcode" class="text-sm text-red-500 mt-1">{{ errors.barcode }}</p>
      </div>

      <div>
        <Label for="location">Location</Label>
        <Input id="location" type="text" v-model="form.location" :class="{ 'border-red-500': errors.location }" />
        <p v-if="errors.location" class="text-sm text-red-500 mt-1">{{ errors.location }}</p>
      </div>

      <div>
        <Label for="status">Status <span class="text-red-500">*</span></Label>
        <Select v-model="form.status">
          <SelectTrigger :class="{ 'border-red-500': errors.status }">
            <SelectValue placeholder="Select a status" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="statusOption in item_statuses" :key="statusOption" :value="statusOption">
              {{ statusOption }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="errors.status" class="text-sm text-red-500 mt-1">{{ errors.status }}</p>
      </div>

      <div>
        <Label for="condition_status">Condition Status <span class="text-red-500">*</span></Label>
        <Select v-model="form.condition_status">
          <SelectTrigger :class="{ 'border-red-500': errors.condition_status }">
            <SelectValue placeholder="Select condition" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="conditionOption in condition_statuses" :key="conditionOption" :value="conditionOption">
              {{ conditionOption }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="errors.condition_status" class="text-sm text-red-500 mt-1">{{ errors.condition_status }}</p>
      </div>

      <div>
        <Label for="purchase_date">Purchase Date</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="cn(
                'w-full justify-start text-left font-normal',
                !form.purchase_date && 'text-muted-foreground',
                { 'border-red-500': errors.purchase_date }
              )"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              {{ form.purchase_date ? format(form.purchase_date, 'PPP') : 'Pick a date' }}
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <Calendar v-model:selected="form.purchase_date" />
          </PopoverContent>
        </Popover>
        <p v-if="errors.purchase_date" class="text-sm text-red-500 mt-1">{{ errors.purchase_date }}</p>
      </div>

      <div>
        <Label for="purchase_cost">Purchase Cost</Label>
        <Input id="purchase_cost" type="number" step="0.01" v-model="form.purchase_cost" :class="{ 'border-red-500': errors.purchase_cost }" />
        <p v-if="errors.purchase_cost" class="text-sm text-red-500 mt-1">{{ errors.purchase_cost }}</p>
      </div>

      <div>
        <Label for="current_value">Current Value</Label>
        <Input id="current_value" type="number" step="0.01" v-model="form.current_value" :class="{ 'border-red-500': errors.current_value }" />
        <p v-if="errors.current_value" class="text-sm text-red-500 mt-1">{{ errors.current_value }}</p>
      </div>

      <div>
        <Label for="warranty_expiry">Warranty Expiry Date</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="cn(
                'w-full justify-start text-left font-normal',
                !form.warranty_expiry && 'text-muted-foreground',
                { 'border-red-500': errors.warranty_expiry }
              )"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              {{ form.warranty_expiry ? format(form.warranty_expiry, 'PPP') : 'Pick a date' }}
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <Calendar v-model:selected="form.warranty_expiry" />
          </PopoverContent>
        </Popover>
        <p v-if="errors.warranty_expiry" class="text-sm text-red-500 mt-1">{{ errors.warranty_expiry }}</p>
      </div>

      <div>
        <Label for="supplier">Supplier</Label>
        <Input id="supplier" type="text" v-model="form.supplier" :class="{ 'border-red-500': errors.supplier }" />
        <p v-if="errors.supplier" class="text-sm text-red-500 mt-1">{{ errors.supplier }}</p>
      </div>

      <div>
        <Label for="expiry_date">Expiry Date</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="cn(
                'w-full justify-start text-left font-normal',
                !form.expiry_date && 'text-muted-foreground',
                { 'border-red-500': errors.expiry_date }
              )"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              {{ form.expiry_date ? format(form.expiry_date, 'PPP') : 'Pick a date' }}
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <Calendar v-model:selected="form.expiry_date" />
          </PopoverContent>
        </Popover>
        <p v-if="errors.expiry_date" class="text-sm text-red-500 mt-1">{{ errors.expiry_date }}</p>
      </div>

      <div>
        <Label for="last_maintenance">Last Maintenance Date</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="cn(
                'w-full justify-start text-left font-normal',
                !form.last_maintenance && 'text-muted-foreground',
                { 'border-red-500': errors.last_maintenance }
              )"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              {{ form.last_maintenance ? format(form.last_maintenance, 'PPP') : 'Pick a date' }}
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <Calendar v-model:selected="form.last_maintenance" />
          </PopoverContent>
        </Popover>
        <p v-if="errors.last_maintenance" class="text-sm text-red-500 mt-1">{{ errors.last_maintenance }}</p>
      </div>

      <div>
        <Label for="next_maintenance">Next Maintenance Date</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="cn(
                'w-full justify-start text-left font-normal',
                !form.next_maintenance && 'text-muted-foreground',
                { 'border-red-500': errors.next_maintenance }
              )"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              {{ form.next_maintenance ? format(form.next_maintenance, 'PPP') : 'Pick a date' }}
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <Calendar v-model:selected="form.next_maintenance" />
          </PopoverContent>
        </Popover>
        <p v-if="errors.next_maintenance" class="text-sm text-red-500 mt-1">{{ errors.next_maintenance }}</p>
      </div>

      <div>
        <Label for="minimum_stock_level">Minimum Stock Level</Label>
        <Input id="minimum_stock_level" type="number" v-model="form.minimum_stock_level" :class="{ 'border-red-500': errors.minimum_stock_level }" />
        <p v-if="errors.minimum_stock_level" class="text-sm text-red-500 mt-1">{{ errors.minimum_stock_level }}</p>
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" id="is_assignable" v-model="form.is_assignable" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" />
        <Label for="is_assignable">Is Assignable?</Label>
        <p v-if="errors.is_assignable" class="text-sm text-red-500 mt-1">{{ errors.is_assignable }}</p>
      </div>
    </div>

    <div class="col-span-2">
      <Label for="description">Description</Label>
      <Textarea id="description" v-model="form.description" :class="{ 'border-red-500': errors.description }" rows="4" />
      <p v-if="errors.description" class="text-sm text-red-500 mt-1">{{ errors.description }}</p>
    </div>

    <div class="col-span-2">
      <Label for="maintenance_notes">Maintenance Notes</Label>
      <Textarea id="maintenance_notes" v-model="form.maintenance_notes" :class="{ 'border-red-500': errors.maintenance_notes }" rows="4" />
      <p v-if="errors.maintenance_notes" class="text-sm text-red-500 mt-1">{{ errors.maintenance_notes }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <Label for="photo">Item Photo</Label>
            <Input id="photo" type="file" @change="handlePhotoFileChange"
                :class="{ 'border-red-500': errors.photo }"
            />
            <p v-if="errors.photo" class="text-sm text-red-500 mt-1">{{ errors.photo }}</p>
            <p class="text-xs text-gray-500 mt-1">Max 2MB. JPG, PNG, GIF, SVG. Leave blank to keep current.</p>
            <div v-if="form.existing_photo_path && !form.photo" class="mt-2">
                <p class="text-sm text-gray-500">Current Photo:</p>
                <img :src="`/storage/${form.existing_photo_path}`" alt="Current Photo" class="h-20 w-20 object-contain mt-1 border rounded p-1" />
                <Button type="button" variant="destructive" size="sm" @click="clearPhoto" class="mt-2">
                    Remove Photo
                </Button>
            </div>
        </div>
    </div>
  </div>
</div>
</template>
