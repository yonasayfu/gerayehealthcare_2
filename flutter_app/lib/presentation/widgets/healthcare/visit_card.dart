import 'package:flutter/material.dart';
import '../../../data/models/visit_service_model.dart';
import '../../../core/constants/app_constants.dart';

class VisitCard extends StatelessWidget {
  final VisitServiceModel visit;
  final VoidCallback? onTap;
  final VoidCallback? onCheckIn;
  final VoidCallback? onCheckOut;
  final VoidCallback? onComplete;
  final VoidCallback? onCancel;
  final bool showActions;
  final bool isCompact;

  const VisitCard({
    super.key,
    required this.visit,
    this.onTap,
    this.onCheckIn,
    this.onCheckOut,
    this.onComplete,
    this.onCancel,
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
              // Header with patient info and status
              Row(
                children: [
                  // Service type icon
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: _getServiceTypeColor(visit.serviceType).withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Icon(
                      _getServiceTypeIcon(visit.serviceType),
                      color: _getServiceTypeColor(visit.serviceType),
                      size: isCompact ? 16 : 20,
                    ),
                  ),
                  
                  const SizedBox(width: 12),
                  
                  // Visit Info
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          visit.patientName,
                          style: theme.textTheme.titleMedium?.copyWith(
                            fontWeight: FontWeight.w600,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        
                        const SizedBox(height: 2),
                        Text(
                          visit.serviceName,
                          style: theme.textTheme.bodyMedium?.copyWith(
                            color: theme.colorScheme.onSurface.withOpacity(0.7),
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  
                  // Status and priority indicators
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      _buildStatusChip(context),
                      if (visit.isHighPriority || visit.isUrgent || visit.isEmergency) ...[
                        const SizedBox(height: 4),
                        _buildPriorityChip(context),
                      ],
                    ],
                  ),
                ],
              ),
              
              if (!isCompact) ...[
                const SizedBox(height: 12),
                
                // Visit details
                Row(
                  children: [
                    // Date and time
                    Icon(
                      Icons.schedule_outlined,
                      size: 16,
                      color: theme.colorScheme.onSurface.withOpacity(0.6),
                    ),
                    const SizedBox(width: 4),
                    Text(
                      '${visit.visitDate} • ${visit.timeDisplay}',
                      style: theme.textTheme.bodySmall?.copyWith(
                        color: theme.colorScheme.onSurface.withOpacity(0.6),
                      ),
                    ),
                    
                    const SizedBox(width: 16),
                    
                    // Duration
                    Icon(
                      Icons.timer_outlined,
                      size: 16,
                      color: theme.colorScheme.onSurface.withOpacity(0.6),
                    ),
                    const SizedBox(width: 4),
                    Text(
                      visit.durationDisplay,
                      style: theme.textTheme.bodySmall?.copyWith(
                        color: theme.colorScheme.onSurface.withOpacity(0.6),
                      ),
                    ),
                  ],
                ),
                
                if (visit.staffName.isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(
                        Icons.person_outlined,
                        size: 16,
                        color: theme.colorScheme.onSurface.withOpacity(0.6),
                      ),
                      const SizedBox(width: 4),
                      Text(
                        'Assigned to: ${visit.staffName}',
                        style: theme.textTheme.bodySmall?.copyWith(
                          color: theme.colorScheme.onSurface.withOpacity(0.6),
                        ),
                      ),
                    ],
                  ),
                ],
                
                if (visit.visitLocation != null) ...[
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
                          visit.visitLocation!,
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
                
                // Check-in/out status
                if (visit.isCheckedIn || visit.isCheckedOut) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      if (visit.isCheckedIn) ...[
                        Icon(
                          Icons.login,
                          size: 16,
                          color: Colors.green,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          'Checked in: ${visit.checkInTime}',
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: Colors.green,
                          ),
                        ),
                      ],
                      
                      if (visit.isCheckedOut) ...[
                        if (visit.isCheckedIn) const SizedBox(width: 16),
                        Icon(
                          Icons.logout,
                          size: 16,
                          color: Colors.blue,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          'Checked out: ${visit.checkOutTime}',
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: Colors.blue,
                          ),
                        ),
                      ],
                    ],
                  ),
                ],
                
                // Action buttons
                if (showActions && _shouldShowActions()) ...[
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      if (visit.isScheduled && !visit.isCheckedIn && onCheckIn != null) ...[
                        _buildActionButton(
                          context,
                          Icons.login,
                          'Check In',
                          onCheckIn!,
                          color: Colors.green,
                        ),
                        const SizedBox(width: 8),
                      ],
                      
                      if (visit.isCheckedIn && !visit.isCheckedOut && onCheckOut != null) ...[
                        _buildActionButton(
                          context,
                          Icons.logout,
                          'Check Out',
                          onCheckOut!,
                          color: Colors.blue,
                        ),
                        const SizedBox(width: 8),
                      ],
                      
                      if (visit.isInProgress && onComplete != null) ...[
                        _buildActionButton(
                          context,
                          Icons.check_circle_outline,
                          'Complete',
                          onComplete!,
                          color: Colors.green,
                        ),
                        const SizedBox(width: 8),
                      ],
                      
                      const Spacer(),
                      
                      if ((visit.isScheduled || visit.isInProgress) && onCancel != null)
                        _buildActionButton(
                          context,
                          Icons.cancel_outlined,
                          'Cancel',
                          onCancel!,
                          color: Colors.red,
                          isOutlined: true,
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

  Widget _buildStatusChip(BuildContext context) {
    final theme = Theme.of(context);
    Color color;
    
    switch (visit.status.toLowerCase()) {
      case 'scheduled':
        color = Colors.blue;
        break;
      case 'in_progress':
        color = Colors.orange;
        break;
      case 'completed':
        color = Colors.green;
        break;
      case 'cancelled':
        color = Colors.red;
        break;
      case 'no_show':
        color = Colors.grey;
        break;
      default:
        color = theme.colorScheme.primary;
    }
    
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        visit.statusDisplay.toUpperCase(),
        style: TextStyle(
          color: color,
          fontSize: 10,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildPriorityChip(BuildContext context) {
    Color color;
    
    if (visit.isEmergency) {
      color = Colors.red;
    } else if (visit.isUrgent) {
      color = Colors.orange;
    } else {
      color = Colors.amber;
    }
    
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        visit.priorityDisplay.toUpperCase(),
        style: TextStyle(
          color: color,
          fontSize: 9,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildActionButton(
    BuildContext context,
    IconData icon,
    String label,
    VoidCallback onPressed, {
    Color? color,
    bool isOutlined = false,
  }) {
    final theme = Theme.of(context);
    final buttonColor = color ?? theme.colorScheme.primary;
    
    return InkWell(
      onTap: onPressed,
      borderRadius: BorderRadius.circular(8),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: isOutlined ? Colors.transparent : buttonColor.withOpacity(0.1),
          border: Border.all(
            color: isOutlined ? buttonColor.withOpacity(0.3) : Colors.transparent,
          ),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              size: 16,
              color: buttonColor,
            ),
            const SizedBox(width: 4),
            Text(
              label,
              style: theme.textTheme.bodySmall?.copyWith(
                color: buttonColor,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }

  bool _shouldShowActions() {
    return visit.isScheduled || visit.isInProgress || visit.isCheckedIn;
  }

  IconData _getServiceTypeIcon(String serviceType) {
    switch (serviceType.toLowerCase()) {
      case 'consultation':
        return Icons.medical_services_outlined;
      case 'checkup':
        return Icons.health_and_safety_outlined;
      case 'treatment':
        return Icons.healing_outlined;
      case 'emergency':
        return Icons.emergency_outlined;
      case 'follow_up':
        return Icons.follow_the_signs_outlined;
      default:
        return Icons.local_hospital_outlined;
    }
  }

  Color _getServiceTypeColor(String serviceType) {
    switch (serviceType.toLowerCase()) {
      case 'consultation':
        return Colors.blue;
      case 'checkup':
        return Colors.green;
      case 'treatment':
        return Colors.purple;
      case 'emergency':
        return Colors.red;
      case 'follow_up':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }
}
