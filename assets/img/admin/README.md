# Admin Icons Setup

## Icon Placeholder System

The admin interface now uses image placeholders instead of FontAwesome icons. 

### Current Implementation

Each icon currently shows as a placeholder with:
- Light orange background (`rgba(253, 127, 68, 0.2)`)
- Orange border matching your accent color
- Small orange dot in the center

### To Replace with Real Icons

1. **Add your icon images** to `/assets/img/admin/` directory
2. **Update the CSS** in `/admin/styles.css` 

### Required Icons

You need 6 icon images (recommended size: 20x20px, PNG format):

- `dashboard.png` - For main dashboard
- `stats.png` - For database statistics  
- `spawn.png` - For spawn management
- `drops.png` - For drop management
- `monsters.png` - For monster management
- `beginner.png` - For beginner items

### CSS Updates Needed

In `/admin/styles.css`, uncomment and update these lines:

```css
.admin-icon-dashboard {
    background-color: rgba(253, 127, 68, 0.2);
    background-image: url('../../assets/img/admin/dashboard.png');
}

.admin-icon-stats {
    background-color: rgba(253, 127, 68, 0.2);
    background-image: url('../../assets/img/admin/stats.png');
}

.admin-icon-spawn {
    background-color: rgba(253, 127, 68, 0.2);
    background-image: url('../../assets/img/admin/spawn.png');
}

.admin-icon-drops {
    background-color: rgba(253, 127, 68, 0.2);
    background-image: url('../../assets/img/admin/drops.png');
}

.admin-icon-monsters {
    background-color: rgba(253, 127, 68, 0.2);
    background-image: url('../../assets/img/admin/monsters.png');
}

.admin-icon-beginner {
    background-color: rgba(253, 127, 68, 0.2);
    background-image: url('../../assets/img/admin/beginner.png');
}
```

### Icon Style Guidelines

- **Size**: 20x20 pixels
- **Format**: PNG with transparency
- **Style**: Should work on dark backgrounds  
- **Color**: Consider white/light colors for visibility
- **Theme**: Match your game's UI style

### Fallback

If no images are provided, the system will show the placeholder design with the orange dot.
