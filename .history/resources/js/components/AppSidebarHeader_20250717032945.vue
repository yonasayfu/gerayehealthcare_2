</script>

<template>
    <header class="bg-background/50 flex h-14 items-center gap-3 px-4 backdrop-blur-xl lg:h-[60px]">
        <Button variant="ghost" size="icon" class="size-9 flex md:hidden lg:flex">
            <PanelLeft />
        </Button>
        <Button variant="ghost" size="icon" class="size-9" @click="$inertia.visit(route('settings.profile'))">
            <Settings />
        </Button>
        <ThemeToggler />
    </header>
</template>
