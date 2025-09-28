Search & Pagination Patterns

Web Lists (Inertia)
- Use useTableFilters composable to keep `search`, `sort`, `direction`, `per_page` in the URL.
- Server returns paginated data; frontend renders Pagination component using server links.

Global Search
- GlobalSearch.vue is a modal overlay for quick navigation; not tied to URL.

API
- For mobile, apply query params on endpoints; respond with paginated Resources.
