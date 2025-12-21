<div
     @if (filament()->isSidebarCollapsibleOnDesktop())
        x-bind:class="$store.sidebar.isOpen ? 'block' : 'hidden'"
    @endif
>
    <x-filament::input.wrapper
        class="relative"
        :inline-prefix="true"
        prefix-icon="tabler-brand-finder"
    >
        <x-filament::input
            type="text"
            placeholder="Buscar ..."
            x-data="sidebarSearch()"
            x-ref="search"
            x-on:input.debounce.300ms="filterItems($event.target.value)"
            x-on:keydown.escape="clearSearch"
            x-on:keydown.meta.j.prevent.document="$refs.search.focus()"
        />

        <kbd class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-100 border border-gray-300 text-gray-400 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-50 text-xs px-1.5 flex items-center justify-center gap-1 py-1 rounded-md">
        </kbd>
    </x-filament::input.wrapper>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebarSearch', () => ({
                init() {
                    this.$refs.search.value = ''
                },

                filterItems(searchTerm) {
                    const groups = document.querySelectorAll('.fi-sidebar-nav-groups .fi-sidebar-group')
                    searchTerm = searchTerm.toLowerCase()

                    groups.forEach(group => {
                        const groupButton = group.querySelector('.fi-sidebar-group-button')
                        const groupText = groupButton?.textContent.toLowerCase() || ''
                        const items = group.querySelectorAll('.fi-sidebar-item')
                        let hasVisibleItems = false

                        const groupMatches = groupText.includes(searchTerm)

                        items.forEach(item => {
                            const itemText = item.textContent.toLowerCase()
                            const isVisible = itemText.includes(searchTerm) || groupMatches

                            item.style.display = isVisible ? '' : 'none'
                            if (isVisible) hasVisibleItems = true
                        })

                        group.style.display = (hasVisibleItems || groupMatches) ? '' : 'none'
                    })
                },

                clearSearch() {
                    this.$refs.search.value = ''
                    this.filterItems('')
                }
            }))
        })
    </script>
</div>