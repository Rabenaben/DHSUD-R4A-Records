# TODO: Move Card Design from Controller to View

- [ ] Modify DisplayController.php index() function to calculate and pass combined counts ($totalDockets, $onShelf, $unavailable, $borrowed) instead of $cards array.
- [ ] Update dashboard.blade.php to use <x-status-cards> component for rendering the cards, removing the inline @foreach loop.
