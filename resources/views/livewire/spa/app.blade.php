<div>
    @if($currentView === 'home')
        @livewire('spa.home', key('home'))
    @elseif($currentView === 'products.index')
        @livewire('spa.products.index', key('products-index'))
    @elseif($currentView === 'products.show' && isset($routeParams['slug']))
        @livewire('spa.products.show', ['slug' => $routeParams['slug']], key('product-' . $routeParams['slug']))
    @elseif($currentView === 'categories.show' && isset($routeParams['slug']))
        @livewire('spa.categories.show', ['slug' => $routeParams['slug']], key('category-' . $routeParams['slug']))
    @elseif($currentView === 'cart.index')
        @livewire('spa.cart.index', key('cart'))
    @elseif($currentView === 'wishlist.index')
        @livewire('spa.wishlist.index', key('wishlist'))
    @elseif($currentView === 'orders.index')
        @livewire('spa.orders.index', key('orders'))
    @else
        @livewire('spa.home', key('fallback-home'))
    @endif

    <script>
        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(event) {
            // Get the current path
            const path = window.location.pathname;

            // Map path to view
            let view = 'home';
            let params = {};

            if (path === '/') {
                view = 'home';
            } else if (path === '/products') {
                view = 'products.index';
            } else if (path.startsWith('/products/')) {
                const slug = path.replace('/products/', '');
                view = 'products.show';
                params = { slug: slug };
            } else if (path.startsWith('/category/')) {
                const slug = path.replace('/category/', '');
                view = 'categories.show';
                params = { slug: slug };
            } else if (path === '/cart') {
                view = 'cart.index';
            } else if (path === '/wishlist') {
                view = 'wishlist.index';
            } else if (path === '/orders') {
                view = 'orders.index';
            }

            // Call the Livewire method
            if (window.livewire_find) {
                const component = window.livewire_find(c => c.name === 'spa.app');
                if (component) {
                    component.call('handleNavigateTo', view, params);
                }
            }
        });
    </script>
</div>
