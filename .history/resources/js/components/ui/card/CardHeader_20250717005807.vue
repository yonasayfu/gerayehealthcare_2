<template>
  <div class="flex flex-col space-y-1.5 p-6">
    <slot />
  </div>
const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="card-header"
    :class="cn('@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6', props.class)"
  >
    <slot />
  </div>
</template>
