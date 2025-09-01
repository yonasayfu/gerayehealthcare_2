import 'package:flutter/material.dart';
import '../../../core/constants/app_constants.dart';

class DashboardStatsCard extends StatelessWidget {
  final String title;
  final String value;
  final String? subtitle;
  final IconData icon;
  final Color? color;
  final VoidCallback? onTap;
  final Widget? trailing;
  final bool isLoading;
  final String? trend;
  final bool isPositiveTrend;

  const DashboardStatsCard({
    super.key,
    required this.title,
    required this.value,
    this.subtitle,
    required this.icon,
    this.color,
    this.onTap,
    this.trailing,
    this.isLoading = false,
    this.trend,
    this.isPositiveTrend = true,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final cardColor = color ?? theme.colorScheme.primary;
    
    return Card(
      margin: const EdgeInsets.all(8),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
        child: Container(
          padding: const EdgeInsets.all(AppConstants.defaultPadding),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                cardColor.withOpacity(0.1),
                cardColor.withOpacity(0.05),
              ],
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header with icon and trailing widget
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: cardColor.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Icon(
                      icon,
                      color: cardColor,
                      size: 20,
                    ),
                  ),
                  
                  const Spacer(),
                  
                  if (trailing != null) trailing!,
                ],
              ),
              
              const SizedBox(height: 16),
              
              // Title
              Text(
                title,
                style: theme.textTheme.bodyMedium?.copyWith(
                  color: theme.colorScheme.onSurface.withOpacity(0.7),
                  fontWeight: FontWeight.w500,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              
              const SizedBox(height: 8),
              
              // Value
              if (isLoading)
                Container(
                  height: 32,
                  width: 80,
                  decoration: BoxDecoration(
                    color: theme.colorScheme.surfaceVariant.withOpacity(0.5),
                    borderRadius: BorderRadius.circular(4),
                  ),
                )
              else
                Text(
                  value,
                  style: theme.textTheme.headlineMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                    color: cardColor,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              
              // Subtitle and trend
              if (subtitle != null || trend != null) ...[
                const SizedBox(height: 4),
                Row(
                  children: [
                    if (subtitle != null) ...[
                      Expanded(
                        child: Text(
                          subtitle!,
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: theme.colorScheme.onSurface.withOpacity(0.6),
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                    
                    if (trend != null) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: (isPositiveTrend ? Colors.green : Colors.red).withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              isPositiveTrend ? Icons.trending_up : Icons.trending_down,
                              size: 12,
                              color: isPositiveTrend ? Colors.green : Colors.red,
                            ),
                            const SizedBox(width: 2),
                            Text(
                              trend!,
                              style: TextStyle(
                                color: isPositiveTrend ? Colors.green : Colors.red,
                                fontSize: 10,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class DashboardStatsGrid extends StatelessWidget {
  final List<DashboardStatsCard> cards;
  final int crossAxisCount;
  final double childAspectRatio;

  const DashboardStatsGrid({
    super.key,
    required this.cards,
    this.crossAxisCount = 2,
    this.childAspectRatio = 1.5,
  });

  @override
  Widget build(BuildContext context) {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        childAspectRatio: childAspectRatio,
        crossAxisSpacing: 8,
        mainAxisSpacing: 8,
      ),
      itemCount: cards.length,
      itemBuilder: (context, index) => cards[index],
    );
  }
}

// Healthcare-specific stats cards
class HealthcareStatsCards {
  static DashboardStatsCard totalPatients({
    required String count,
    String? newToday,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Total Patients',
      value: count,
      subtitle: newToday != null ? '$newToday new today' : null,
      icon: Icons.people_outline,
      color: const Color(AppConstants.primaryColorValue),
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard todayVisits({
    required String count,
    String? completed,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Today\'s Visits',
      value: count,
      subtitle: completed != null ? '$completed completed' : null,
      icon: Icons.medical_services_outlined,
      color: const Color(AppConstants.secondaryColorValue),
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard availableStaff({
    required String count,
    String? total,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Available Staff',
      value: count,
      subtitle: total != null ? 'of $total total' : null,
      icon: Icons.person_outline,
      color: const Color(AppConstants.successColorValue),
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard revenue({
    required String amount,
    String? trend,
    bool isPositiveTrend = true,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Revenue',
      value: amount,
      icon: Icons.attach_money,
      color: Colors.green,
      trend: trend,
      isPositiveTrend: isPositiveTrend,
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard pendingClaims({
    required String count,
    String? amount,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Pending Claims',
      value: count,
      subtitle: amount != null ? amount : null,
      icon: Icons.receipt_long_outlined,
      color: const Color(AppConstants.warningColorValue),
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard lowStockItems({
    required String count,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Low Stock Items',
      value: count,
      subtitle: 'Need attention',
      icon: Icons.inventory_2_outlined,
      color: count != '0' ? const Color(AppConstants.emergencyColorValue) : Colors.green,
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard emergencyAlerts({
    required String count,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Emergency Alerts',
      value: count,
      subtitle: count != '0' ? 'Requires immediate attention' : 'All clear',
      icon: Icons.emergency_outlined,
      color: const Color(AppConstants.emergencyColorValue),
      onTap: onTap,
      isLoading: isLoading,
    );
  }

  static DashboardStatsCard unreadMessages({
    required String count,
    VoidCallback? onTap,
    bool isLoading = false,
  }) {
    return DashboardStatsCard(
      title: 'Unread Messages',
      value: count,
      icon: Icons.message_outlined,
      color: Colors.blue,
      onTap: onTap,
      isLoading: isLoading,
    );
  }
}
