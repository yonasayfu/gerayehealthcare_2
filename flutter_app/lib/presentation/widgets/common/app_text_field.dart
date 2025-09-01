import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

enum AppTextFieldType {
  text,
  email,
  password,
  phone,
  number,
  multiline,
}

class AppTextField extends StatefulWidget {
  final String? label;
  final String? hint;
  final String? helperText;
  final String? errorText;
  final TextEditingController? controller;
  final AppTextFieldType type;
  final bool isRequired;
  final bool isEnabled;
  final int? maxLines;
  final int? maxLength;
  final Widget? prefixIcon;
  final Widget? suffixIcon;
  final VoidCallback? onTap;
  final ValueChanged<String>? onChanged;
  final ValueChanged<String>? onSubmitted;
  final String? Function(String?)? validator;
  final List<TextInputFormatter>? inputFormatters;
  final TextInputAction? textInputAction;
  final FocusNode? focusNode;
  final bool autofocus;

  const AppTextField({
    super.key,
    this.label,
    this.hint,
    this.helperText,
    this.errorText,
    this.controller,
    this.type = AppTextFieldType.text,
    this.isRequired = false,
    this.isEnabled = true,
    this.maxLines,
    this.maxLength,
    this.prefixIcon,
    this.suffixIcon,
    this.onTap,
    this.onChanged,
    this.onSubmitted,
    this.validator,
    this.inputFormatters,
    this.textInputAction,
    this.focusNode,
    this.autofocus = false,
  });

  const AppTextField.email({
    super.key,
    this.label,
    this.hint,
    this.helperText,
    this.errorText,
    this.controller,
    this.isRequired = false,
    this.isEnabled = true,
    this.prefixIcon,
    this.suffixIcon,
    this.onTap,
    this.onChanged,
    this.onSubmitted,
    this.validator,
    this.inputFormatters,
    this.textInputAction,
    this.focusNode,
    this.autofocus = false,
  }) : type = AppTextFieldType.email,
       maxLines = 1,
       maxLength = null;

  const AppTextField.password({
    super.key,
    this.label,
    this.hint,
    this.helperText,
    this.errorText,
    this.controller,
    this.isRequired = false,
    this.isEnabled = true,
    this.prefixIcon,
    this.suffixIcon,
    this.onTap,
    this.onChanged,
    this.onSubmitted,
    this.validator,
    this.inputFormatters,
    this.textInputAction,
    this.focusNode,
    this.autofocus = false,
  }) : type = AppTextFieldType.password,
       maxLines = 1,
       maxLength = null;

  const AppTextField.phone({
    super.key,
    this.label,
    this.hint,
    this.helperText,
    this.errorText,
    this.controller,
    this.isRequired = false,
    this.isEnabled = true,
    this.prefixIcon,
    this.suffixIcon,
    this.onTap,
    this.onChanged,
    this.onSubmitted,
    this.validator,
    this.inputFormatters,
    this.textInputAction,
    this.focusNode,
    this.autofocus = false,
  }) : type = AppTextFieldType.phone,
       maxLines = 1,
       maxLength = null;

  const AppTextField.multiline({
    super.key,
    this.label,
    this.hint,
    this.helperText,
    this.errorText,
    this.controller,
    this.isRequired = false,
    this.isEnabled = true,
    this.maxLines = 4,
    this.maxLength,
    this.prefixIcon,
    this.suffixIcon,
    this.onTap,
    this.onChanged,
    this.onSubmitted,
    this.validator,
    this.inputFormatters,
    this.textInputAction,
    this.focusNode,
    this.autofocus = false,
  }) : type = AppTextFieldType.multiline;

  @override
  State<AppTextField> createState() => _AppTextFieldState();
}

class _AppTextFieldState extends State<AppTextField> {
  bool _obscureText = true;
  late FocusNode _focusNode;

  @override
  void initState() {
    super.initState();
    _focusNode = widget.focusNode ?? FocusNode();
  }

  @override
  void dispose() {
    if (widget.focusNode == null) {
      _focusNode.dispose();
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    
    // Determine keyboard type
    TextInputType keyboardType;
    switch (widget.type) {
      case AppTextFieldType.email:
        keyboardType = TextInputType.emailAddress;
        break;
      case AppTextFieldType.phone:
        keyboardType = TextInputType.phone;
        break;
      case AppTextFieldType.number:
        keyboardType = TextInputType.number;
        break;
      case AppTextFieldType.multiline:
        keyboardType = TextInputType.multiline;
        break;
      default:
        keyboardType = TextInputType.text;
    }

    // Determine input formatters
    List<TextInputFormatter>? formatters = widget.inputFormatters;
    if (widget.type == AppTextFieldType.phone && formatters == null) {
      formatters = [FilteringTextInputFormatter.digitsOnly];
    }

    // Build prefix icon
    Widget? prefixIcon = widget.prefixIcon;
    if (prefixIcon == null) {
      switch (widget.type) {
        case AppTextFieldType.email:
          prefixIcon = const Icon(Icons.email_outlined);
          break;
        case AppTextFieldType.password:
          prefixIcon = const Icon(Icons.lock_outlined);
          break;
        case AppTextFieldType.phone:
          prefixIcon = const Icon(Icons.phone_outlined);
          break;
        default:
          break;
      }
    }

    // Build suffix icon
    Widget? suffixIcon = widget.suffixIcon;
    if (widget.type == AppTextFieldType.password && suffixIcon == null) {
      suffixIcon = IconButton(
        icon: Icon(_obscureText ? Icons.visibility : Icons.visibility_off),
        onPressed: () {
          setState(() {
            _obscureText = !_obscureText;
          });
        },
      );
    }

    // Build label with required indicator
    String? label = widget.label;
    if (widget.isRequired && label != null) {
      label = '$label *';
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextFormField(
          controller: widget.controller,
          focusNode: _focusNode,
          enabled: widget.isEnabled,
          autofocus: widget.autofocus,
          obscureText: widget.type == AppTextFieldType.password ? _obscureText : false,
          keyboardType: keyboardType,
          textInputAction: widget.textInputAction,
          maxLines: widget.type == AppTextFieldType.password ? 1 : widget.maxLines,
          maxLength: widget.maxLength,
          inputFormatters: formatters,
          validator: widget.validator,
          onTap: widget.onTap,
          onChanged: widget.onChanged,
          onFieldSubmitted: widget.onSubmitted,
          decoration: InputDecoration(
            labelText: label,
            hintText: widget.hint,
            helperText: widget.helperText,
            errorText: widget.errorText,
            prefixIcon: prefixIcon,
            suffixIcon: suffixIcon,
            border: const OutlineInputBorder(),
            enabledBorder: OutlineInputBorder(
              borderSide: BorderSide(color: theme.colorScheme.outline),
              borderRadius: BorderRadius.circular(8),
            ),
            focusedBorder: OutlineInputBorder(
              borderSide: BorderSide(color: theme.colorScheme.primary, width: 2),
              borderRadius: BorderRadius.circular(8),
            ),
            errorBorder: OutlineInputBorder(
              borderSide: BorderSide(color: theme.colorScheme.error),
              borderRadius: BorderRadius.circular(8),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderSide: BorderSide(color: theme.colorScheme.error, width: 2),
              borderRadius: BorderRadius.circular(8),
            ),
            disabledBorder: OutlineInputBorder(
              borderSide: BorderSide(color: theme.colorScheme.outline.withOpacity(0.5)),
              borderRadius: BorderRadius.circular(8),
            ),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          ),
        ),
      ],
    );
  }
}
