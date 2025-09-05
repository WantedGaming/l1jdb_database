# L1j-R Database Website

A modern, card-based MMORPG database website built with PHP and CSS.

## Features
- Modern dark theme design
- Responsive card-based layout
- 6 main categories: Weapons, Armor, Items, Monsters, Maps, Dolls
- Search functionality
- Pagination support
- Admin panel (basic structure)

## Directory Structure
```
/
├── index.php                 # Main homepage with hero section and category cards
├── admin/                    # Admin panel (basic structure)
│   └── index.php
├── assets/                   # Static assets
│   ├── css/
│   │   └── style.css        # Main stylesheet with modern design
│   ├── js/
│   │   └── main.js          # JavaScript functionality
│   └── img/                 # Images and placeholders
├── includes/
│   └── functions.php        # Core PHP functions and database helpers
├── categories/              # Category-specific pages
│   ├── weapons/
│   │   ├── weapon-list.php
│   │   └── weapon-detail.php
│   ├── armor/
│   │   ├── armor-list.php
│   │   └── armor-detail.php
│   ├── items/
│   │   ├── item-list.php
│   │   └── item-detail.php
│   ├── monsters/
│   │   ├── monster-list.php
│   │   └── monster-detail.php
│   ├── maps/
│   │   ├── map-list.php
│   │   └── map-detail.php
│   └── dolls/
│       ├── doll-list.php
│       └── doll-detail.php
└── sql_structure/           # Database structure files
```

## Color Scheme
- Text: #ffffff
- Background: #0f0f0f
- Primary: #171718
- Secondary: #1c1c1c
- Accent: #fd7f44

## Setup Instructions
1. Configure database connection in `includes/functions.php`
2. Update database credentials:
   - Host: localhost (default)
   - Database: l1j_database (default)
   - Username: root (default)
   - Password: (empty by default)
3. Create your database tables as needed
4. Place placeholder images in `/assets/img/placeholders/`
5. Upload to your web server

## Database Functions
The `includes/functions.php` file provides helper functions for:
- Database connections
- Data retrieval
- Pagination
- Search functionality
- HTML generation
- Security (input sanitization)

## Responsive Design
The website is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## Browser Support
- Modern browsers with CSS Grid and Flexbox support
- Chrome, Firefox, Safari, Edge

## Notes
- All category pages follow the same structure for consistency
- PHP includes are used for maintainable code
- CSS is organized with modern best practices
- JavaScript provides enhanced user experience
- Ready for database integration with your L1j server data