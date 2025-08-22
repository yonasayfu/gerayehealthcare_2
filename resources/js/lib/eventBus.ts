import mitt from 'mitt';

type ApplicationEvents = {
  'open-chat': number; // Event to open chat with a specific conversation ID
  'confirm:open': {
    title?: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    __resolve: (result: boolean) => void;
  };
};

const emitter = mitt<ApplicationEvents>();

export const eventBus = emitter;
