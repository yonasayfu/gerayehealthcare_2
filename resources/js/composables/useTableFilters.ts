import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import { router } from '@inertiajs/vue3'

type Direction = 'asc' | 'desc'

export interface TableFilterOptions {
  routeName: string
  initial?: {
    search?: string
    sort?: string
    direction?: Direction
    per_page?: number
  }
}

export function useTableFilters(opts: TableFilterOptions) {
  const search = ref(opts.initial?.search ?? '')
  const sort = ref(opts.initial?.sort ?? '')
  const direction = ref<Direction>(opts.initial?.direction ?? 'asc')
  const perPage = ref<number>(opts.initial?.per_page ?? 10)

  const apply = debounce(() => {
    const params: Record<string, string | number> = {
      search: search.value,
      direction: direction.value,
      per_page: perPage.value,
    }
    if (sort.value) params.sort = sort.value
    router.get(route(opts.routeName), params, { preserveState: true, replace: true })
  }, 400)

  watch([search, sort, direction, perPage], apply)

  function toggleSort(field: string) {
    if (sort.value === field) {
      direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
      sort.value = field
      direction.value = 'asc'
    }
  }

  return { search, sort, direction, perPage, apply, toggleSort }
}

