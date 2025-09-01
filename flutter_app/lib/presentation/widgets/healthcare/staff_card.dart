import 'package:flutter/material.dart';
import '../../../data/models/staff_model.dart';
import '../../../core/constants/app_constants.dart';

class StaffCard extends StatelessWidget {
  final StaffModel staff;
  final VoidCallback? onTap;
  final VoidCallback? onCall;
  final VoidCallback? onMessage;
  final VoidCallback? onSchedule;
  final bool showActions;
  final bool isCompact;

  const StaffCard({
    super.key,
    required this.staff,
    this.onTap,
    this.onCall,
    this.onMessage,
    this.onSchedule,
    this.showActions = true,
    this.isCompact = false,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    
    return Card(
      margin: EdgeInsets.symmetric(
        horizontal: isCompact ? 8 : AppConstants.defaultPadding,
        vertical: isCompact ? 4 : 8,
      ),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
        child: Padding(
          padding: EdgeInsets.all(isCompact ? 12 : AppConstants.defaultPadding),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header with avatar and basic info
              Row(
                children: [
                  // Staff Avatar
                  Stack(
                    children: [
                      CircleAvatar(
                        radius: isCompact ? 20 : 24,
                        backgroundColor: theme.colorScheme.primary.withOpacity(0.1),
                        child: Text(
                          staff.initials,
                          style: TextStyle(
                            color: theme.colorScheme.primary,
                            fontWeight: FontWeight.bold,
                            fontSize: isCompact ? 14 : 16,
                          ),
                        ),
                      ),
                      
                      // Availability status indicator
                      Positioned(
                        bottom: 0,
                        right: 0,
                        child: Container(
                          width: isCompact ? 12 : 14,
                          height: isCompact ? 12 : 14,
                          decoration: BoxDecoration(
                            color: _getAvailabilityColor(),
                            shape: BoxShape.circle,
                            border: Border.all(
                              color: theme.colorScheme.surface,
                              width: 2,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                  
                  const SizedBox(width: 12),
                  
                  // Staff Info
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          staff.displayName,
                          style: theme.textTheme.titleMedium?.copyWith(
                            fontWeight: FontWeight.w600,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        
                        const SizedBox(height: 2),
                        Text(
                          staff.specialization,
                          style: theme.textTheme.bodyMedium?.copyWith(
                            color: theme.colorScheme.onSurface.withOpacity(0.7),
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        
                        if (!isCompact && staff.qualification != null) ...[
                          const SizedBox(height: 2),
                          Text(
                            staff.qualification!,
                            style: theme.textTheme.bodySmall?.copyWith(
                              color: theme.colorScheme.onSurface.withOpacity(0.6),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ],
                    ),
                  ),
                  
                  // Status and availability
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      _buildAvailabilityChip(context),
                      if (!staff.isActive) ...[
                        const SizedBox(height: 4),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: Colors.grey.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            'INACTIVE',
                            style: TextStyle(
                              color: Colors.grey,
                              fontSize: 9,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),
                ],
              ),
              
              if (!isCompact) ...[
                const SizedBox(height: 12),
                
                // Staff Details
                Row(
                  children: [
                    if (staff.experienceYears != null) ...[
                      _buildInfoChip(
                        context,
                        Icons.work_outline,
                        staff.experienceDisplay,
                      ),
                      const SizedBox(width: 8),
                    ],
                    
                    if (staff.department != null) ...[
                      _buildInfoChip(
                        context,
                        Icons.business_outlined,
                        staff.department!,
                      ),
                      const SizedBox(width: 8),
                    ],
                    
                    if (staff.hourlyRate != null) ...[
                      _buildInfoChip(
                        context,
                        Icons.attach_money,
                        staff.hourlyRateDisplay,
                      ),
                    ],
                  ],
                ),
                
                if (staff.location != null) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(
                        Icons.location_on_outlined,
                        size: 16,
                        color: theme.colorScheme.onSurface.withOpacity(0.6),
                      ),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          staff.location!,
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: theme.colorScheme.onSurface.withOpacity(0.6),
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
                
                // Performance metrics
                if (staff.totalPatientsAssigned > 0 || staff.totalVisits > 0) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      if (staff.totalPatientsAssigned > 0) ...[
                        Icon(
                          Icons.people_outline,
                          size: 16,
                          color: theme.colorScheme.onSurface.withOpacity(0.6),
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '${staff.totalPatientsAssigned} patients',
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: theme.colorScheme.onSurface.withOpacity(0.6),
                          ),
                        ),
                        const SizedBox(width: 16),
                      ],
                      
                      if (staff.totalVisits > 0) ...[
                        Icon(
                          Icons.medical_services_outlined,
                          size: 16,
                          color: theme.colorScheme.onSurface.withOpacity(0.6),
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '${staff.totalVisits} visits',
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: theme.colorScheme.onSurface.withOpacity(0.6),
                          ),
                        ),
                      ],
                    ],
                  ),
                ],
                
                // Action buttons
                if (showActions && staff.isActive) ...[
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      if (staff.phone != null && onCall != null) ...[
                        _buildActionButton(
                          context,
                          Icons.phone_outlined,
                          'Call',
                          onCall!,
                        ),
                        const SizedBox(width: 8),
                      ],
                      
                      if (onMessage != null) ...[
                        _buildActionButton(
                          context,
                          Icons.message_outlined,
                          'Message',
                          onMessage!,
                        ),
                        const SizedBox(width: 8),
                      ],
                      
                      if (onSchedule != null) ...[
                        _buildActionButton(
                          context,
                          Icons.schedule_outlined,
                          'Schedule',
                          onSchedule!,
                        ),
                      ],
                      
                      const Spacer(),
                      
                      // Completion rate indicator
                      if (staff.completionRate > 0)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: _getCompletionRateColor(staff.completionRate).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            '${staff.completionRate.toStringAsFixed(0)}%',
                            style: TextStyle(
                              color: _getCompletionRateColor(staff.completionRate),
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                    ],
                  ),
                ],
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildAvailabilityChip(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: _getAvailabilityColor().withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        staff.availabilityStatusDisplay.toUpperCase(),
        style: TextStyle(
          color: _getAvailabilityColor(),
          fontSize: 10,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildInfoChip(BuildContext context, IconData icon, String label) {
    final theme = Theme.of(context);
    
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: theme.colorScheme.surfaceVariant.withOpacity(0.5),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            icon,
            size: 14,
            color: theme.colorScheme.onSurfaceVariant,
          ),
          const SizedBox(width: 4),
          Text(
            label,
            style: theme.textTheme.bodySmall?.copyWith(
              color: theme.colorScheme.onSurfaceVariant,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButton(
    BuildContext context,
    IconData icon,
    String label,
    VoidCallback onPressed,
  ) {
    final theme = Theme.of(context);
    
    return InkWell(
      onTap: onPressed,
      borderRadius: BorderRadius.circular(8),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          border: Border.all(
            color: theme.colorScheme.outline.withOpacity(0.3),
          ),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              size: 16,
              color: theme.colorScheme.primary,
            ),
            const SizedBox(width: 4),
            Text(
              label,
              style: theme.textTheme.bodySmall?.copyWith(
                color: theme.colorScheme.primary,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getAvailabilityColor() {
    if (staff.isAvailable) return Colors.green;
    if (staff.isBusy) return Colors.orange;
    if (staff.isOnEmergency) return Colors.red;
    return Colors.grey;
  }

  Color _getCompletionRateColor(double rate) {
    if (rate >= 90) return Colors.green;
    if (rate >= 70) return Colors.orange;
    return Colors.red;
  }
}
