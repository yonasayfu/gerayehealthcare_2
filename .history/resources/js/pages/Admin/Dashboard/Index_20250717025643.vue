<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import RecentSales from '@/components/RecentSales.vue';
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import { Bar, Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const barChartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Monthly Patient Registrations',
      backgroundColor: '#3b82f6',
      data: [400, 300, 500, 450, 600, 550, 700, 650, 800, 750, 900, 850],
    },
  ],
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}

const pieChartData = {
  labels: ['Cardiology', 'Pediatrics', 'Orthopedics', 'Dermatology'],
  datasets: [
    {
      backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350'],
      data: [300, 50, 100, 75],
    },
  ],
}

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
      <DashboardHeader />
      <DashboardTabs />
      <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
        <StatCard
          title="Total Patients"
          value="1,234"
          change="+15% from last month"
          :icon="Users"
          color="bg-blue-100"
        />
        <StatCard
          title="Active Staff"
          value="250"
          change="+5% from last month"
          :icon="Activity"
          color="bg-green-100"
        />
        <StatCard
          title="Completed Visits"
          value="876"
          change="+10% from last month"
          :icon="CreditCard"
          color="bg-yellow-100"
        />
        <StatCard
          title="Pending Tasks"
          value="42"
          change="-5% from last week"
          :icon="DollarSign"
          color="bg-red-100"
        />
      </div>
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
        <div class="col-span-4">
          <h3 class="text-lg font-medium">Overview</h3>
          <div class="h-[350px] mt-4">
            <Bar :data="chartData" :options="chartOptions" />
          </div>
        </div>
        <div class="col-span-3">
          <h3 class="text-lg font-medium">Recent Sales</h3>
          <p class="text-sm text-muted-foreground">You made 265 sales this month.</p>
          <RecentSales />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
