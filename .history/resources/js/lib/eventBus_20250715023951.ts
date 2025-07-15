import mitt from 'mitt';

type ApplicationEvents = {
  'open-chat': number; // Event to open chat with a specific conversation ID
};

const emitter = mitt<ApplicationEvents>();

export const eventBus = emitter;
