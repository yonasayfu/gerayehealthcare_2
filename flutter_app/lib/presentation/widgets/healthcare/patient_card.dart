import 'package:flutter/material.dart';
import '../../../data/models/patient_model.dart';
import '../../../core/constants/app_constants.dart';

class PatientCard extends StatelessWidget {
  final PatientModel patient;
  final VoidCallback? onTap;
  final VoidCallback? onCall;
  final VoidCallback? onMessage;
  final bool showActions;
  final bool isCompact;

  const PatientCard({
    super.key,
    required this.patient,
    this.onTap,
    this.onCall,
    this.onMessage,
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
                  // Patient Avatar
                  CircleAvatar(
                    radius: isCompact ? 20 : 24,
                    backgroundColor: theme.colorScheme.primary.withOpacity(0.1),
                    child: Text(
                      patient.initials,
                      style: TextStyle(
                        color: theme.colorScheme.primary,
                        fontWeight: FontWeight.bold,
                        fontSize: isCompact ? 14 : 16,
                      ),
                    ),
                  ),
                  
                  const SizedBox(width: 12),
                  
                  // Patient Info
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          patient.displayName,
                          style: theme.textTheme.titleMedium?.copyWith(
                            fontWeight: FontWeight.w600,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        
                        if (!isCompact) ...[
                          const SizedBox(height: 2),
                          Text(
                            patient.primaryContact,
                            style: theme.textTheme.bodyMedium?.copyWith(
                              color: theme.colorScheme.onSurface.withOpacity(0.7),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ],
                    ),
                  ),
                  
                  // Status indicator
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: patient.isActive 
                          ? Colors.green.withOpacity(0.1)
                          : Colors.grey.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      patient.status.toUpperCase(),
                      style: TextStyle(
                        color: patient.isActive ? Colors.green : Colors.grey,
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ],
              ),
              
              if (!isCompact) ...[
                const SizedBox(height: 12),
                
                // Patient Details
                Row(
                  children: [
                    if (patient.age != null) ...[
                      _buildInfoChip(
                        context,
                        Icons.cake_outlined,
                        patient.ageDisplay,
                      ),
                      const SizedBox(width: 8),
                    ],
                    
                    if (patient.gender != null) ...[
                      _buildInfoChip(
                        context,
                        patient.gender?.toLowerCase() == 'male' 
                            ? Icons.male 
                            : Icons.female,
                        patient.gender!,
                      ),
                      const SizedBox(width: 8),
                    ],
                    
                    if (patient.bloodType != null) ...[
                      _buildInfoChip(
                        context,
                        Icons.bloodtype,
                        patient.bloodType!,
                      ),
                    ],
                  ],
                ),
                
                if (patient.city != null) ...[
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
                          patient.city!,
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
                
                // Action buttons
                if (showActions) ...[
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      if (patient.phoneNumber != null && onCall != null) ...[
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
                      
                      const Spacer(),
                      
                      if (patient.hasEmergencyContact)
                        Icon(
                          Icons.emergency,
                          size: 16,
                          color: Colors.orange,
                        ),
                      
                      if (patient.hasInsurance) ...[
                        const SizedBox(width: 4),
                        Icon(
                          Icons.health_and_safety_outlined,
                          size: 16,
                          color: Colors.blue,
                        ),
                      ],
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
}
