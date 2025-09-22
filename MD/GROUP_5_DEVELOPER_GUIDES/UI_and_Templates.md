
# UI and Templates Guide: Geraye Health Care

This document provides a guide for implementing the "liquid glass" UI theme across the Geraye Health Care project.

## Overview

The "liquid glass" theme provides a modern, visually appealing, and consistent user interface. It is characterized by the use of blurred backgrounds, gradients, and subtle shadows to create a sense of depth and transparency.

## Implementation

To implement the "liquid glass" theme in a module, follow these steps:

1.  **Add Global CSS:** Add the necessary CSS rules for the `liquidGlass-wrapper`, `btn-glass`, and `search-glass` classes to the `resources/css/app.css` file.
2.  **Update Header Toolbar:** Replace the existing header toolbar with the `liquidGlass-wrapper` component.
3.  **Update Footer Actions:** Use the `btn-glass` class for the footer action buttons.
4.  **Update Form Selects:** Use the dark-friendly classes for the form select controls.
5.  **Update Search Input:** Wrap the search input with the `search-glass` class to add the liquid glass effect.
6.  **Update Table Action Buttons:** Use the `btn-glass-sm` class for the table action buttons.
7.  **Add Print Behavior:** Use the `print:hidden` class to hide interactive elements when printing.

## Reusable Components

To avoid repeating markup, it is recommended to create reusable Vue components for the `GlassCard` and `GlassButton` elements.

## Show.vue JavaScript Error Fixes

Many `Show.vue` files have JavaScript errors that need to be fixed. The most common errors are:

*   **Wrong Variable References:** Using the wrong variable to access data.
*   **Non-existent Functions:** Calling functions that do not exist.
*   **Wrong Route Parameters:** Passing the wrong parameters to routes.

To fix these errors, you need to:

1.  **Identify the correct variable name:** Look at the `defineProps` function to find the correct variable name.
2.  **Replace the incorrect references:** Replace the incorrect variable references with the correct ones.
3.  **Test the page:** Test the page to make sure that the errors are gone.

This document provides a high-level overview of the UI and templates guide. For more detailed information, please refer to the source code and other documentation in this repository.
