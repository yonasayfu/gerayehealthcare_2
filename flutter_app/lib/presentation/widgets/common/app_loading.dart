import 'package:flutter/material.dart';

enum LoadingType {
  circular,
  linear,
  dots,
  pulse,
}

class AppLoading extends StatefulWidget {
  final LoadingType type;
  final double? size;
  final Color? color;
  final String? message;
  final bool overlay;

  const AppLoading({
    super.key,
    this.type = LoadingType.circular,
    this.size,
    this.color,
    this.message,
    this.overlay = false,
  });

  const AppLoading.circular({
    super.key,
    this.size,
    this.color,
    this.message,
    this.overlay = false,
  }) : type = LoadingType.circular;

  const AppLoading.linear({
    super.key,
    this.color,
    this.message,
    this.overlay = false,
  }) : type = LoadingType.linear,
       size = null;

  const AppLoading.dots({
    super.key,
    this.size,
    this.color,
    this.message,
    this.overlay = false,
  }) : type = LoadingType.dots;

  const AppLoading.overlay({
    super.key,
    this.type = LoadingType.circular,
    this.size,
    this.color,
    this.message,
  }) : overlay = true;

  @override
  State<AppLoading> createState() => _AppLoadingState();
}

class _AppLoadingState extends State<AppLoading>
    with TickerProviderStateMixin {
  late AnimationController _controller;
  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(seconds: 2),
      vsync: this,
    )..repeat();
    
    _pulseController = AnimationController(
      duration: const Duration(milliseconds: 1000),
      vsync: this,
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _controller.dispose();
    _pulseController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final color = widget.color ?? theme.colorScheme.primary;
    final size = widget.size ?? 40.0;

    Widget loadingWidget;
    switch (widget.type) {
      case LoadingType.circular:
        loadingWidget = SizedBox(
          width: size,
          height: size,
          child: CircularProgressIndicator(
            valueColor: AlwaysStoppedAnimation<Color>(color),
            strokeWidth: 3,
          ),
        );
        break;

      case LoadingType.linear:
        loadingWidget = LinearProgressIndicator(
          valueColor: AlwaysStoppedAnimation<Color>(color),
          backgroundColor: color.withOpacity(0.2),
        );
        break;

      case LoadingType.dots:
        loadingWidget = _buildDotsLoading(color, size);
        break;

      case LoadingType.pulse:
        loadingWidget = _buildPulseLoading(color, size);
        break;
    }

    Widget content = Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        loadingWidget,
        if (widget.message != null) ...[
          const SizedBox(height: 16),
          Text(
            widget.message!,
            style: theme.textTheme.bodyMedium?.copyWith(
              color: widget.overlay ? Colors.white : null,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ],
    );

    if (widget.overlay) {
      return Container(
        color: Colors.black54,
        child: Center(child: content),
      );
    }

    return content;
  }

  Widget _buildDotsLoading(Color color, double size) {
    return SizedBox(
      width: size * 2,
      height: size / 2,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: List.generate(3, (index) {
          return AnimatedBuilder(
            animation: _controller,
            builder: (context, child) {
              final delay = index * 0.2;
              final progress = (_controller.value + delay) % 1.0;
              final scale = 0.5 + (0.5 * (1 - (progress - 0.5).abs() * 2).clamp(0.0, 1.0));
              
              return Transform.scale(
                scale: scale,
                child: Container(
                  width: size / 6,
                  height: size / 6,
                  decoration: BoxDecoration(
                    color: color,
                    shape: BoxShape.circle,
                  ),
                ),
              );
            },
          );
        }),
      ),
    );
  }

  Widget _buildPulseLoading(Color color, double size) {
    return AnimatedBuilder(
      animation: _pulseController,
      builder: (context, child) {
        return Transform.scale(
          scale: 0.8 + (_pulseController.value * 0.4),
          child: Container(
            width: size,
            height: size,
            decoration: BoxDecoration(
              color: color.withOpacity(0.8 - (_pulseController.value * 0.3)),
              shape: BoxShape.circle,
            ),
          ),
        );
      },
    );
  }
}

// Loading overlay helper
class LoadingOverlay {
  static OverlayEntry? _overlayEntry;

  static void show(
    BuildContext context, {
    String? message,
    LoadingType type = LoadingType.circular,
  }) {
    hide(); // Remove any existing overlay
    
    _overlayEntry = OverlayEntry(
      builder: (context) => AppLoading.overlay(
        type: type,
        message: message,
      ),
    );
    
    Overlay.of(context).insert(_overlayEntry!);
  }

  static void hide() {
    _overlayEntry?.remove();
    _overlayEntry = null;
  }
}

// Loading dialog helper
class LoadingDialog {
  static void show(
    BuildContext context, {
    String? message,
    LoadingType type = LoadingType.circular,
    bool barrierDismissible = false,
  }) {
    showDialog(
      context: context,
      barrierDismissible: barrierDismissible,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        elevation: 0,
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Theme.of(context).colorScheme.surface,
            borderRadius: BorderRadius.circular(12),
          ),
          child: AppLoading(
            type: type,
            message: message,
          ),
        ),
      ),
    );
  }

  static void hide(BuildContext context) {
    Navigator.of(context).pop();
  }
}
