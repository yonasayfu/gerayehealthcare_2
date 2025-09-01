import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../domain/entities/user.dart';

enum AvatarSize {
  small,
  medium,
  large,
  extraLarge,
}

class UserAvatar extends StatelessWidget {
  final User? user;
  final String? imageUrl;
  final String? name;
  final AvatarSize size;
  final bool showOnlineStatus;
  final bool isOnline;
  final VoidCallback? onTap;
  final Color? backgroundColor;
  final Color? textColor;

  const UserAvatar({
    super.key,
    this.user,
    this.imageUrl,
    this.name,
    this.size = AvatarSize.medium,
    this.showOnlineStatus = false,
    this.isOnline = false,
    this.onTap,
    this.backgroundColor,
    this.textColor,
  });

  const UserAvatar.small({
    super.key,
    this.user,
    this.imageUrl,
    this.name,
    this.showOnlineStatus = false,
    this.isOnline = false,
    this.onTap,
    this.backgroundColor,
    this.textColor,
  }) : size = AvatarSize.small;

  const UserAvatar.large({
    super.key,
    this.user,
    this.imageUrl,
    this.name,
    this.showOnlineStatus = false,
    this.isOnline = false,
    this.onTap,
    this.backgroundColor,
    this.textColor,
  }) : size = AvatarSize.large;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;

    // Get avatar properties
    final String displayName = user?.name ?? name ?? 'User';
    final String? avatarUrl = user?.profilePhotoUrl ?? imageUrl;
    final String initials = user?.initials ?? _getInitials(displayName);

    // Size configurations
    final double avatarSize;
    final double fontSize;
    final double onlineIndicatorSize;

    switch (size) {
      case AvatarSize.small:
        avatarSize = 32;
        fontSize = 14;
        onlineIndicatorSize = 8;
        break;
      case AvatarSize.medium:
        avatarSize = 48;
        fontSize = 18;
        onlineIndicatorSize = 12;
        break;
      case AvatarSize.large:
        avatarSize = 64;
        fontSize = 24;
        onlineIndicatorSize = 16;
        break;
      case AvatarSize.extraLarge:
        avatarSize = 96;
        fontSize = 36;
        onlineIndicatorSize = 20;
        break;
    }

    Widget avatar = Container(
      width: avatarSize,
      height: avatarSize,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: backgroundColor ?? _getBackgroundColor(displayName, colorScheme),
      ),
      child: avatarUrl != null
          ? ClipOval(
              child: CachedNetworkImage(
                imageUrl: avatarUrl,
                width: avatarSize,
                height: avatarSize,
                fit: BoxFit.cover,
                placeholder: (context, url) => _buildInitialsAvatar(
                  initials,
                  fontSize,
                  textColor ?? _getTextColor(displayName, colorScheme),
                ),
                errorWidget: (context, url, error) => _buildInitialsAvatar(
                  initials,
                  fontSize,
                  textColor ?? _getTextColor(displayName, colorScheme),
                ),
              ),
            )
          : _buildInitialsAvatar(
              initials,
              fontSize,
              textColor ?? _getTextColor(displayName, colorScheme),
            ),
    );

    // Add online status indicator if needed
    if (showOnlineStatus) {
      avatar = Stack(
        children: [
          avatar,
          Positioned(
            right: 0,
            bottom: 0,
            child: Container(
              width: onlineIndicatorSize,
              height: onlineIndicatorSize,
              decoration: BoxDecoration(
                color: isOnline ? Colors.green : Colors.grey,
                shape: BoxShape.circle,
                border: Border.all(
                  color: colorScheme.surface,
                  width: 2,
                ),
              ),
            ),
          ),
        ],
      );
    }

    // Add tap functionality if provided
    if (onTap != null) {
      avatar = GestureDetector(
        onTap: onTap,
        child: avatar,
      );
    }

    return avatar;
  }

  Widget _buildInitialsAvatar(String initials, double fontSize, Color textColor) {
    return Center(
      child: Text(
        initials,
        style: TextStyle(
          fontSize: fontSize,
          fontWeight: FontWeight.w600,
          color: textColor,
        ),
      ),
    );
  }

  String _getInitials(String name) {
    final words = name.trim().split(' ');
    if (words.isEmpty) return 'U';
    
    if (words.length == 1) {
      return words[0].substring(0, 1).toUpperCase();
    } else {
      return (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
    }
  }

  Color _getBackgroundColor(String name, ColorScheme colorScheme) {
    // Generate a consistent color based on the name
    final hash = name.hashCode;
    final colors = [
      colorScheme.primary,
      colorScheme.secondary,
      colorScheme.tertiary,
      Colors.red,
      Colors.green,
      Colors.blue,
      Colors.orange,
      Colors.purple,
      Colors.teal,
      Colors.indigo,
    ];
    
    return colors[hash.abs() % colors.length];
  }

  Color _getTextColor(String name, ColorScheme colorScheme) {
    final backgroundColor = _getBackgroundColor(name, colorScheme);
    // Use white text for dark backgrounds, dark text for light backgrounds
    return backgroundColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;
  }
}

// Avatar group widget for showing multiple users
class AvatarGroup extends StatelessWidget {
  final List<User> users;
  final AvatarSize size;
  final int maxVisible;
  final VoidCallback? onTap;

  const AvatarGroup({
    super.key,
    required this.users,
    this.size = AvatarSize.medium,
    this.maxVisible = 3,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final visibleUsers = users.take(maxVisible).toList();
    final remainingCount = users.length - maxVisible;

    final double avatarSize;
    switch (size) {
      case AvatarSize.small:
        avatarSize = 32;
        break;
      case AvatarSize.medium:
        avatarSize = 48;
        break;
      case AvatarSize.large:
        avatarSize = 64;
        break;
      case AvatarSize.extraLarge:
        avatarSize = 96;
        break;
    }

    return GestureDetector(
      onTap: onTap,
      child: SizedBox(
        width: avatarSize + (visibleUsers.length - 1) * (avatarSize * 0.7),
        height: avatarSize,
        child: Stack(
          children: [
            // Visible user avatars
            ...visibleUsers.asMap().entries.map((entry) {
              final index = entry.key;
              final user = entry.value;
              
              return Positioned(
                left: index * (avatarSize * 0.7),
                child: Container(
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: theme.colorScheme.surface,
                      width: 2,
                    ),
                  ),
                  child: UserAvatar(
                    user: user,
                    size: size,
                  ),
                ),
              );
            }),
            
            // Remaining count indicator
            if (remainingCount > 0)
              Positioned(
                left: visibleUsers.length * (avatarSize * 0.7),
                child: Container(
                  width: avatarSize,
                  height: avatarSize,
                  decoration: BoxDecoration(
                    color: theme.colorScheme.surfaceVariant,
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: theme.colorScheme.surface,
                      width: 2,
                    ),
                  ),
                  child: Center(
                    child: Text(
                      '+$remainingCount',
                      style: theme.textTheme.labelMedium?.copyWith(
                        fontWeight: FontWeight.w600,
                        color: theme.colorScheme.onSurfaceVariant,
                      ),
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
