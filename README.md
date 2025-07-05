# MTG Trader

MTG Trader is a modern web application for Magic: The Gathering players in Namibia to find, buy, and sell cards locally. Built with Laravel and Tailwind CSS, it provides a user-friendly dashboard for managing your collection, as well as a public-facing card search and seller discovery platform.

## Features

- **Card Dashboard:**
  - Add, edit, and delete cards in your collection
  - Bulk import cards (Moxfield compatible)
  - Attribute support: Foil, Borderless, Retro Frame, Etched Foil, Judge Promo Foil, Japanese Language, Signed by Artist, and Private
  - Client-side search and filtering
  - Modern, responsive UI with dark mode

- **Public Card Search:**
  - Browse and search all public cards
  - Advanced search by card attributes
  - See all sellers for a card, with contact info
  - Clickable rows for quick card detail access

- **Card Detail Pages:**
  - View all sellers for a specific card
  - See card attributes and seller contact info

- **Authentication:**
  - Register and log in to manage your own cards
  - Private cards are hidden from public search

## Tech Stack
- Laravel (PHP)
- Tailwind CSS (via Vite)
- Blade templates
- SQLite (default, can be changed)
- AJAX for dynamic search

## Getting Started
1. Clone the repository
2. Install dependencies with `composer install` and `npm install`
3. Copy `.env.example` to `.env` and set up your environment variables
4. Run migrations: `php artisan migrate`
5. Build assets: `npm run build`
6. Start the server: `php artisan serve`

## Contributing
Pull requests and suggestions are welcome!

## License
MIT
