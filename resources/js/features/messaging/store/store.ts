import { defineStore } from "pinia";
import type { Ref } from "vue";
import { computed, ref } from "vue";

import defaults from "@src/store/defaults";
import type {
  IConversation,
  IContactGroup,
  IUser,
  INotification,
  ICall,
  ISettings,
  IEmoji,
} from "@src/types";

const useStore = defineStore("chat", () => {
  // local storage
  const storage = JSON.parse(localStorage.getItem("chat") || "{}");

  // app status refs
  const status = ref("idle");

  // app data refs
  // data refs
  const user: Ref<IUser | undefined> = ref(defaults.user);
  const conversations: Ref<IConversation[]> = ref(defaults.conversations || []);
  const notifications: Ref<INotification[]> = ref(defaults.notifications || []);
  const archivedConversations: Ref<IConversation[]> = ref(
    defaults.archive || []
  );
  const calls: Ref<ICall[]> = ref(defaults.calls || []);
  const settings: Ref<ISettings> = ref(
    storage.settings || defaults.defaultSettings
  );
  const activeCall: Ref<ICall | undefined> = ref(defaults.activeCall);
  const recentEmoji: Ref<IEmoji[]> = ref(storage.recentEmoji || []);
  const emojiSkinTone: Ref<string> = ref(storage.emojiSkinTone || "neutral");

  // ui refs
  const activeSidebarComponent: Ref<string> = ref(
    storage.activeSidebarComponent || "messages"
  );
  const delayLoading = ref(true);
  const activeConversationId: Ref<number | null> = ref(6 || null);
  const conversationOpen: Ref<string | undefined> = ref(
    storage.conversationOpen
  );
  const callMinimized = ref(false);
  const openVoiceCall = ref(false);

  // contacts grouped alphabetically.
  const contactGroups: Ref<IContactGroup[] | undefined> = computed(() => {
    if (user.value) {
      const sortedContacts = [...user.value.contacts];

      sortedContacts.sort();

      const groups: IContactGroup[] = [];
      let currentLetter: string = "";
      const groupNames: string[] = [];

      // create an array of letter for every different sort level.
      for (const contact of sortedContacts) {
        // if the first letter is different create a new group.
        if (contact.firstName[0].toUpperCase() !== currentLetter) {
          currentLetter = contact.firstName[0].toUpperCase();
          groupNames.push(currentLetter);
        }
      }

      // create an array that groups contact names based on the first letter;
      for (const groupName of groupNames) {
        const group: IContactGroup = { letter: groupName, contacts: [] };
        for (const contact of sortedContacts) {
          if (contact.firstName[0].toUpperCase() === groupName) {
            group.contacts.push(contact);
          }
        }
        groups.push(group);
      }

      return groups;
    }
  });

  const getStatus = computed(() => status);

  return {
    // status refs
    status,
    getStatus,

    // data refs
    user,
    conversations,
    contactGroups,
    notifications,
    archivedConversations,
    calls,
    settings,
    activeCall,
    recentEmoji,
    emojiSkinTone,

    // ui refs
    activeSidebarComponent,
    delayLoading,
    activeConversationId,
    conversationOpen,
    callMinimized,
    openVoiceCall,
  };
});

export default useStore;
