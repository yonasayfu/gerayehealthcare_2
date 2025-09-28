Messaging Deep Dive — Working Notes (clean start)

Source of truth: MESSAGING_SYSTEM_DOCUMENTATION.md

What I verified and fixed

- Backend reactions: Direct messages use MessageReaction; group messages use polymorphic Reaction. Removed all Message->reactions usage. Events now eager-load messageReactions.
- Missing group routes: Added update/destroy/download attachment routes for group messages.
  - routes/web.php: groups.messages.update, groups.messages.destroy, groups.messages.attachment
- Inertia page wired: resources/js/pages/Admin/Messages/Index.vue is the main inbox UI used by messages.inbox.

UI improvements shipped now

- Channel editing: Enable edit for own channel messages. Uses groups.messages.update.
- Channel deletion: Enable delete for own channel messages. Uses groups.messages.destroy.
- Pin/unpin (both): Implemented for direct and channel messages. Uses messages.pin/unpin and groups.messages.pin/unpin.
- Attachment download (both): Already wired; confirmed route mapping and usage.
- Chat modal (glass + dark mode): Converted the messaging modal to use the existing liquid glass styles, fixed dark mode contrast, made People/Groups tabs clearly visible, and ensured avatars display on the sidebar.

How to test quickly

1) Load Inbox: GET /messages/inbox
2) Select a direct conversation and a channel; send a message in each.
3) Edit/delete: Use the 3-dot menu on your own message in both contexts.
4) Pin/unpin: Use the same menu; a pinned banner appears above the thread.
5) Attachment: Send a file; use Download in the bubble.

If anything looks stale, clear caches:
- php artisan optimize:clear
- php artisan route:clear
- npm run dev (or build) and hard refresh

Open follow-ups (prioritized)

- Reactions UI: Add per-message emoji reaction picker and counts (direct + group).
- Typing indicators: Wire to existing messages.typing endpoints on this page (works today in ChatModal).
- Read receipts: Visual indicator for direct messages (single/double check).
- Presence: Online dot on conversation list using Echo presence channels.
- Bulk actions: Select and bulk delete/export direct threads.
- Accessibility polish: Contrast, focus rings, reduced motion.

Quick reference files

- routes/web.php: messaging + group message routes
- resources/js/pages/Admin/Messages/Index.vue: inbox UI
- resources/js/components/ChatModal.vue: modal UI (now glass-styled; fixed fetch + dark mode)
- app/Services/Messaging/TelegramInboxService.php: data shaping
- app/Http/Controllers/MessageController.php: direct message API
- app/Http/Controllers/GroupMessageController.php: channel message API

Notes

- The ChatModal.vue still offers a compact widget; the full-page Inbox (Index.vue) is now feature-complete for edit/delete/pin.
- Permissions: Channel edit/delete shown for own messages only; admins/owners can delete via API, UI can be extended to surface role checks if needed.

