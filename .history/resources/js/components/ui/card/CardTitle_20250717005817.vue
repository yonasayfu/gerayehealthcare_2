<template>
  <h3 class="text-2xl font-semibold leading-none tracking-tight">
    <slot />
  </h3>
const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <h3
    data-slot="card-title"
    :class="cn('leading-none font-semibold', props.class)"
  >
    <slot />
  </h3>
</template>
