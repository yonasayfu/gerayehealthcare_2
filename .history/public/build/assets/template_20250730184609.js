 <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-8 shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.2s">
            <h2 class="text-lg font-medium mb-1">All Earnings</h2>
            <p class="text-3xl font-bold mb-4" id="earnings-counter">$0</p>
            <div class="flex justify-between items-center">
                <span class="text-indigo-100">IDX changes on profit</span>
                <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-medium transform transition duration-300 hover:scale-110">+3.2%</span>
            </div>
        </div>

        <!-- Stats Grid with staggered animations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Sales Card -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 0.3s">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Sales Per Day</h3>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-2xl font-bold text-gray-800" id="sales-percent">0%</p>
                        <p class="text-green-500 text-sm font-medium">+1.2% from yesterday</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 0.4s">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Revenue</h3>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-2xl font-bold text-gray-800" id="revenue-counter">$0</p>
                        <p class="text-gray-500 text-sm">321 Today Sales</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
                        <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Stats with hover animations -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.5s">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Social Media Performance</h3>
            <div class="space-y-4">
                <!-- YouTube -->
                <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                            <i class="fab fa-youtube text-red-600"></i>
                        </div>
                        <span class="font-medium">YouTube</span>
                    </div>
                    <div class="text-right">
                        <p class="text-green-500 font-medium">+1/8 REVX</p>
                        <p class="text-xs text-gray-500">Views: 12.4K</p>
                    </div>
                </div>

                <!-- Facebook -->
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                            <i class="fab fa-facebook-f text-blue-600"></i>
                        </div>
                        <span class="font-medium">Facebook</span>
                    </div>
                    <div class="text-right">
                        <p class="text-green-500 font-medium">+4/5 26.9K</p>
                        <p class="text-xs text-gray-500">Engagement: 8.2%</p>
                    </div>
                </div>

                <!-- Twitter -->
                <div class="flex justify-between items-center p-3 bg-sky-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                    <div class="flex items-center space-x-3">
                        <div class="bg-sky-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                            <i class="fab fa-twitter text-sky-500"></i>
                        </div>
                        <span class="font-medium">Twitter</span>
                    </div>
                    <div class="text-right">
                        <p class="text-red-500 font-medium">-6/10 6.9K</p>
                        <p class="text-xs text-gray-500">Impressions: 24.1K</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic Sources with animated progress bars -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.6s">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Traffic Sources</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-24 font-medium">Direct</span>
                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full progress-bar" style="width: 0%" data-width="80%"></div>
                        </div>
                    </div>
                    <span class="w-10 text-right font-medium" id="direct-percent">0%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="w-24 font-medium">Social</span>
                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-500 h-2.5 rounded-full progress-bar" style="width: 0%" data-width="50%"></div>
                        </div>
                    </div>
                    <span class="w-10 text-right font-medium" id="social-percent">0%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="w-24 font-medium">Referral</span>
                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full progress-bar" style="width: 0%" data-width="20%"></div>
                        </div>
                    </div>
                    <span class="w-10 text-right font-medium" id="referral-percent">0%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="w-24 font-medium">Bounce</span>
                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-yellow-500 h-2.5 rounded-full progress-bar" style="width: 0%" data-width="60%"></div>
                        </div>
                    </div>
                    <span class="w-10 text-right font-medium" id="bounce-percent">0%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="w-24 font-medium">Internet</span>
                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-purple-500 h-2.5 rounded-full progress-bar" style="width: 0%" data-width="40%"></div>
                        </div>
                    </div>
                    <span class="w-10 text-right font-medium" id="internet-percent">0%</span>
                </div>
            </div>
        </div>
    </div>